<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
require('pdfboleto/fpdf/code25.php');

class Pdfboleto{
	
	private function utf8dec($array) {
		if (!is_array($array)) return;
		$helper = array();
		foreach ($array as $key => $value) 
			$helper[utf8_decode($key)] = is_array($value) ? $this->utf8dec($value) : utf8_decode($value);
		return $helper;
	}
	
	public function gerar($bol,$gerar_verso=false,$filename="",$impPgMuitosDetalhes=true,$protocolo=false){
		$pdf = new PDF_i25();
		$bols = array();		
		if(isset($bol[0])){
			$bols = $bol;			
		}else{
			$bols[] = $bol;
		}
		$tipo_entrega = array();		
		
		#ordenando boletos para agrupar pelo tipo de entrega;
		// usort($bols,function($a,$b){			
		// 	return strcmp($a['nome_unidade'], $b['nome_unidade']);
		// });
		// usort($bols,function($a,$b){			
		// 	return strcmp($a['tipoEntrega'], $b['tipoEntrega']);
		// });

		$campo1 = array();
        $campo2 = array();
		for ($i = 0; $i < count($bols); $i++) {
		  $campo2[] = $bols[$i]['nome_unidade'];
		  if(!isset($bols[$i]['tipoEntrega'])){
		  	$bols[$i]['tipoEntrega'] = 'ROTA';
		  }
		  $campo1[] = $bols[$i]['tipoEntrega']=='CORREIOS' ? 'A'.$bols[$i]['tipoEntrega'] : $bols[$i]['tipoEntrega'];
		}
		array_multisort($campo1, SORT_ASC, SORT_STRING, $campo2, SORT_ASC, $bols);
		
		$tipo_entrega_atual = isset($bols[0]['tipoEntrega']) ?  $bols[0]['tipoEntrega'] : 'ROTA';
		if(!$protocolo){
			foreach ($bols as $boleto){
				$boleto['tipoEntrega'] = isset($boleto['tipoEntrega']) ? $boleto['tipoEntrega'] : 'ROTA';
				if($tipo_entrega_atual!=$boleto['tipoEntrega']){
					$tipo_entrega_atual = $boleto['tipoEntrega'];				
					$this->gerarPgDivisaoEntrega($pdf,$tipo_entrega_atual);
				}

				if($boleto['tipoEntrega']=='ROTA'){
					$tipo_entrega['ROTA'][] = $boleto;
				}else{
					$tipo_entrega['CORREIOS'][] = $boleto;
				}

				if($boleto['banco']=='001'){#Bnaco do Brasil
					$boleto['local_pagamento'] = 'PAGÁVEL EM QUALQUER BANCO';
				}else{
					#outros bancos
					$boleto['local_pagamento'] = 'PAGÁVEL EM QUALQUER BANCO ATÉ O VENCIMENTO';
				}				
				$boleto = $this->utf8dec($boleto);

				$usar_top_mais_detalhes = count($boleto['detalhes_boleto']) >= 60 && $impPgMuitosDetalhes;
				//var_dump($usar_top_mais_detalhes);exit;
				if($usar_top_mais_detalhes){
					$pdf->AddPage("L");

					$pdf->SetFont('Arial','',7);				
					$pdf->text(10,7,$boleto['cedente']);
					$pdf->text(10,10,$boleto['nome_condominio']);
					$pdf->text(10,13,$boleto['endereco_cedente']);				
					$pdf->text(170,13,'R$ '.$boleto['valor_boleto']);
					$pdf->text(10,16,'Vencimento: '.$boleto['data_vencimento'].', Documento: '.$boleto['numero_documento']);

					if(count($boleto['detalhes_boleto'])>200){
						$pdf->SetFont('Arial','',5);
					}else{
						$pdf->SetFont('Arial','',6.4);
					}
					
					$linha_detalhe = 18;
					$altura_linha = 2.5;
					$split = 50;

					$posicao_x1 = 10;
					$posicao_x2 = 90;

					//$detalhes1 = array_slice($boleto['detalhes_boleto'],0,$split);
					$detalhes_split = array_chunk($boleto['detalhes_boleto'],$split); 

					//pre($detalhes_split);exit;
					$coluna = 0;	
					foreach ($detalhes_split as $detalhes1){
						$stringX1 = array();		
						$stringX2 = array();	
						if($coluna>0 && $coluna%2==0){
							$posicao_x1 = 10;
							$posicao_x2 = 90;
							$pdf->AddPage("L");
						}
						foreach($detalhes1 as $detalhe){
							$pdf->SetXY($posicao_x1,$linha_detalhe);
							$str = str_replace('Taxa de Reembolso Referente','Tx Reembolso Ref.',$detalhe['descricao']);
							$str = str_replace('gua de Janeiro/2018 (02/2018)','gua 01/2018',$str);
							$str = str_replace('Energia de Janeiro/2018 (02/2018)','Energia 01/2018',$str);
							$str = utf8_decode(str_replace('Taxa Extra - (Para Melhorias: Recuperação do Jardim, Sistema de Irrigação e Limpeza Pós-Obra) - AGE - ','Tx Extra - (Rec. Jardim, Sis. Irrig. e Limp. Pós-Obra) - ',utf8_encode($str)));
							$str = str_replace('Taxa','Tx',$str);
							$pdf->MultiCell(120, $altura_linha , trim($str),0);
							$pdf->SetXY($posicao_x2,$linha_detalhe); #175
							$pdf->MultiCell(26, $altura_linha , trim($detalhe['valor']),0,"R");
							$linha_detalhe += $altura_linha;
							$altura_linha = 2.5;
							$coluna++;
						}
						$linha_detalhe = 18;
						$altura_linha = 2.5;

						$posicao_x1 += 120;
						$posicao_x2 += 120;

					}		
				}
				$usar_top_mais_detalhes2 = !$usar_top_mais_detalhes&&count($boleto['detalhes_boleto']) > 21 && count($boleto['detalhes_boleto'])<60 && $impPgMuitosDetalhes;
				//var_dump($usar_top_mais_detalhes)." - ";
				$pdf->AddPage();
				if($usar_top_mais_detalhes2){
					$pdf->SetFont('Arial','',6.5);							
					$linha_detalhe = 28;				
					$altura_linha = 2.5;
					$detalhes1 = array_slice($boleto['detalhes_boleto'],0,36);
					foreach($detalhes1 as $detalhe){				
						$pdf->SetXY(10,$linha_detalhe);						
						$pdf->MultiCell(67, $altura_linha , trim($detalhe['descricao']),0);
						$pdf->SetXY(77,$linha_detalhe); #175
						$pdf->MultiCell(26, $altura_linha , $detalhe['valor'],0,"R");
						if(strlen($detalhe['descricao'])>62){
							$altura_linha = 5;
						}
						$linha_detalhe += $altura_linha;
						$altura_linha = 2.5;					
					}
					$linha_detalhe = 28;
					$altura_linha = 2.5;
					// pre($boleto['detalhes_boleto']);
					// pre($detalhes1);exit;
					$detalhes2 = array_slice($boleto['detalhes_boleto'],36,count($boleto['detalhes_boleto']));
					foreach($detalhes2 as $detalhe){				
						$pdf->SetXY(105,$linha_detalhe);						
						$pdf->MultiCell(72, $altura_linha , trim($detalhe['descricao']),0);
						$pdf->SetXY(175,$linha_detalhe); #175
						$pdf->MultiCell(26, $altura_linha , $detalhe['valor'],0,"R");
						if(strlen($detalhe['descricao'])>62){
							$altura_linha = 5;
						}
						$linha_detalhe += $altura_linha;
						$altura_linha = 2.5;					
					}
					
					$pdf->text(160,120,'Total em R$:');
					$pdf->SetXY(175,94.3);
					$pdf->cell(26, 50 , $boleto['valor_boleto'] ,0,0,'R');
				}		
				//BOLETO
				$pdf->Image('images/boleto/'.$boleto['img_topo'],7,7,-228);
				
				$pdf->Image('images/boleto/boleto_sacado_registrado.png',6,125,-225);
				$pdf->Image('images/boleto/boleto_cedente_registrado.png',6,194,-225);

				// $pdf->Image('images/boleto/boleto_sacado.png',6,125,-222);
				// $pdf->Image('images/boleto/boleto_cedente.png',6,194,-222);	

				if($boleto['banco']=='001'){
					//LOGOMARCA BANCO
					$pdf->Image('images/boleto/icone_bb.png',8,128,38);
					$pdf->Image('images/boleto/icone_bb.png',8,198,38);	
					$boleto['codigo_banco'] = '001-9';	
					$boleto['moeda'] = "R$";
				}

				if($boleto['banco']=='341'){
					//LOGOMARCA BANCO
					$pdf->Image('images/boleto/logoitau.png',8,125,38);
					$pdf->Image('images/boleto/logoitau.png',8,195.2,38);
					$boleto['especie_doc'] = "";
					$boleto['moeda'] = "Real";
					$boleto['codigo_banco'] = '341-7';
				}					
				
				$pdf->SetFont('Arial','B',12);
				$pdf->text(60,7,$boleto['nome_condominio']);
				
				$pdf->SetFont('Arial','B',8);
				$pdf->text(10,141,$boleto['cedente']);
				$pdf->text(10,144.8,$boleto['endereco_cedente']);
				$pdf->text(131,141,$boleto['agencia'].' / '.$boleto['conta']);
				$pdf->text(184,141,$boleto['data_vencimento'], 'R');
				$pdf->text(13,151,$boleto['data_documento']);
				$pdf->text(49,151,$boleto['numero_documento'], 'C');
				$pdf->text(86,151,$boleto['especie_doc'], 'C');
				$pdf->text(108,151,$boleto['aceite'], 'C');
				$pdf->text(131,151,$boleto['data_processamento'], 'C');
				$pdf->text(171.5,151,$boleto['nosso_numero'], 'R');
				$pdf->text(66,156.5,$boleto['moeda'], 'C');
				$pdf->text(40,156.5,$boleto['carteira'].$boleto['variacao_carteira'], 'C');
				
				//$pdf->text(193,157,$boleto['valor_boleto'], 'R');
				$pdf->SetXY(169,153.5);
				$pdf->cell(30,4,$boleto['valor_boleto'], 0,0,'R');

				$pdf->text(9,162,$boleto['demonstrativo1'], 'L');
				$pdf->text(9,169,$boleto['sacado']);
				$pdf->text(9,174,$boleto['demonstrativo2']);
				$pdf->text(9,180,$boleto['endereco1'].$boleto['endereco2']);		
				$pdf->SetFont('Arial','B',18);

				$pdf->text(51,134,$boleto['codigo_banco'], 'L');
				$pdf->text(51,203,$boleto['codigo_banco'], 'L');
				
				$pdf->SetFont('Arial','B',12.5);
				$pdf->text(75,203,$boleto['linha_digitavel'], 'L');
				$pdf->text(75,134,$boleto['linha_digitavel'], 'L');
				$pdf->SetFont('Arial','B',8);
				$pdf->text(9,210,$boleto['local_pagamento'], 'L');
				$pdf->text(184,210,$boleto['data_vencimento'], 'R');
				$pdf->text(9,215.6,$boleto['cedente'], 'L');
				$pdf->text(175,215.6,$boleto['agencia'].' / '.$boleto['conta'], 'R');
				$pdf->text(13,221.5,$boleto['data_documento']);
				$pdf->text(49,221.5,$boleto['numero_documento'], 'C');
				$pdf->text(86,221.5,$boleto['especie_doc'], 'C');
				$pdf->text(110,221.5,$boleto['aceite'], 'C');
				$pdf->text(133,221.5,$boleto['data_processamento'], 'C');
				$pdf->text(171.5,221.5,$boleto['nosso_numero'], 'R');
				$pdf->text(40,227,$boleto['carteira'].$boleto['variacao_carteira'], 'C');
				$pdf->text(66,227,$boleto['moeda'], 'C');
				
				//$pdf->text(193,227,$boleto['valor_boleto'], 'R');
				$pdf->SetXY(169,224.5);
				$pdf->cell(30,4,$boleto['valor_boleto'], 0,0,'R');
				$pdf->SetFont('Arial','',7);
				$pdf->text(9,234,$boleto['demonstrativo2']);
				$pdf->text(9,238,$boleto['demonstrativo3']);
				$pdf->text(9,242,$boleto['instrucoes1']);
				$pdf->text(9,246,$boleto['instrucoes2']);
				
				$pdf->SetFont('Arial','B',8);
				$pdf->text(9,250,$boleto['instrucoes3']);
				
				$pdf->SetFont('Arial','',7);
				$pdf->text(9,254,$boleto['instrucoes4']);
				
				$pdf->text(9,264,$boleto['sacado']);
				$pdf->text(9,268.5,$boleto['endereco1']);
				$pdf->text(9,273,$boleto['endereco2']);

				$pdf->text(110,276,$boleto['cpf_cnpj']);
				
				//dados na imagem
				$pdf->SetFont('Arial','B',7);
				$pdf->text(62,15,$boleto['sacado']);
				$pdf->SetXY(61,17);
				$pdf->MultiCell(73, 2, $boleto['endereco1']);
				
				$pdf->SetFont('Arial','B',12);
				$pdf->text(143,17,$boleto['data_vencimento']);		
				$pdf->text(176,17,$boleto['valor_boleto']);
				
				#detalhes
				$pdf->SetFont('Arial','',7);			
				
				if(!$usar_top_mais_detalhes2 && count($boleto['detalhes_boleto']) <= 21){
					$linha_detalhe = 30;
					$linha_detalhe2 = 6;
					$altura_linha = 4;
					foreach($boleto['detalhes_boleto'] as $detalhe){				
						$pdf->SetXY(61,$linha_detalhe);
						$pdf->MultiCell(114, $altura_linha , trim(($detalhe['descricao'])),0);
						$pdf->SetXY(175,$linha_detalhe);
						$pdf->MultiCell(26, $altura_linha , $detalhe['valor'],0,"R");
						if(strlen($detalhe['descricao'])>94){
							$altura_linha = 8;
						}
						$linha_detalhe += $altura_linha;
						$altura_linha = 4;				
					}		
					
					$pdf->text(155,115,'Total em R$:');
					$pdf->SetXY(175,89.1);
					$pdf->cell(26, 50 , $boleto['valor_boleto'] ,0,0,'R');			

					if(isset($boleto['boletos_em_aberto'])&&$boleto['boletos_em_aberto']>0){
						//informacao boleto - pode ser mensagem ou quantidade de boleto em aberto
						$pdf->SetFont('Arial','B',6.5);
						$pdf->SetXY(7,95);
						$pdf->SetFillColor(222, 222, 222);
						$pdf->MultiCell(51, 3,utf8_decode("ATENÇÃO!\nEm ".date("d/m/Y")." a unidade possui ".$boleto['boletos_em_aberto'].($boleto['boletos_em_aberto']>1 ? " boletos" : " boleto")." em aberto. Acesso o site ou entre em contato."),0,'C',1);				
						$pdf->SetFillColor(222, 222, 222);		

					}else{
						if(isset($boleto['mensagem_adicional'])&&$boleto['mensagem_adicional']!=''){
							$pdf->SetFont('Arial','B',9);
							$pdf->SetXY(7,95);
							$pdf->SetFillColor(222, 222, 222);
							$pdf->MultiCell(51, 4,utf8_decode($boleto['mensagem_adicional']),0,'C',1);
						}
					}
					/*
				    Autor:  Natanael Diego  
				    Data:   15/08/2016
				    Hora:   16:27
				    Funcao: Colocado o texto de atraso.
				    */
				    if(isset($boleto['info_serasa'])&&$boleto['info_serasa']>0){
						$pdf->Ln();
						$pdf->SetFont('Arial','B',6.5);			
						$pdf->setXY(7,108);
						$pdf->MultiCell(51, 3,utf8_decode("ATENÇÃO!\nAtraso superior a ".$boleto['info_serasa']['dias']." dias será enviado ao órgão de restrição de crédito \n(SPC)-(".$boleto['info_serasa']['age_ago']."-".$boleto['info_serasa']['data'].")".$boleto['info_serasa']['dataAde']),0,'C',0);
					}
				}
				
				$pdf->SetFillColor(0, 0, 0);
				$pdf->i25(10, 279, $boleto['cod_barra']);

				if($gerar_verso){
					//verso
					$pdf->AddPage();
					
					if($boleto['marca_adm']!='marca-predial.png'){
						$pdf->Image('images/'.$boleto['marca_adm'],38,102);
						$pdf->Rect(37, 122, 140, 40);
					}else{
						$pdf->Image('images/boleto/envelope-boleto.png',2.5,106.5,205,112);
					}

					if($boleto['possuiDescPontualidade']){
						if($boleto['idAdministradora']==1){
							$pdf->Image('images/boleto/comunica-02-capa-do-boleto-desc-pont.png',38,15,130,52);
						}
						if($boleto['idAdministradora']==2){
							$pdf->Image('images/boleto/comunica-02-capa-boleto-do-boleto-asp-desc-pont.png',38,15,130,52);					
						}
						if($boleto['idAdministradora']==3){
							//$pdf->Image('images/boleto/comunica-02-capa-boleto-do-boleto-asp-desc-pont.png',38,15,130,52);					
							$pdf->Image('images/boleto/infomativo-desconto-DF-novo.png',1,1,-198);
						}
					}					

					$pdf->SetFont('Arial','B',9);
					$pdf->text(118,120,'Boleto de vencimento: '.$boleto['data_vencimento']);
					$pdf->SetFont('Arial','I',8);
					$pdf->text(41,126,utf8_decode('Destinatário:'));
					$pdf->SetFont('Arial','',9);
					$pdf->text(41,130,$boleto['sacado']);
					$pdf->text(41,134,$boleto['endereco1']);
					$pdf->text(41,138,$boleto['endereco2']);
					$pdf->text(41,142, $boleto['descricao_unidade']);

					$pdf->SetFont('Arial','I',8);
					$pdf->text(41,152,'Remetente:');
					$pdf->SetFont('Arial','',9);
					$pdf->text(41,156,$boleto['cedente']);
					$pdf->text(41,160,$boleto['endereco_cedente']);
				}			
			}
		}
			
		if(count($bols)>1&&$protocolo){
			$tipo_entrega = array();
			$vencimentos = array();			
			$descricoes = array();
			foreach ($bols as $boleto){
				$boleto['tipoEntrega'] = isset($boleto['tipoEntrega']) ? $boleto['tipoEntrega'] : 'ROTA';				
				$vencimentos[$boleto['data_vencimento']] = $boleto['data_vencimento'];
				$descricoes[$boleto['demonstrativo1']] = $boleto['demonstrativo1'];
				if($boleto['tipoEntrega']=='ROTA'){
					$tipo_entrega['ROTA'][] = $boleto;
				}else{
					$tipo_entrega['CORREIOS'][] = $boleto;
				}
			}
			$this->gerarProtocoloBoletos($pdf,$tipo_entrega,$bols[0]['marca_adm'],$bols[0]['demonstrativo1'],$vencimentos,$descricoes);
		}
		$pdf->Output($filename,"I");
	}

	private function gerarPgDivisaoEntrega($pdf,$tipo_entrega){		
		$pdf->SetFont('arial','',40);		
		$pdf->MultiCell(190,270,$tipo_entrega,0,'C');
		$pdf->addPage();
	}

	private function cabecalhoProtocolo($pdf,$tipo,$img,$detalhes){
		$pdf->Image('images/'.$img,5,5);
		$pdf->SetY(2);
		$pdf->SetFont('arial','',7);
		$CI =& get_instance();
		$usuario = $CI->session->userdata('dados_usuario_logado');
				
		$pdf->Ln(0);						
		$pdf->SetFont('arial','B',12);
		$pdf->SetDrawColor(255,255,255);
		#$pdf->SetDrawColor(1,1,1);
		
		$pdf->Cell(45,20,"",0,0,'C'); #logo
		$pdf->MultiCell(110,7.5,'Protocolo de Entrega - '.$tipo,1,'C');
		$pdf->setXY(165,9);		

		$pdf->SetFont('arial','',7);
		$pdf->MultiCell(35,3,"Classecon ".$CI->config->item('versaoClassecon')."\n".date("d/m/Y H:i:s")."\n". utf8_decode("Usuário: ".$usuario['nomeLogado'].""),1,'R'); #info
				
		$str_d = array();
		$pdf->txtHeader = $detalhes;
		for($i=0;$i<count($pdf->txtHeader);$i++){			
			$str_d[] = utf8_decode($pdf->txtHeader[$i]);
		}
		$pdf->setXY(45,10);	
		$pdf->SetFont('arial','B',8.5);	
		$pdf->SetDrawColor(255,255,255);
		$pdf->MultiCell(120,5,implode(', ',$str_d),0,'C');
		$pdf->Ln(5);
				
		$pdf->SetDrawColor(255,255,255);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('arial','B');
		
		//$pdf->SetDrawColor(1,1,1);
		$x = $pdf->GetX();
		$y = $pdf->GetY();

		$pdf->SetXY($x, $y);
		$pdf->MultiCell(36,5,'Unidade',1,'L');		
		
		$pdf->SetXY($x + 37, $y);
		$pdf->MultiCell(50,5,utf8_decode('Condômino'),1,'L');
		
		$pdf->SetXY($x + 87, $y);
		$pdf->MultiCell(80,5,'Assinatura',1,'L');
		
		$pdf->SetXY($x + 167, $y);
		$pdf->MultiCell(24,5,'Data',1,'L');

		$pdf->SetFont('arial','',9);
		$pdf->SetDrawColor(1,1,1);
		$pdf->SetFillColor(1,1,1);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->Line($x,$y, 200, $y);
		$pdf->Ln(2);
	}
	
	private function gerarProtocoloBoletos($pdf,$bols,$img,$descricao,$vencimentos,$descricoes){

		foreach ($bols as $tipo => $boletos){
			$pdf->AddPage();
			$detalhes = array();
			$detalhes[] = 'Condomínio: '.$boletos[0]['nome_condominio'];
			$detalhes[] = implode(',',$descricoes);
			$detalhes[] = 'Quantidade: '.count($boletos);
			$detalhes[] = 'Vencimento(s): '.implode(',',$vencimentos);
			$this->cabecalhoProtocolo($pdf,$tipo,$img,$detalhes);
			//****************************** inicio linhas	
			foreach($boletos as $bol){
				$pdf->SetFont('arial','',8);
				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->SetDrawColor(255,255,255);

				$pdf->SetXY($x, $y);
				//pre($bol['demonstrativo2']);exit;
				$unidade = url_title($bol['demonstrativo2']);
				$pdf->MultiCell(36,2,str_replace('Referente-unidade-','',$unidade),0,'L');
				
				$pdf->SetXY($x + 37, $y);				
				$pdf->MultiCell(50,2,utf8_decode($bol['sacado']),0,'L');
				
				$pdf->SetXY($x + 87, $y);
				$pdf->MultiCell(80,2,'______________________________________',0,'L');
				
				$pdf->SetXY($x + 167, $y);
				$pdf->MultiCell(24,2,'___/___/____',0,'L');

				$pdf->Ln(3);

				if($pdf->GetY() > 272){
					$pdf->Ln(4);
					$pdf->AddPage();
					$this->cabecalhoProtocolo($pdf,$tipo,$img,$detalhes);
				}
			}
			//*************************************** fim linhas
		}

		$pdf->SetDrawColor(255,255,255);
		$pdf->SetFillColor(255,255,255);
		$pdf->Ln(1);		
	}
	
	public function gerar_com_balancete($boletos,$nome_arquivo="",$layoutboleto="boleto_demonstrativo1_v2.png",$marca_adm){
		$pdf = new PDF_i25();
		if(isset($boletos['prestacao_contas']['RECEITA']))
			$boletos['prestacao_contas']['RECEITA'] = $this->utf8dec($boletos['prestacao_contas']['RECEITA']);
		if(isset($boletos['prestacao_contas']['DESPESA']))
			$boletos['prestacao_contas']['DESPESA'] = $this->utf8dec($boletos['prestacao_contas']['DESPESA']);
		foreach($boletos['lista'] as $boleto){
			$pdf->AddPage();
			
			//demonstraivo
			if($boleto['banco']=='001'){
				$pdf->Image('images/boleto/'.$layoutboleto,7,4,-222);			
			}
			if($boleto['banco']=='341'){
				// if($boleto['idAdministradora']==1){
				// 	$layoutboleto = 'boleto_demonstrativo1_v2_predial_itau.png';
				// }else{
				// 	$layoutboleto = 'boleto_demonstrativo1_v2_generico_itau.png';
				// }
				$layoutboleto = 'boleto_demonstrativo1_v2_generico_itau.png';
				$pdf->Image('images/boleto/'.$layoutboleto,7,4,-222);
				$pdf->Image('images/'.$marca_adm,10,10);
			}
			
			//boleto
			$pdf->Image('images/boleto/boleto_cedente.png',6,194,-222);
			//LOGOMARCA BANCO
			//$pdf->Image('images/boleto/icone_bb.png',8,198,38);


			if($boleto['banco']=='001'){
				//LOGOMARCA BANCO				
				$pdf->Image('images/boleto/icone_bb.png',8,198,38);	
				$boleto['codigo_banco'] = '001-9';	
				$boleto['moeda'] = "R$";
			}

			if($boleto['banco']=='341'){
				//LOGOMARCA BANCO				
				$pdf->Image('images/boleto/logoitau.png',8,195.2,38);
				$boleto['especie_doc'] = "";
				$boleto['moeda'] = "Real";
				$boleto['codigo_banco'] = '341-7';
			}

			$site_adm = '';
			if($boleto['idAdministradora']=='1'){
				$site_adm = 'www.predialadm.com.br';
			}
			if($boleto['idAdministradora']=='2'){
				$site_adm = 'www.aspnewpred.com.br';
			}
			if($boleto['idAdministradora']=='3'){
				$site_adm = 'www.aspnewpred.com.br';
			}
			if($boleto['idAdministradora']=='10'){
				$site_adm = 'www.livingteresina.com.br';
			}
			if($boleto['idAdministradora']=='11'){
				$site_adm = 'www.newpred.com.br';
			}			
			
			if($boleto['banco']=='001'){#Bnaco do Brasil
				$boleto['local_pagamento'] = 'PAGÁVEL EM QUALQUER BANCO';
			}else{
				#outros bancos
				$boleto['local_pagamento'] = 'PAGÁVEL EM QUALQUER BANCO ATÉ O VENCIMENTO';
			}			
			$boleto = $this->utf8dec($boleto);			
			$pdf->SetFont('Arial','',9);
			if(validateDate($boletos['referencia_balancete'])){
				$pdf->SetXY(8,30.5);
				$pdf->cell(150,5,$boleto['nome_condominio']." - Balancete Ref. ".date("m/Y",strtotime($boletos['referencia_balancete'])),0,0,'C');				
			}else{
				$pdf->text(60,34,$boleto['nome_condominio']);
			}

			$pdf->SetFont('Arial','',7);
			#enderco do site no boleto
			$pdf->SetXY(163,106);
			$pdf->cell(37,5,$site_adm,0,0,'C');
			
			$pdf->SetFont('Arial','B',8);
			$pdf->text(53,23,$boleto['cedente'], 'L');
			$pdf->text(53,15,$boleto['local_pagamento'], 'L');
			$pdf->text(179,141,$boleto['agencia'].' / '.$boleto['conta']);
			$pdf->text(185,133,$boleto['data_vencimento'], 'R');
			$pdf->text(110,30,$boleto['data_documento']);
			$pdf->text(133,30,$boleto['numero_documento'], 'C');
			$pdf->text(170,30,$boleto['especie_doc'], 'C');
			//$pdf->text(108,142.5,$boleto['aceite'], 'C');
			$pdf->text(180,30,$boleto['data_processamento'], 'C');
			
			
			$pdf->SetXY(166,144);
			$pdf->cell(34,5,$boleto['nosso_numero'],0,0,'R');
			//$pdf->text(163,148.5,$boleto['nosso_numero'], 'R');
			
			//$pdf->text(66,148.5,$boleto['moeda'], 'C');
			$pdf->text(94,30,$boleto['carteira'].$boleto['variacao_carteira'], 'C');
			
			$pdf->SetXY(166,152);
			$pdf->cell(34,5,$boleto['valor_boleto'],0,0,'R');
	
			$pdf->SetFont('Arial','',7);
			$pdf->text(9,185,$boleto['sacado']);
			$pdf->text(9,188,$boleto['demonstrativo2']);
	
			//$pdf->text(9,165.5,$boleto['demonstrativo2']);
			//$pdf->text(9,170.5,$boleto['endereco1'].$boleto['endereco2']);		
			$pdf->SetFont('Arial','B',18);
			$pdf->text(52,203,$boleto['codigo_banco'], 'L');
			//$pdf->SetFont('Arial','B',8);
			
			//segunda parte boleto
			$pdf->SetFont('Arial','B',12.5);
			$pdf->text(73,203,$boleto['linha_digitavel'], 'L');
			$pdf->SetFont('Arial','B',8);		
			$pdf->text(9,210,$boleto['local_pagamento'], 'L');
			$pdf->text(188,210,$boleto['data_vencimento'], 'R');
			$pdf->text(9,215.6,$boleto['cedente'], 'L');
			$pdf->text(175,215.6,$boleto['agencia'].' / '.$boleto['conta'], 'R');
			$pdf->text(13,222,$boleto['data_documento']);
			$pdf->text(49,222,$boleto['numero_documento'], 'C');
			$pdf->text(86,222,$boleto['especie_doc'], 'C');
			$pdf->text(110,222,$boleto['aceite'], 'C');
			$pdf->text(133,222,$boleto['data_processamento'], 'C');
			$pdf->text(175,222,$boleto['nosso_numero'], 'R');
			$pdf->text(40,227,$boleto['carteira'].$boleto['variacao_carteira'], 'C');
			$pdf->text(66,227,$boleto['moeda'], 'C');
			$pdf->text(193,227,$boleto['valor_boleto'], 'R');
			
			$pdf->SetFont('Arial','B',6);
			$pdf->text(9,234,$boleto['demonstrativo1']);
			$pdf->text(9,237,$boleto['demonstrativo2']);
			$pdf->text(9,240,$boleto['demonstrativo3']);
			$pdf->text(9,243,$boleto['instrucoes1']);
			$pdf->text(9,246,$boleto['instrucoes2']);

			$pdf->SetFont('Arial','B',8);
			$pdf->text(9,249,$boleto['instrucoes3']);
			
			$pdf->SetFont('Arial','B',6);
			$pdf->text(9,252,$boleto['instrucoes4']);

			#Após 30 dias de atraso -– cobrar 10.00% Despesas de Cobrança
			
			$pdf->SetFont('Arial','B',8);
			$pdf->text(9,264,$boleto['sacado']);
			$pdf->text(9,268.5,$boleto['endereco1']);
			$pdf->text(9,273,$boleto['endereco2']);
			
			$pdf->text(62,159.5,utf8_decode('Composição da Arrecadação'));
			
			//dados na imagem
			/*$pdf->SetFont('Arial','B',7);
			$pdf->text(62,15,$boleto['sacado']);
			$pdf->SetXY(61,17);
			$pdf->MultiCell(75, 2, $boleto['endereco1']);
			*/
			/*$pdf->SetFont('Arial','B',12);
			$pdf->text(144,17,$boleto['data_vencimento']);		
			$pdf->text(177,17,$boleto['valor_boleto']);*/
			
			#detalhes
			$pdf->SetFont('Arial','',6);
			// $linha_detalhe = 163;
			// $linha_detalhe2 = 138; 

			$linha_detalhe = 161;
			$cont_detalhes = 1;
			$coluna_detalhe_descricao = 7;
			$coluna_detalhe_valor = 65;
			$largura_padrao = 57;
			$coluna1 = true;
			if(isset($boleto['detalhes_boleto'])){
				foreach($boleto['detalhes_boleto'] as $detalhe=>$valor){
					// $pdf->text(8,$linha_detalhe,$detalhe);
					// $pdf->SetXY(134,$linha_detalhe2);
					// $pdf->cell(25, 50 , $valor ,0,0,'R');
					// $linha_detalhe+=2.2;
					// $linha_detalhe2+=2.2;
					// $linha_detalhe+=2.2;
					// $linha_detalhe2+=2.2;					
					$pdf->SetXY($coluna_detalhe_descricao,$linha_detalhe);					
					$pdf->MultiCell($largura_padrao,2,$detalhe,0,"L");
					
					$pdf->SetXY($coluna_detalhe_valor,$linha_detalhe);
					$pdf->MultiCell(15,2,$valor,0,"R");
					if(strlen($detalhe)>65){
						$linha_detalhe += 5;
						$cont_detalhes++;
					}else{
						$linha_detalhe += 2.1;
					}
					$cont_detalhes++;
					if($cont_detalhes%11==0){
						$coluna1 = false;
						$linha_detalhe = 161;
						$coluna_detalhe_descricao = 83;
						$coluna_detalhe_valor = 146;
						$cont_detalhes = 1;
						$largura_padrao = 62;
					}
				}
			}
				
			//$pdf->text(155,115,'Total em R$:');
			//$pdf->SetXY(177,89.1);
			//$pdf->cell(25, 50 , $boleto['valor_boleto'] ,0,0,'R');
			//$pdf->text(177,115,$boleto['valor_boleto']);
			$cont_linha_bal = 0;
			if(isset($boletos['prestacao_contas']['DESPESA'])&&count($boletos['prestacao_contas']['DESPESA'])>0){
				foreach($boletos['prestacao_contas']['DESPESA'] as $receita){
					$cont_linha_bal++;
					foreach($receita['lancamentos'] as $lanc){
						$cont_linha_bal++;
					}
				}
			}
			
			if($cont_linha_bal<47){
				$pdf->SetFont('Arial','',6.5);
				$cont_linha = 17;
				$salto_linha = 2.5;
			}else{
				$pdf->SetFont('Arial','',5);
				$cont_linha = 17;
				$salto_linha = 2;
			}
			
			//print_r($boleto['prestacao_contas']);exit;
			
			//receitas	
			if(isset($boletos['prestacao_contas']['RECEITA'])&&count($boletos['prestacao_contas']['RECEITA']>0)){		
				foreach($boletos['prestacao_contas']['RECEITA'] as $receita){
					$pdf->SetFont('Arial','B');
					$pdf->SetXY(8,$cont_linha);
					$pdf->cell(73, 50 , "- ".$receita['Conta'] ,0,0,'L');
					$pdf->SetXY(8,$cont_linha);
					$pdf->cell(73, 50 , $receita['Valor'] ,0,0,'R');
					$cont_linha += $salto_linha;
					$pdf->SetFont('Arial','');
					foreach($receita['lancamentos'] as $lanc){
						$pdf->SetXY(8,$cont_linha);
						$pdf->cell(73, 50 , "\t\t\t\t".$lanc['Conta'] ,0,0,'L');
						$pdf->SetXY(8,$cont_linha);
						$pdf->cell(73, 50 , $lanc['Valor'] ,0,0,'R');
						$cont_linha += $salto_linha;
					}
				}
			}
			$pdf->SetXY(8,$cont_linha);
			$pdf->cell(73, 50 , 'Total' ,0,0,'L');
			$pdf->SetXY(8,$cont_linha);
			$pdf->cell(73, 50 , $boletos['prestacao_contas']['total_receitas'] ,0,0,'R');
			
			$cont_linha = 17;			
			if(isset($boletos['prestacao_contas']['DESPESA'])&&count($boletos['prestacao_contas']['DESPESA'])>0){
				foreach($boletos['prestacao_contas']['DESPESA'] as $receita){
					$pdf->SetFont('Arial','B');
					$pdf->SetXY(85,$cont_linha);
					$pdf->cell(73, 50 , "- ".$receita['Conta'] ,0,0,'L');
					$pdf->SetXY(85,$cont_linha);
					$pdf->cell(73, 50 , $receita['Valor'] ,0,0,'R');
					$cont_linha += $salto_linha;
					$pdf->SetFont('Arial','');
					foreach($receita['lancamentos'] as $lanc){
						$pdf->SetXY(85,$cont_linha);
						$pdf->cell(73, 50 , "\t\t\t\t".$lanc['Conta'] ,0,0,'L');
						$pdf->SetXY(85,$cont_linha);
						$pdf->cell(73, 50 , $lanc['Valor'] ,0,0,'R');
						$cont_linha += $salto_linha;
					}
				}
			}
			
			$pdf->SetXY(85,$cont_linha);
			$pdf->cell(73, 50 , 'Total' ,0,0,'L');
			$pdf->SetXY(85,$cont_linha);
			$pdf->cell(73, 50 , $boletos['prestacao_contas']['total_despesas'] ,0,0,'R');
			
			//resumo
			$pdf->SetFont('Arial','',4.5);
			$altura_linha = 2;
			$salto_linha = 1.5;			

			$pdf->SetXY(8,135);
			$pdf->cell(9, $altura_linha , 'Conta' ,0,0,'L');
			$pdf->SetXY(34,135);
			$pdf->cell(16, $altura_linha , 'Sl Anterior',0,0,'R');
			$pdf->SetXY(45,135);
			$pdf->cell(16, $altura_linha , 'Entradas',0,0,'R');		
			$pdf->SetXY(56,135);
			$pdf->cell(16, $altura_linha , 'Saidas',0,0,'R');		
			$pdf->SetXY(65,135);
			$pdf->cell(16, $altura_linha , 'Sl Atual',0,0,'R');
			
			
			$inicio_linha = 137;
			foreach($boletos['resumo'] as $sl){
				if($sl['Conta']=='Totais'){
					$pdf->SetFont('Arial','B');
				}
				$pdf->SetXY(8,$inicio_linha);
				$pdf->cell(9, $altura_linha, $sl['Conta'],0,0,'L');
				
				$pdf->SetXY(34,$inicio_linha);
				$pdf->cell(16, $altura_linha , $sl['Sl Anterior'],0,0,'R');
				
				$pdf->SetXY(45,$inicio_linha);
				$pdf->cell(16, $altura_linha , $sl['Entradas'],0,0,'R');
				
				$pdf->SetXY(56,$inicio_linha);
				$pdf->cell(16, $altura_linha, $sl['Saidas'],0,0,'R');
				
				$pdf->SetXY(65,$inicio_linha);
				$pdf->cell(16, $altura_linha, $sl['Sl Atual'],0,0,'R');
				$inicio_linha += $salto_linha;
			}
			
			$pdf->SetFillColor(0, 0, 0);
			$pdf->i25(8, 279, $boleto['cod_barra']);
			
			if($nome_arquivo=='ddddd'){
				//verso
				$pdf->AddPage();	
				$pdf->Image('images/'.$marca_adm,38,102);
				$pdf->SetFont('Arial','B',10);
				$pdf->text(120,118,'Boleto de vencimento: '.$boleto['data_vencimento']);
				$pdf->text(37,159, $boleto['descricao_unidade']);
				$pdf->Rect(37, 120, 140, 35);
				
				$pdf->SetFont('Arial','B',10);
				$pdf->text(38,130,$boleto['sacado']);
				$pdf->text(38,136,$boleto['endereco1']);
				$pdf->text(38,142,$boleto['endereco2']);
			}			
			if(isset($boleto['boletos_em_aberto'])&&$boleto['boletos_em_aberto']>0){
								//informacao boleto - pode ser mensagem ou quantidade de boleto em aberto
				$pdf->SetFont('Arial','B',6.5);
				$pdf->SetXY(163,40);
				$pdf->SetFillColor(222, 222, 222);
				$pdf->MultiCell(37,3,utf8_decode("ATENÇÃO!\nEm ".date("d/m/Y")." a unidade possui ".$boleto['boletos_em_aberto'].($boleto['boletos_em_aberto']>1 ? " boletos" : " boleto")." em aberto. Acesso o site ou entre em contato."),0,'C',1);				
				$pdf->SetFillColor(222, 222, 222);		

			}else{
				if(isset($boleto['mensagem_adicional'])&&$boleto['mensagem_adicional']!=''){
					$pdf->SetFont('Arial','B',9);
					$pdf->SetXY(163,40);
					$pdf->SetFillColor(222, 222, 222);
					$pdf->MultiCell(37, 4,utf8_decode($boleto['mensagem_adicional']),0,'C',1);
				}
			}		
		    if(isset($boleto['info_serasa'])&&$boleto['info_serasa']>0){
				$pdf->Ln();
				$pdf->SetFont('Arial','B',6.5);			
				$pdf->SetXY(163,60);
				$pdf->MultiCell(37, 3,utf8_decode("ATENÇÃO!\nAtraso superior a ".$boleto['info_serasa']['dias']." dias será enviado ao órgão de restrição de crédito \n(SPC)-(".$boleto['info_serasa']['age_ago']."-".$boleto['info_serasa']['data'].")".$boleto['info_serasa']['dataAde']),0,'C',0);
			}

			$pdf->SetFillColor(0, 0, 0);
			if($nome_arquivo==''){
				//verso
				$pdf->AddPage();
				
				if($marca_adm!='marca-predial.png'){
					$pdf->Image('images/'.$marca_adm,38,102);
					$pdf->Rect(37, 122, 140, 40);
				}else{
					$pdf->Image('images/boleto/envelope-boleto.png',2.5,106.5,205,112);
				}

				if($boleto['possuiDescPontualidade']){
					if($boleto['idAdministradora']==1){
						$pdf->Image('images/boleto/comunica-02-capa-do-boleto-desc-pont.png',38,15,130,52);
					}else{
						$pdf->Image('images/boleto/comunica-02-capa-boleto-do-boleto-asp-desc-pont.png',38,15,130,52);					
					}
				}

				$pdf->SetFont('Arial','B',9);
				$pdf->text(118,120,'Boleto de vencimento: '.$boleto['data_vencimento']);
				$pdf->SetFont('Arial','I',8);
				$pdf->text(41,126,utf8_decode('Destinatário:'));
				$pdf->SetFont('Arial','',9);
				$pdf->text(41,130,$boleto['sacado']);
				$pdf->text(41,134,$boleto['endereco1']);
				$pdf->text(41,138,$boleto['endereco2']);
				$pdf->text(41,142, $boleto['descricao_unidade']);

				$pdf->SetFont('Arial','I',8);
				$pdf->text(41,152,'Remetente:');
				$pdf->SetFont('Arial','',9);
				$pdf->text(41,156,$boleto['cedente']);
				$pdf->text(41,160,$boleto['endereco_cedente']);
			}

		}
		
		if($nome_arquivo!='')
			$pdf->Output("images/arquivos/".$nome_arquivo,"F");
		else
			$pdf->Output();
			
	}

	public function gerar_carne($bol,$gerar_verso=false,$filename=""){		
		$pdf = new PDF_i25();
		$bols = array();		
		if(isset($bol[0])){
			$bols = $bol;			
		}else{
			$bols[] = $bol;
		}		
		$boletos_split = array_chunk($bols,2);
		foreach ($boletos_split as $bols){
			$pdf->AddPage();
			$cont = 1;
			foreach ($bols as $boleto){
				if($cont==1){
					#posicao acima
					$add_incremento_alt = -144;
					$altura_bol_sacado = 5;
					$altura_bol_cedente = 59;
					$altura_ico_banco_bb1 = 9;
					$altura_ico_banco_bb2 = 64;
					$altura_ico_banco_itau1 = 5;
					$altura_ico_banco_itau2 = 60;
					$altura_cod_barras = 135.5;
				}else{
					#posicao abaixo
					$add_incremento_alt = 2;
					$altura_bol_sacado = 151;
					$altura_bol_cedente = 205;
					$altura_ico_banco_bb1 = 155;
					$altura_ico_banco_bb2 = 209;
					$altura_ico_banco_itau1 = 151;
					$altura_ico_banco_itau2 = 205;
					$altura_cod_barras = 281.5;
				}
				$cont++;
				$pdf->Image('images/boleto/boleto_sacado_registrado_carne.png',6,$altura_bol_sacado,-225);
				$pdf->Image('images/boleto/boleto_cedente_registrado_carne.png',6,$altura_bol_cedente,-225);

				if($boleto['banco']=='001'){
					//LOGOMARCA BANCO
					$pdf->Image('images/boleto/icone_bb.png',8,$altura_ico_banco_bb1,38);
					$pdf->Image('images/boleto/icone_bb.png',8,$altura_ico_banco_bb2,38);
					$boleto['codigo_banco'] = '001-9';	
					$boleto['moeda'] = "R$";
				}

				if($boleto['banco']=='341'){
					//LOGOMARCA BANCO
					$pdf->Image('images/boleto/logoitau.png',8,$altura_ico_banco_itau1,38);
					$pdf->Image('images/boleto/logoitau.png',8,$altura_ico_banco_itau2,38);
					$boleto['especie_doc'] = "";
					$boleto['moeda'] = "Real";
					$boleto['codigo_banco'] = '341-7';
				}
				
				if($boleto['banco']=='001'){#Bnaco do Brasil
					$boleto['local_pagamento'] = 'PAGÁVEL EM QUALQUER BANCO';
				}else{
					#outros bancos
					$boleto['local_pagamento'] = 'PAGÁVEL EM QUALQUER BANCO ATÉ O VENCIMENTO';
				}	
				
				$boleto = $this->utf8dec($boleto);			
				#parte um boleto
				$incremento_altura = 24 + $add_incremento_alt;
				$pdf->SetFont('Arial','B',8);
				$pdf->text(10,141+$incremento_altura,$boleto['cedente']);
				$pdf->text(10,144.8+$incremento_altura,$boleto['endereco_cedente']);
				$pdf->text(131,141+$incremento_altura,$boleto['agencia'].' / '.$boleto['conta']);
				$pdf->text(184,141+$incremento_altura,$boleto['data_vencimento'], 'R');
				$pdf->text(13,151+$incremento_altura,$boleto['data_documento']);
				$pdf->text(49,151+$incremento_altura,$boleto['numero_documento'], 'C');
				$pdf->text(86,151+$incremento_altura,$boleto['especie_doc'], 'C');
				$pdf->text(108,151+$incremento_altura,$boleto['aceite'], 'C');
				$pdf->text(131,151+$incremento_altura,$boleto['data_processamento'], 'C');
				$pdf->text(171.5,151+$incremento_altura,$boleto['nosso_numero'], 'R');
				$pdf->text(66,156.5+$incremento_altura,$boleto['moeda'], 'C');
				$pdf->text(40,156.5+$incremento_altura,$boleto['carteira'].$boleto['variacao_carteira'], 'C');
				$pdf->SetXY(169,153.5+$incremento_altura);
				$pdf->cell(30,4,$boleto['valor_boleto'], 0,0,'R');
				$pdf->text(9,162+$incremento_altura,$boleto['demonstrativo1'], 'L');
				$pdf->text(9,167+$incremento_altura,$boleto['sacado']);
				$pdf->text(9,170+$incremento_altura,$boleto['demonstrativo2']);
				$pdf->text(9,172.5+$incremento_altura,$boleto['endereco1'].$boleto['endereco2']);
				$pdf->SetFont('Arial','B',18);
				$pdf->text(51,134+$incremento_altura,$boleto['codigo_banco'], 'L');
				$pdf->SetFont('Arial','B',12.5);
				$pdf->text(75,134+$incremento_altura,$boleto['linha_digitavel'], 'L');
				$pdf->SetFont('Arial','B',10);
				$pdf->text(95,167+$incremento_altura,$boleto['nome_condominio'], 'L');
				
				#parte dois boleto
				$incremento_altura = 9 + $add_incremento_alt;
				$pdf->SetFont('Arial','B',18);			
				$pdf->text(51,203+$incremento_altura,$boleto['codigo_banco'], 'L');
				$pdf->SetFont('Arial','B',12.5);
				$pdf->text(75,203+$incremento_altura,$boleto['linha_digitavel'], 'L');
				$pdf->SetFont('Arial','B',8);
				$pdf->text(9,210+$incremento_altura,$boleto['local_pagamento'], 'L');
				$pdf->text(184,210+$incremento_altura,$boleto['data_vencimento'], 'R');
				$pdf->text(9,215.6+$incremento_altura,$boleto['cedente'], 'L');
				$pdf->text(175,215.6+$incremento_altura,$boleto['agencia'].' / '.$boleto['conta'], 'R');
				$pdf->text(13,221.5+$incremento_altura,$boleto['data_documento']);
				$pdf->text(49,221.5+$incremento_altura,$boleto['numero_documento'], 'C');
				$pdf->text(86,221.5+$incremento_altura,$boleto['especie_doc'], 'C');
				$pdf->text(110,221.5+$incremento_altura,$boleto['aceite'], 'C');
				$pdf->text(133,221.5+$incremento_altura,$boleto['data_processamento'], 'C');
				$pdf->text(171.5,221.5+$incremento_altura,$boleto['nosso_numero'], 'R');
				$pdf->text(40,227+$incremento_altura,$boleto['carteira'].$boleto['variacao_carteira'], 'C');
				$pdf->text(66,227+$incremento_altura,$boleto['moeda'], 'C');			
				$pdf->SetXY(169,224.5+$incremento_altura);
				$pdf->cell(30,4,$boleto['valor_boleto'], 0,0,'R');
				$pdf->SetFont('Arial','',7);
				$pdf->text(9,234+$incremento_altura,$boleto['demonstrativo2']);
				$pdf->text(9,238+$incremento_altura,$boleto['demonstrativo3']);
				$pdf->text(9,242+$incremento_altura,$boleto['instrucoes1']);
				$pdf->text(9,246+$incremento_altura,$boleto['instrucoes2']);
				$pdf->SetFont('Arial','B',9);
				$pdf->text(9,250+$incremento_altura,$boleto['instrucoes3']);
				$pdf->SetFont('Arial','',7);
				$pdf->text(9,254+$incremento_altura,$boleto['instrucoes4']);			
				$pdf->text(9,262+$incremento_altura,$boleto['sacado']);
				$pdf->text(9,265+$incremento_altura,$boleto['endereco1']);
				$pdf->text(9,268+$incremento_altura,$boleto['endereco2']);
				$pdf->text(110,268+$incremento_altura,$boleto['cpf_cnpj']);

				if(isset($boleto['detalhes_boleto'])){
					$pdf->SetFont('Arial','',6);
					$linha_detalhe = 231+$incremento_altura;
					foreach($boleto['detalhes_boleto'] as $detalhe){						
						$pdf->text(70,$linha_detalhe,$detalhe['descricao'].": ".$detalhe['valor']);						
						$linha_detalhe+=2.2;
					}
				}

				$pdf->SetFillColor(0, 0, 0);
				$pdf->i25(10, $altura_cod_barras, $boleto['cod_barra']);
				$pdf->SetFont('Arial','B',10);
				$pdf->text(95,261+$incremento_altura,$boleto['nome_condominio'], 'L');
			}
		}
		$pdf->Output($filename,"I");
	}
}
?> 