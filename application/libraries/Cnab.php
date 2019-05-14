<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
require_once APPPATH.'libraries/CnabPHP/vendor/autoload.php';

class Cnab{
	const   OCORRENCIA_REMESSA = "1";
	const   OCORRENCIA_BAIXA = "2";
	const   OCORRENCIA_ALT_VENCIMENTO = "6";
	const   OCORRENCIA_ALT_OUTROS_DADOS = "31";
	public function lerRetorno($caminho_arquivo){
		$cnabFactory = new Cnab\Factory();
		$arquivo = $cnabFactory->createRetorno($caminho_arquivo);
		return $arquivo;		
	}

	public function gerarRemessaItau($boletos,$conta,$pm){
		$codigo_banco = Cnab\Banco::ITAU;
		$empresa = ($conta->getIdAdministradora()!='' ? $conta->getIdAdministradora() : $conta->getIdCondominio());
		$arquivo = new Cnab\Remessa\Cnab400\Arquivo($codigo_banco);
		$arquivo->configure(array(
		    'data_geracao'  => new DateTime(),
		    'data_gravacao' => new DateTime(), 
		    'nome_fantasia' => $this->limparTexto($empresa->getNome()), // seu nome de empresa
		    'razao_social'  => $this->limparTexto($empresa->getIdPessoaJuridica()->getRazaoSocial()),  // sua razão social
		    'cnpj'          => $empresa->getIdPessoaJuridica()->getCnpj(), // seu cnpj completo
		    'banco'         => $codigo_banco, //código do banco
		    'logradouro'    => $this->limparTexto($empresa->getLogradouro()),
		    'numero'        => $empresa->getNumero(),
		    'bairro'        => $this->limparTexto($empresa->getBairro()), 
		    'cidade'        => $this->limparTexto($empresa->getIdMunicipio()->getDescricao()),
		    'uf'            => $empresa->getIdMunicipio()->getIdEstado()->getUf(),
		    'cep'           => $empresa->getCep()=='' ? 0 : $this->limparTexto(str_replace('.','',str_replace('-','',$empresa->getCep()))), // sem hífem str_replace('.','',str_replace('-','',$empresa->getCep())),
		    'agencia'       => $conta->getAgencia(), 
		    'conta'         => $conta->getNumeroSemDv(), // número da conta
		    'conta_dac'     => $conta->getDvConta(), // digito da conta
		));

		// você pode adicionar vários boletos em uma remessa
		foreach ($boletos as $bls){
			$boleto = $bls['boleto'];
			$operacao = $bls['operacao'];
			$boleto->atualizarProprietario();
			$condominio = $boleto->getIdUnidade()->getIdCondominio();
			$painel = $pm->getPainel($condominio);
			$cobranca = $boleto->getCobrancaPrincipal();
			$data_desconto = "";
			$data_desconto_2 = "";
			$valor_desconto = 0;
			$valor_desconto_2 = 0;
			
			$cobrancas_unidade = $boleto->getCobrancas();

			if($cobranca->getIdLancamentoDesconto()!=''){
				$data_desconto = $cobranca->getIdLancamentoDesconto()->getDataVencimento();			
			}

			$diff_dias_desconto = dateDiff2($data_desconto,proximoDiaUtil($boleto->getDataVencimentoBD()));//usada a funcao de proximoDiaUtil - pq o desconto pode cair num dia nao util
			$diff_dias_hoje = dateDiff2(date("Y-m-d"),$data_desconto);//se o desconto ja tiver passado nao envia informacao de desconto
			if($diff_dias_hoje>0 && $diff_dias_desconto>=0 && $diff_dias_desconto<=20){//verificando se o desconto ta dentro do mês, para nao enviar detalhe de desconto

				if(count($cobrancas_unidade)>0){
					foreach ($cobrancas_unidade as $cob) {
						if($cob->getIdLancamentoDesconto()!=''){							
							$valor_desconto += abs($cob->getIdLancamentoDesconto()->getValorPrevisto());
						}
					}
				}		

			}else{
				$data_desconto = '';
			}


			if($cobranca->getIdLancamentoDesconto2()!=''){
				$data_desconto_2 = $cobranca->getIdLancamentoDesconto2()->getDataVencimento();			
			}
			$diff_dias_desconto = dateDiff2($data_desconto_2,proximoDiaUtil($boleto->getDataVencimentoBD()));//usada a funcao de proximoDiaUtil - pq o desconto pode cair num dia nao util
			$diff_dias_hoje = dateDiff2(date("Y-m-d"),$data_desconto_2);//se o desconto ja tiver passado nao envia informacao de desconto
			if($diff_dias_hoje>0 && $diff_dias_desconto>=0 && $diff_dias_desconto<=20){//verificando se o desconto ta dentro do mês, para nao enviar detalhe de desconto

				if(count($cobrancas_unidade)>0){
					foreach ($cobrancas_unidade as $cob) {
						if($cob->getIdLancamentoDesconto2()!=''){							
							$valor_desconto_2 += abs($cob->getIdLancamentoDesconto2()->getValorPrevisto());
						}
					}
				}		

			}else{
				$data_desconto_2 = '';
			}

			$remOperacional = $painel->getIdRemuneracaoOperacional();
			$valorJuros = $cobranca->calcularJuros($remOperacional->getPercentual_Juros(), $remOperacional->getTipoPeriodicidade_Juros(), $boleto->getValorCobrado(), 1);
			$valorJuros = number_format($valorJuros,2,'.','');
			if($operacao==self::OCORRENCIA_REMESSA){
				$arquivo->insertDetalhe(array(
				    'codigo_ocorrencia' => self::OCORRENCIA_REMESSA,
				    'nosso_numero'      => (int)$boleto->getNossoNumero2(),
				    'numero_documento'  => (int)$boleto->getNoDocumento(),
				    'carteira'          => '109',
				    'especie'           => Cnab\Especie::ITAU_DUPLICATA_DE_SERVICO, // Você pode consultar as especies Cnab\Especie
				    'valor'             => $boleto->getValorDevido(), // Valor do boleto
				    'instrucao1'        => 3, // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
				    'instrucao2'        => 0, // preenchido com zeros
				    'sacado_nome'       => $this->limparTexto($boleto->getNomeSacado()), // O Sacado é o cliente, preste atenção nos campos abaixo
				    'sacado_tipo'       => (strlen($boleto->getCpfSacado())>14 ? 'cnpj' : 'cpf'), //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
				    'sacado_cpf'        => $boleto->getCpfSacado(),
				    'sacado_logradouro' => $this->limparTexto($boleto->getEnderecoSacado()),
				    'sacado_bairro'     => $this->limparTexto($boleto->getBairroSacado()),
				    'sacado_cep'        => $boleto->getCepSacado()=='' ? 0 : $this->limparTexto(str_replace('.','',str_replace('-','',$boleto->getCepSacado()))), // sem hífem
				    'sacado_cidade'     => $boleto->getCidadeSacado(),
				    'sacado_uf'         => $boleto->getUfSacado(),
				    'data_vencimento'   => new DateTime($boleto->getDataVencimentoBD()),
				    'data_cadastro'     => new DateTime(data2BD($boleto->getDataProcessamento())),
				    'juros_de_um_dia'     => $valorJuros,//$painel->getIdRemuneracaoOperacional()->getPercentual_Juros(), // Valor do juros de 1 dia'
				    'data_desconto'       => $data_desconto!='' ? new DateTime($data_desconto) : "000000",
				    'valor_desconto'      => $valor_desconto, // Valor do desconto

				    'data_desconto_2'       => $data_desconto_2!='' ? new DateTime($data_desconto_2) : "000000",
				    'valor_desconto_2'      => $valor_desconto_2, // Valor do desconto

				    'prazo'               => 0, // prazo de dias para o cliente pagar após o vencimento
				    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
				    'mensagem'            => $this->limparTexto($boleto->getInstrucao1()),
				    'data_multa'          => new DateTime($boleto->getDataVencimentoBD()), // data da multa
				    'valor_multa'         => $painel->getIdRemuneracaoOperacional()->getPercentual_Multa(), // valor da multa
				));
			}

			if($operacao==self::OCORRENCIA_ALT_VENCIMENTO){
				$arquivo->insertDetalhe(array(
				    'codigo_ocorrencia' => self::OCORRENCIA_ALT_VENCIMENTO,
				    'codigo_de_ocorrencia'=> self::OCORRENCIA_ALT_VENCIMENTO,
				    'nosso_numero'      => (int)$boleto->getNossoNumero2(),
				    'numero_documento'  => '',
				    'carteira'          => '109',
				    'especie'           => 0, // Você pode consultar as especies Cnab\Especie
				    'valor'             => 0, // Valor do boleto
				    
				    'instrucao1'        => 0, // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
				    'instrucao2'        => 0, // preenchido com zeros
				    'sacado_nome'       => '', // O Sacado é o cliente, preste atenção nos campos abaixo
				    'sacado_tipo'       => 0, //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
				    'sacado_cpf'        => 0,
				    'sacado_logradouro' => '',
				    'sacado_bairro'     => '',
				    'sacado_cep'        => 0, // sem hífem
				    'sacado_cidade'     => '',
				    'sacado_uf'         => '',
				    
				    'data_vencimento'   => new DateTime($boleto->getDataVencimentoBD()),
				    'data_cadastro'     => '',
				    'juros_de_um_dia'     => 0.00,//$painel->getIdRemuneracaoOperacional()->getPercentual_Juros(), // Valor do juros de 1 dia'
				    'data_desconto'       => "000000",
				    'valor_desconto'      => 0, // Valor do desconto
				    'prazo'               => '0', // prazo de dias para o cliente pagar após o vencimento
				    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
				    'mensagem'            => '',
				    'data_multa'          => '000000', // data da multa
				    'valor_multa'         => 0, // valor da multa
				));

				//foi criado para ajustar desconto de boletos que nao foram na remessa
				/*$data_desconto = '';
				$valor_desconto = 0;
				$array_boletos_des = array(2303751,2303752,2303754,2303756,2303757,2303759,2303760,2303761,2303762,2303764,2303765,2303767,2303768,2303770,2303772,2303773,2303776,2303777,2303778,2303780,2303782,2303783,2303784,2303786,2303789,2303790,2303791,2303792,2303794,2303796,2303798,2303800,2303802,2303804,2303806,2303808,2303810,2303811,2303813,2303814,2303815,2303817,2303819,2303820,2303821,2303822,2303823,2303824,2303825,2303827,2303828,2303830,2303832,2303834,2303836,2303837,2303840,2303841,2303843,2303844,2306989,2306991,2306993,2306994,2306995,2306997,2306998,2306999,2307000,2307001,2307002,2307003,2307004,2307005,2307006,2307007,2307010,2307011,2307012,2307013,2307015,2307017,2307018,2307019,2307020,2307021,2307022,2307023,2307024,2307025,2307027,2307029,2307030,2307033,2307034,2307035,2307036,2307037,2307038,2307039,2307040,2307041,2307042,2307043,2307045,2307046,2307048,2307049,2307050,2307051,2307052,2307053,2307054,2307055,2307056,2307057,2307058,2307059,2307060,2307061,2307062,2307064,2307066,2307067,2307068,2307069,2307070,2307071,2307072,2307073,2307074,2307075,2307076,2307077,2307078,2307079,2307080,2307081,2307082,2307083,2307084,2307085,2307086,2307087,2307089,2307092,2307093,2307094,2307095,2307096,2307097,2307099,2307100,2307101,2307102,2307103,2307104,2307105,2307106,2307107,2307108,2307109,2307110,2307111,2307112,2307113,2307114,2307115,2307116,2307117,2307118,2307119,2307120,2307121,2307122,2307123,2307124,2307125,2307127,2307128,2307129,2307131,2307133,2307134,2307135,2307137,2307138,2307139,2307140,2307141,2307142,2307143,2307144,2307145,2307146,2307148,2307149,2307150,2307152,2307153,2307154,2307155,2307156,2307158,2307159,2307162,2307165,2307166,2307167,2307169,2307172,2307173,2307174,2307175,2307176,2307177,2307178,2307179,2307180,2307181,2307182,2307183,2307184,2307185,2307186,2307188,2307189,2307190,2307191,2307192,2307193,2307195,2307196,2307197,2307198,2307199,2307200,2307201,2307202,2307205,2307206,2307207,2307208,2307209,2307210,2307211,2307212,2307213,2307214,2307215,2307216,2307217,2307218,2307219,2307220,2307221,2307222,2307223,2307224,2307225,2307226,2307227,2307228,2307230,2307231,2307232,2307233,2307234,2307235,2307237,2307238,2307240,2307241,2307242,2307243,2307353,2307354,2307355,2307356,2307357,2307358,2307359,2307360,2307362,2307363,2307364,2307365,2307366,2307368,2307369,2307370,2307371,2307372,2307373,2307374,2307376,2307377,2307379,2307380,2307382,2307476,2307477,2307478,2307479,2307480,2307481,2307482,2307484,2307485,2307486,2307487,2307488,2307489,2307490,2307491,2307492,2307493,2307494,2307495,2307496,2307497,2307498,2307499,2307500,2307501,2307502,2307503,2307504,2307505,2307506,2307507,2307508,2307509,2307510,2307511,2307512,2307513,2307514,2307515,2307516,2307517,2307518,2307519,2307520,2307521,2307522,2307523,2307524,2307525,2307526,2307527,2307528,2307530,2307532,2307533,2307534,2307535,2307536,2307537,2307538,2307539,2307540,2307541,2307542,2307543,2307544,2307545,2307546,2307547,2307548,2307549,2307550,2307551,2307552,2307553,2307554,2307555,2307556,2307558,2307559,2307560,2307561,2307563,2307564,2307565,2307566,2307567,2307568,2307569,2307570,2307571,2307572,2307573,2307574,2307575,2307576,2307577,2307578,2307579,2307580,2307581,2307582,2307583,2307584,2307585,2307586,2307587,2307588,2307589,2307590,2307591,2307592,2307593,2307594,2307595,2307596,2307597,2307598,2307599,2307600,2307601,2307602,2307603,2307604,2307605,2307606,2307609,2307610,2307611,2307612,2307613,2307614,2307615,2307616,2307617,2307618,2307619,2307620,2307621,2307623,2307624,2307625,2307626,2307627,2307628,2307630,2307631,2307632,2307633,2307634,2307635,2307636,2307637,2307638,2307639,2307641,2307642,2307643,2307644,2307647,2307648,2307649,2307650,2307651,2307652,2307653,2307654,2307655,2307656,2307657,2307658,2307659,2307660,2307661,2307662,2307663,2307664,2307665,2307667,2307668,2307669,2307670,2307671,2307672,2307674,2307675,2307676,2307677,2307678,2307679,2307680,2307681,2307682,2307683,2307684,2307685,2307686,2307687,2307688,2307689,2307690,2307691,2307692,2307693,2307694,2307695,2307697,2307698,2307699,2308274,2308275,2308277,2308278,2308280,2308281,2308282,2308283,2308284,2308285,2308286,2308287,2308288,2308289,2308290,2308291,2308292,2308293,2308294,2308295,2308296,2308297,2308298,2308299,2308300,2308301,2308302,2308303,2308304,2308305,2308306,2308307,2308308,2308310,2308311,2308312,2308313,2308314,2308315,2308316,2308317,2308318,2308319,2308320,2308321,2308322,2308323,2308324,2308325,2308326,2308327,2308328,2308329,2308330,2308331,2308332,2308333,2308334,2308335,2308336,2308337,2308338,2308340,2308341,2308342,2308343,2308344,2308345,2308346,2308347,2308348,2308349,2308350,2308351,2308352,2308353,2308354,2308355,2308356,2308357,2308359,2308360,2308362,2308363,2308364,2308365,2308367,2308368,2308369,2308370,2308371,2308372,2308373,2308374,2308375,2308376,2308377,2308378,2308380,2308381,2308383,2308384,2308385,2308386,2308387,2308388,2308389,2308390,2308392,2308393,2308394,2308395,2308396,2308397,2308398,2308399,2308400,2308402,2308403,2308404,2308405,2308406,2308407,2308408,2308409,2308410,2308412,2308413,2308414,2308415,2308416,2308417,2308418,2308419,2308421,2308422,2308423,2308424,2308425,2308426,2308427,2308428,2308429,2308430,2308431,2308432,2308433,2308434,2308435,2308436,2308437,2308438,2308439,2308440,2308441,2308442,2308443,2308445,2308446,2308447,2308448,2308449,2308450,2308451,2308452,2308453,2308456,2308458,2308459,2308460,2308461,2308462,2308464,2308465,2308466,2308467,2308468,2308469,2308470,2308472,2308473,2308474,2308476,2308477,2308478,2308479,2308481,2308482,2308483,2308484,2308485,2308486,2308487,2308488,2308489,2308490,2308491,2308492,2308493,2308702,2308703,2308704,2308705,2308706,2308707,2308708,2308709,2308710,2308712,2308715,2308716,2308717,2308718,2308719,2308720,2308721,2308722,2308723,2308724,2308725,2308726,2308727,2308729,2308731,2308732,2308733,2308735,2308736,2308738,2308739,2308741,2308742,2308744,2308745,2308746,2308747,2308748,2308749,2308750,2308751,2308752,2308753,2308754,2308755,2308756,2308757,2308758,2308759,2308760,2308761,2308762,2308763,2308764,2308768,2308769,2308771,2308772,2308773,2308774,2308775,2308776,2308777,2308778,2308779,2308781,2308782,2308783,2308785,2308787,2308789,2308790,2308791,2308792,2308793,2308794,2308795,2308796,2308797,2309052,2309053,2309054,2309055,2309056,2309057,2309058,2309059,2309060,2309061,2309062,2309063,2309064,2309065,2309066,2309067,2309068,2309069,2309070,2309071,2309073,2309074,2309076,2309077,2309078,2309079,2309081,2309082,2309083,2309084,2309085,2309086,2309087,2309089,2309090,2309091,2309092,2309094,2309095,2309096,2309097,2309098,2309099,2309101,2309102,2309104,2309106,2309107,2309108,2309109,2309110,2309111,2309112,2309116,2309117,2309118,2309119,2309120,2309121,2309122,2309123,2309125,2309126,2309127,2309129,2309130,2309131,2309132,2309133,2309134,2309135,2309136,2309137,2309138,2309139,2309140,2309141,2309142,2309143,2309144,2309145,2309146,2309148,2309149,2309150,2309151,2309152,2309153,2309154,2309155,2309156,2309157,2309158,2309159,2309160,2309161,2309163,2309166,2309167,2309168,2309169,2309170,2309172,2309173,2309174,2309175,2309176,2309177,2309178,2309180,2309181,2309182,2309183,2309184,2309185,2309186,2309187,2309188,2309189,2309190,2309192,2309193,2309194,2309195,2309196,2309197,2309198,2309199,2309200,2309201,2309202,2309204,2309205,2309206,2309207,2309208,2309209,2309210,2309211,2309212,2309213,2309214,2309215,2309216,2309217,2309218,2309219,2309220,2309221,2309222,2309223,2309224,2309225,2309226,2309227,2309228,2309229,2309230,2309231,2309232,2309233,2309234,2309235,2309236,2309237,2309238,2309239,2309240,2309241,2309242,2309244,2309245,2309246,2309247,2309248,2309249,2309250,2309252,2309253,2309254,2309256,2309257,2309259,2309260,2309262,2309263,2309264,2309266,2309267,2309268,2309269,2309270,2309271,2311840,2311841,2311842,2311843,2311844,2311845,2311846,2311847,2311848,2311849,2311850,2311851,2311852,2311853,2311854,2311855,2311856,2311857,2311858,2311859,2311860,2311861,2311862,2311863,2311864,2311865,2311866,2311867,2311868,2311869,2311870,2311871,2311872,2311873,2311874,2311875,2311876,2311877,2311878,2311879,2311881,2311882,2311883,2311884,2311885,2311886,2311887,2311888,2311889,2311890,2311891,2311893,2311894,2311895,2311896,2311897,2311898,2311899,2311900,2311901,2311902,2311903,2311904,2311905,2311906,2311907,2311908,2311909,2311910,2311911,2311912,2311913,2311914,2311915,2311916,2311917,2311918,2311919,2311920,2311921,2311922,2311923,2311924,2311925,2311926,2311927,2311928,2311929,2311930,2311931,2311932,2311933,2311934,2311935,2311936,2311937,2311938,2311939,2311940,2311941,2311942,2311943,2311944,2311945,2311946,2311947,2311948,2311949,2311950,2311951,2311952,2311953,2311954,2311955,2311956,2311957,2311958,2311959,2311960,2311961,2311962,2311963,2311964,2311965,2311966,2311967,2311968,2311969,2311970,2311971,2311972,2311973,2311974,2311975,2311976,2311977,2311978,2311979,2311980,2311981,2311982,2311983,2311984,2311985,2311986,2311987,2311988,2311990,2311991,2311992,2311993,2311994,2311995,2311996,2311997,2311998,2311999,2312000,2312001,2312002,2312003,2312004,2312005,2312006,2312007,2312008,2312009,2312010,2312012,2312013,2312014,2312015,2312016,2312017,2312018,2312019,2312020,2312021,2312022,2312023,2312024,2312025,2312026,2312027,2312028,2312029,2312030,2312031,2312032,2312033,2312034,2312035,2312036,2312038,2312039,2312040,2312041,2312042,2312043,2312044,2312045,2312046,2312047,2312048,2312049,2312050,2312051,2312052,2312053,2312054,2312055,2312056,2312057,2312058,2312059,2312060,2312061,2312062,2312063,2312065,2312066,2312067,2312068,2312069,2312070,2312072,2312073,2312074,2312075,2312076,2312077,2312078,2312079,2312080,2312081,2312082,2312083,2312084,2312085,2312086,2312087,2312088,2312089,2312090,2312091,2312092,2312094,2312095,2312096,2312097,2312098,2312099,2312100,2312101,2312102,2312103,2312104,2312105);
				if(in_array($boleto->getId(),$array_boletos_des) && date("Y-m-d")=='2017-10-06'){					
					if(count($cobrancas_unidade)>0){
						foreach ($cobrancas_unidade as $cob) {
							if($cob->getIdLancamentoDesconto()!=''){								
								$valor_desconto += abs($cob->getIdLancamentoDesconto()->getValorPrevisto());
							}
						}
					}	

					if($valor_desconto>0){
						$data_desconto = '2017-10-15';
					}
				}*/

				$arquivo->insertDetalhe(array(
				    'codigo_ocorrencia' => self::OCORRENCIA_ALT_OUTROS_DADOS,
				    'codigo_de_ocorrencia'=> self::OCORRENCIA_ALT_OUTROS_DADOS,
				    'nosso_numero'      => (int)$boleto->getNossoNumero2(),
				    'numero_documento'  => '',
				    'carteira'          => '109',
				    'especie'           => 0, // Você pode consultar as especies Cnab\Especie
				    'valor'             => $boleto->getValorDevido(), // Valor do boleto
				    
				    'instrucao1'        => 0, // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
				    'instrucao2'        => 0, // preenchido com zeros
				    'sacado_nome'       => '', // O Sacado é o cliente, preste atenção nos campos abaixo
				    'sacado_tipo'       => 0, //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
				    'sacado_cpf'        => 0,
				    'sacado_logradouro' => '',
				    'sacado_bairro'     => '',
				    'sacado_cep'        => 0, // sem hífem
				    'sacado_cidade'     => '',
				    'sacado_uf'         => '',
				    
				    'data_vencimento'   => '000000',
				    'data_cadastro'     => '',
				    'juros_de_um_dia'     => 0.00,//$painel->getIdRemuneracaoOperacional()->getPercentual_Juros(), // Valor do juros de 1 dia'
				    'data_desconto'       => $data_desconto!='' ? new DateTime($data_desconto) : "000000",
				    'valor_desconto'      => $valor_desconto, // Valor do desconto

				    'data_desconto_2'       => $data_desconto_2!='' ? new DateTime($data_desconto_2) : "000000",
				    'valor_desconto_2'      => $valor_desconto_2, // Valor do desconto

				    'prazo'               => '0', // prazo de dias para o cliente pagar após o vencimento
				    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
				    'mensagem'            => '',
				    'data_multa'          => '000000', // data da multa
				    'valor_multa'         => 0, // valor da multa
				));
			}

			if($operacao==self::OCORRENCIA_BAIXA){
				$arquivo->insertDetalhe(array(
				    'codigo_ocorrencia' => self::OCORRENCIA_BAIXA,
				    'codigo_de_ocorrencia'=> self::OCORRENCIA_BAIXA,
				    'nosso_numero'      => (int)$boleto->getNossoNumero2(),
				    'numero_documento'  => '',
				    'carteira'          => '109',
				    'especie'           => 0, // Você pode consultar as especies Cnab\Especie
				    'valor'             => $boleto->getValorCobrado(), // Valor do boleto				    
				    'instrucao1'        => 0, // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
				    'instrucao2'        => 0, // preenchido com zeros
				    'sacado_nome'       => '', // O Sacado é o cliente, preste atenção nos campos abaixo
				    'sacado_tipo'       => 0, //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
				    'sacado_cpf'        => 0,
				    'sacado_logradouro' => '',
				    'sacado_bairro'     => '',
				    'sacado_cep'        => 0, // sem hífem
				    'sacado_cidade'     => '',
				    'sacado_uf'         => '',				    
				    'data_vencimento'   => '000000',
				    'data_cadastro'     => '',
				    'juros_de_um_dia'     => 0.00,//$painel->getIdRemuneracaoOperacional()->getPercentual_Juros(), // Valor do juros de 1 dia'
				    'data_desconto'       => "000000",
				    'valor_desconto'      => 0, // Valor do desconto
				    'prazo'               => '0', // prazo de dias para o cliente pagar após o vencimento
				    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
				    'mensagem'            => '',
				    'data_multa'          => '000000', // data da multa
				    'valor_multa'         => 0, // valor da multa
				));
			}
		}

		// para salvar		
		//$arquivo->save('images/arquivos/remessas/remessa.txt');
		//echo "<a href='".base_url()."images/arquivos/remessas/remessa.txt' target='_blank'>Remessa</a>";
		return $arquivo->getText();
	}

	public function gerarRemessaBB($boletos,$conta,$pm,$convenio){
		$codigo_banco = Cnab\Banco::BANCO_DO_BRASIL_7;		
		$convenio = $convenio->getNumeroConvenio();
		if(strlen($convenio)==6){
			$codigo_banco = Cnab\Banco::BANCO_DO_BRASIL_6;
		}		
		$empresa = ($conta->getIdAdministradora()!='' ? $conta->getIdAdministradora() : $conta->getIdCondominio());		
		$arquivo = new Cnab\Remessa\Cnab400\Arquivo($codigo_banco);
		$arquivo->configure(array(
		    'data_geracao'  => new DateTime(),
		    'data_gravacao' => new DateTime(), 
		    'nome_fantasia' => $this->limparTexto($empresa->getNome()), // seu nome de empresa
		    'razao_social'  => $this->limparTexto($empresa->getIdPessoaJuridica()->getRazaoSocial()),  // sua razão social
		    'cnpj'          => $empresa->getIdPessoaJuridica()->getCnpj(), // seu cnpj completo
		    'banco'         => "1", //código do banco
		    'logradouro'    => $this->limparTexto($empresa->getLogradouro()),
		    'numero'        => $empresa->getNumero(),
		    'bairro'        => $this->limparTexto($empresa->getBairro()), 
		    'cidade'        => $this->limparTexto($empresa->getIdMunicipio()->getDescricao()),
		    'uf'            => $empresa->getIdMunicipio()->getIdEstado()->getUf(),
		    'cep'           => $empresa->getCep()=='' ? 0 : $this->limparTexto(str_replace('.','',str_replace('-','',$empresa->getCep()))), // sem hífem str_replace('.','',str_replace('-','',$empresa->getCep())),
		    'agencia'       => $conta->getAgenciaSemDv(), 
		    'agencia_dv'    => $conta->getDVAgencia(),
		    'conta'         => $conta->getNumeroSemDv(), // número da conta
		    'conta_dac'     => $conta->getDvConta(), // digito da conta		
		    'convenio_lider' => $convenio, // convenio do boleto
		));

		// você pode adicionar vários boletos em uma remessa
		foreach ($boletos as $bls){
			$boleto = $bls['boleto'];
			$operacao = $bls['operacao'];
			$boleto->atualizarProprietario();
			$condominio = $boleto->getIdUnidade()->getIdCondominio();
			$painel = $pm->getPainel($condominio);
			$cobranca = $boleto->getCobrancaPrincipal();
			$data_desconto = "";
			$valor_desconto = 0;
			// if($cobranca->getIdLancamentoDesconto()!=''){
			// 	$data_desconto = $cobranca->getIdLancamentoDesconto()->getDataVencimento();
			// 	$valor_desconto = abs($cobranca->getIdLancamentoDesconto()->getValorPrevisto());
			// }

			$cobrancas_unidade = $boleto->getCobrancas();
			if(count($cobrancas_unidade)>0){
				foreach ($cobrancas_unidade as $cob) {
					if($cob->getIdLancamentoDesconto()!=''){
						//$data_desconto = $cob->getIdLancamentoDesconto()->getDataVencimento();
						$valor_desconto += abs($cob->getIdLancamentoDesconto()->getValorPrevisto());
					}
				}
			}

			if($cobranca->getIdLancamentoDesconto()!=''){
				$data_desconto = $cobranca->getIdLancamentoDesconto()->getDataVencimento();
			// 	$valor_desconto = abs($cobranca->getIdLancamentoDesconto()->getValorPrevisto());
			}

			$remOperacional = $painel->getIdRemuneracaoOperacional();
			$valorJuros = $cobranca->calcularJuros($remOperacional->getPercentual_Juros(), $remOperacional->getTipoPeriodicidade_Juros(), $boleto->getValorCobrado(), 1);			
			$valorJuros = number_format($valorJuros,2,'.','');
			if(strlen($convenio)==6){
				$nosso_numero = $this->formata_numero($boleto->getNossoNumero2(),5,0);
				$nosso_numero = $this->formata_numero($boleto->getNumConvenio().$nosso_numero,11,0);

				$arquivo->insertDetalhe(array(
				    'codigo_ocorrencia' => 1, // 1 = Entrada de título, futuramente poderemos ter uma constante
				    'codigo_inscricao'	=> '02',
				    'numero_inscricao'	=> $empresa->getIdPessoaJuridica()->getCnpj(),
				    'agencia'      		=> $conta->getAgenciaSemDv(), 
				    'agencia_dv'    	=> $conta->getDVAgencia(),
			    	'conta'         	=> $conta->getNumeroSemDv(), // número da conta
			    	'conta_dac'     	=> $conta->getDvConta(), // digito da conta		
				    'nosso_numero'      => $nosso_numero,
				    'nosso_numero_dv'   => $this->modulo_11($nosso_numero),
				    'numero_documento'  => (int)$boleto->getNoDocumento(),
				    'variacao_carteira'	=> $conta->getVariacao()!='' ? $conta->getVariacao() : "027",
				    'aceite'  			=> $boleto->getAceite(),
				    'carteira'          => $boleto->getCarteira(),
				    'especie'           => Cnab\Especie::BB_DUPLICATA_DE_SERVICO, // Você pode consultar as especies Cnab\Especie
				    'valor'             => $boleto->getValorDevido(), // Valor do boleto
				    'instrucao1'        => 1, 
				    'instrucao2'        => 0, // preenchido com zeros
				    'sacado_nome'       => $this->limparTexto($boleto->getNomeSacado()), // O Sacado é o cliente, preste atenção nos campos abaixo
				    'sacado_tipo'       => (strlen($boleto->getCpfSacado())>14 ? 'cnpj' : 'cpf'), //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
				    'sacado_cpf'        => $boleto->getCpfSacado(),
				    'sacado_logradouro' => $this->limparTexto($boleto->getEnderecoSacado()),
				    'sacado_bairro'     => $this->limparTexto($boleto->getBairroSacado()),
				    'sacado_cep'        => $boleto->getCepSacado()=='' ? 0 : $this->limparTexto(str_replace('.','',str_replace('-','',$boleto->getCepSacado()))), //str_replace('.','',str_replace('-','',$boleto->getCepSacado())), // sem hífem
				    'sacado_cidade'     => $this->limparTexto($boleto->getCidadeSacado()),
				    'sacado_uf'         => $boleto->getUfSacado(),
				    'data_vencimento'   => new DateTime($boleto->getDataVencimentoBD()),
				    'data_cadastro'     => new DateTime(data2BD($boleto->getDataProcessamento())),
				    'juros_de_um_dia'     => $valorJuros,//$painel->getIdRemuneracaoOperacional()->getPercentual_Juros(), // Valor do juros de 1 dia'
				    'data_desconto'       => $data_desconto!='' ? new DateTime($data_desconto) : "000000",
				    'valor_desconto'      => $valor_desconto, // Valor do desconto
				    'prazo'               => '', // prazo de dias para protesto 
				    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
				    'mensagem'            => $boleto->getInstrucao1(),
				    'data_multa'          => new DateTime($boleto->getDataVencimentoBD()), // data da multa
				    'valor_multa'         => $painel->getIdRemuneracaoOperacional()->getPercentual_Multa(), // valor da multa
				    'convenio'     		  => $boleto->getNumConvenio(), // convenio do boleto
				));
			}elseif(strlen($convenio)==7){
				
				if($operacao==self::OCORRENCIA_REMESSA){
					$arquivo->insertDetalhe(array(
					    'codigo_ocorrencia' => 1, // 1 = Entrada de título, futuramente poderemos ter uma constante
					    'codigo_inscricao'	=> '02',
					    'numero_inscricao'	=> $empresa->getIdPessoaJuridica()->getCnpj(),
					    'agencia'      		=> $conta->getAgenciaSemDv(), 
					    'agencia_dv'    	=> $conta->getDVAgencia(),
				    	'conta'         	=> $conta->getNumeroSemDv(), // número da conta
				    	'conta_dac'     	=> $conta->getDvConta(), // digito da conta		
					    'nosso_numero'      => $boleto->getNumConvenio().str_pad((int)$boleto->getNossoNumero(),10,"0",STR_PAD_LEFT),
					    'numero_documento'  => (int)$boleto->getNoDocumento(),
					    'variacao_carteira'	=> $conta->getVariacao()!='' ? $conta->getVariacao() : "027",
					    'aceite'  			=> $boleto->getAceite(),
					    'carteira'          => $boleto->getCarteira(),
					    'especie'           => Cnab\Especie::BB_DUPLICATA_DE_SERVICO, // Você pode consultar as especies Cnab\Especie
					    'valor'             => $boleto->getValorDevido(), // Valor do boleto
					    'instrucao1'        => 1, 
					    'instrucao2'        => 0, // preenchido com zeros
					    'sacado_nome'       => $this->limparTexto($boleto->getNomeSacado()), // O Sacado é o cliente, preste atenção nos campos abaixo
					    'sacado_tipo'       => (strlen($boleto->getCpfSacado())>14 ? 'cnpj' : 'cpf'), //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
					    'sacado_cpf'        => $boleto->getCpfSacado(),
					    'sacado_logradouro' => $this->limparTexto($boleto->getEnderecoSacado()),
					    'sacado_bairro'     => $this->limparTexto($boleto->getBairroSacado()),
					    'sacado_cep'        => $boleto->getCepSacado()=='' ? 0 : $this->limparTexto(str_replace('.','',str_replace('-','',$boleto->getCepSacado()))), //str_replace('.','',str_replace('-','',$boleto->getCepSacado())), // sem hífem
					    'sacado_cidade'     => $this->limparTexto($boleto->getCidadeSacado()),
					    'sacado_uf'         => $boleto->getUfSacado(),
					    'data_vencimento'   => new DateTime($boleto->getDataVencimentoBD()),
					    'data_cadastro'     => new DateTime(data2BD($boleto->getDataProcessamento())),
					    'juros_de_um_dia'     => $valorJuros,//$painel->getIdRemuneracaoOperacional()->getPercentual_Juros(), // Valor do juros de 1 dia'
					    'data_desconto'       => $data_desconto!='' ? new DateTime($data_desconto) : "000000",
					    'valor_desconto'      => $valor_desconto, // Valor do desconto
					    'prazo'               => '', //prazo de dias para protesto 
					    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
					    'mensagem'            => $boleto->getInstrucao1(),
					    'data_multa'          => new DateTime($boleto->getDataVencimentoBD()), // data da multa
					    'valor_multa'         => $painel->getIdRemuneracaoOperacional()->getPercentual_Multa(), // valor da multa
					    'convenio'     		  => $boleto->getNumConvenio(), // convenio do boleto
					));
				}
				if($operacao==self::OCORRENCIA_ALT_VENCIMENTO){
					$arquivo->insertDetalhe(array(
					    'codigo_ocorrencia' => 6, // 1 = Entrada de título, futuramente poderemos ter uma constante
					    'codigo_inscricao'	=> '02',
					    'numero_inscricao'	=> $empresa->getIdPessoaJuridica()->getCnpj(),
					    'agencia'      		=> $conta->getAgenciaSemDv(), 
					    'agencia_dv'    	=> $conta->getDVAgencia(),
				    	'conta'         	=> $conta->getNumeroSemDv(), // número da conta
				    	'conta_dac'     	=> $conta->getDvConta(), // digito da conta		
					    'nosso_numero'      => $boleto->getNumConvenio().str_pad((int)$boleto->getNossoNumero(),10,"0",STR_PAD_LEFT),
					    'numero_documento'  => (int)$boleto->getNoDocumento(),
					    'variacao_carteira'	=> $conta->getVariacao(),
					    'aceite'  			=> $boleto->getAceite(),
					    'carteira'          => $boleto->getCarteira(),
					    'especie'           => Cnab\Especie::BB_DUPLICATA_DE_SERVICO, // Você pode consultar as especies Cnab\Especie
					    'valor'             => $boleto->getValorDevido(), // Valor do boleto
					    'instrucao1'        => 0,
					    'instrucao2'        => 0, // preenchido com zeros
					    'sacado_nome'       => $this->limparTexto($boleto->getNomeSacado()), // O Sacado é o cliente, preste atenção nos campos abaixo
					    'sacado_tipo'       => (strlen($boleto->getCpfSacado())>14 ? 'cnpj' : 'cpf'), //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
					    'sacado_cpf'        => $boleto->getCpfSacado(),
					    'sacado_logradouro' => $this->limparTexto($boleto->getEnderecoSacado()),
					    'sacado_bairro'     => $this->limparTexto($boleto->getBairroSacado()),
					    'sacado_cep'        => $boleto->getCepSacado()=='' ? 0 : $this->limparTexto(str_replace('.','',str_replace('-','',$boleto->getCepSacado()))), // str_replace('.','',str_replace('-','',$boleto->getCepSacado())), // sem hífem
					    'sacado_cidade'     => $this->limparTexto($boleto->getCidadeSacado()),
					    'sacado_uf'         => $boleto->getUfSacado(),
					    'data_vencimento'   => new DateTime($boleto->getDataVencimentoBD()),
					    'data_cadastro'     => new DateTime(data2BD($boleto->getDataProcessamento())),
					    'juros_de_um_dia'     => $valorJuros,//$painel->getIdRemuneracaoOperacional()->getPercentual_Juros(), // Valor do juros de 1 dia'
					    'data_desconto'       => $data_desconto!='' ? new DateTime($data_desconto) : "000000",
					    'valor_desconto'      => $valor_desconto, // Valor do desconto
					    'prazo'               => '', //prazo de dias para protesto 
					    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
					    'mensagem'            => $boleto->getInstrucao1(),
					    'data_multa'          => '', // data da multa
					    'valor_multa'         => 0, // valor da multa
					    'convenio'     		  => $boleto->getNumConvenio(), // convenio do boleto
					));
					//alteracao de valor
					$arquivo->insertDetalhe(array(
					    'codigo_ocorrencia' => 17, // 1 = Entrada de título, futuramente poderemos ter uma constante
					    'codigo_inscricao'	=> '02',
					    'numero_inscricao'	=> $empresa->getIdPessoaJuridica()->getCnpj(),
					    'agencia'      		=> $conta->getAgenciaSemDv(), 
					    'agencia_dv'    	=> $conta->getDVAgencia(),
				    	'conta'         	=> $conta->getNumeroSemDv(), // número da conta
				    	'conta_dac'     	=> $conta->getDvConta(), // digito da conta		
					    'nosso_numero'      => $boleto->getNumConvenio().str_pad((int)$boleto->getNossoNumero(),10,"0",STR_PAD_LEFT),
					    'numero_documento'  => (int)$boleto->getNoDocumento(),
					    'variacao_carteira'	=> $conta->getVariacao(),
					    'aceite'  			=> $boleto->getAceite(),
					    'carteira'          => $boleto->getCarteira(),
					    'especie'           => Cnab\Especie::BB_DUPLICATA_DE_SERVICO, // Você pode consultar as especies Cnab\Especie
					    'valor'             => $boleto->getValorDevido(), // Valor do boleto
					    'instrucao1'        => 0, 
					    'instrucao2'        => 0, // preenchido com zeros
					    'sacado_nome'       => $this->limparTexto($boleto->getNomeSacado()), // O Sacado é o cliente, preste atenção nos campos abaixo
					    'sacado_tipo'       => (strlen($boleto->getCpfSacado())>14 ? 'cnpj' : 'cpf'), //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
					    'sacado_cpf'        => $boleto->getCpfSacado(),
					    'sacado_logradouro' => $this->limparTexto($boleto->getEnderecoSacado()),
					    'sacado_bairro'     => $this->limparTexto($boleto->getBairroSacado()),
					    'sacado_cep'        => $boleto->getCepSacado()=='' ? 0 : $this->limparTexto(str_replace('.','',str_replace('-','',$boleto->getCepSacado()))), // str_replace('.','',str_replace('-','',$boleto->getCepSacado())), // sem hífem 
					    'sacado_cidade'     => $this->limparTexto($boleto->getCidadeSacado()),
					    'sacado_uf'         => $boleto->getUfSacado(),
					    'data_vencimento'   => new DateTime($boleto->getDataVencimentoBD()),
					    'data_cadastro'     => new DateTime(data2BD($boleto->getDataProcessamento())),
					    'juros_de_um_dia'     => $valorJuros,//$painel->getIdRemuneracaoOperacional()->getPercentual_Juros(), // Valor do juros de 1 dia'
					    'data_desconto'       => $data_desconto!='' ? new DateTime($data_desconto) : "000000",
					    'valor_desconto'      => $valor_desconto, // Valor do desconto
					    'prazo'               => '', //prazo de dias para protesto 
					    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
					    'mensagem'            => $boleto->getInstrucao1(),
					    'data_multa'          => '', // data da multa
					    'valor_multa'         => 0, // valor da multa
					    'convenio'     		  => $boleto->getNumConvenio(), // convenio do boleto
					));
				}
			}
		}

		// para salvar		
		#$remessa = "remessa_".date("ymdhis").".txt";
		#$arquivo->save('images/arquivos/remessas/'.$remessa);
		return $arquivo->getText();
	}

	private function modulo_11($num, $base=9, $r=0) {
		$soma = 0;
		$fator = 2; 
		for ($i = strlen($num); $i > 0; $i--) {
			$numeros[$i] = substr($num,$i-1,1);
			$parcial[$i] = $numeros[$i] * $fator;
			$soma += $parcial[$i];
			if ($fator == $base) {
				$fator = 1;
			}
			$fator++;
		}
		if ($r == 0) {
			$soma *= 10;
			$digito = $soma % 11;
			
			//corrigido
			if ($digito == 10) {
				$digito = "X";
			}
					
			if (strlen($num) == "43") {
				//então estamos checando a linha digitável
				if ($digito == "0" or $digito == "X" or $digito > 9) {
						$digito = 1;
				}
			}
			return $digito;
		} 
		elseif ($r == 1){
			$resto = $soma % 11;
			return $resto;
		}
	}

	
	private function removeAcentos($str) {				 	
		$from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
		$to = "aaaaeeiooouucAAAAEEIOOOUUC";
	    $keys = array();
	    $values = array();
	    preg_match_all('/./u', $from, $keys);
	    preg_match_all('/./u', $to, $values);
	    $mapping = array_combine($keys[0], $values[0]);
	    return strtr($str, $mapping);		
	}
	
	private function limparTexto($nome){
	    $nome = str_ireplace("_","",$nome);  
	    $nome = str_ireplace("’","",$nome);  
	    $nome = str_ireplace("`","",$nome);  
	    $nome = str_ireplace(".","",$nome);
	    $nome = str_ireplace("-","",$nome);  
	    $nome = str_ireplace("´","",$nome);  
	    $nome = str_ireplace("'","",$nome);
	    $nome = str_ireplace("nº","n",$nome);
	    $nome = str_ireplace("N°","N",$nome);
	    $nome = str_ireplace("º","",$nome);
	    $nome = str_ireplace("°","",$nome);
	    $nome = $this->removeAcentos($nome);  
	    $nome = str_ireplace("[^\\p{ASCII}]","",$nome);  
	    return $nome;  
	}

	private function formata_numero($numero,$loop,$insert,$tipo = "geral") {
		if ($tipo == "geral") {
			$numero = str_replace(",","",$numero);
			while(strlen($numero)<$loop){
				$numero = $insert . $numero;
			}
		}
		if ($tipo == "valor") {
			/*
			retira as virgulas
			formata o numero
			preenche com zeros
			*/
			$numero = str_replace(",","",$numero);
			while(strlen($numero)<$loop){
				$numero = $insert . $numero;
			}
		}
		if ($tipo == "convenio") {
			while(strlen($numero)<$loop){
				$numero = $numero . $insert;
			}
		}
		return $numero;
	}
}