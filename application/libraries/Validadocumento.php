<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Validadocumento{
	var $documentos = array();
	var $pdftxt = null;
	/*CASO SEJA NA APLICAÇÂO DE TESTES*/
	//var $path = '/home/classecon.com.br/images/arquivos/digitalizacao/';
	/*CASO SEJA NA PRODUÇÂO*/
	var $path = '/home/classecon.com.br/images/arquivos/digitalizacao/';	
	var $condominios = array();
	var $fornecedores = array();
	function __construct($params){     
		$this->pdftxt = $params[0];
		$this->condominios = apc_fetch('condominios');
		$this->fornecedores = apc_fetch('fornecedores');
	}

	public function setDocumentos($documentos){
		$this->documentos = $documentos;
	}

	public function getTxtDoc(){
		$arquivos = $this->documentos;
		if(count($arquivos)){
			$array = array();
			foreach ($arquivos as $file){
				$fileaa =  $this->path.$file;
				if(file_exists($fileaa) && !is_dir($fileaa)){
				 	$paginas = $this->pdftxt->getPaginas($fileaa);			
				 	$str = $paginas[0];

				 	$matches = array();
				 	$hasCond = false;
				 	foreach ($this->condominios as $val) {
				 		if($val['documento']!=''){
				 			$var = str_replace('/','\/',$val['documento']);
				 			$found = preg_match('/('.$var.')/', $str, $matches);
				 			if($found!=''){
				 				$array['condominio'] = $val['id'];
				 				$hasCond = true;
				 				break;
				 			}
				 		}
				 		if($hasCond==false){
				 			$var = str_replace('/','\/',$val['nome']);
				 			$found = preg_match('/('.$var.')/', $str, $matches);
				 			if($found!=''){
				 				$array['condominio'] = $val['id'];
								//$hasCond = true;
				 				break;
				 			}
				 		}
				 	}
				 	$hasFor = false;
			 		foreach ($this->fornecedores as $val) {
				 		if($val['documento']!=''){
					 		$var = str_replace('/','\/',$val['documento']);
							$found = preg_match('/('.$var.')/', $str, $matches);
							if($found!=''){
								$array['fornecedor'] = $val['id'];
								$hasFor = true;
								break;
							}
						}
						if($hasFor==true){
							$var = str_replace('/','\/',$val['nome']);
							$found = preg_match('/('.$var.')/', $str, $matches);
							if($found!=''){
								$array['fornecedor'] = $val['id'];
								//$hasFor = true;
								break;
							}
						}
				 	}	
				 	$res = $this->buscaInteligente($str);
				 	$array = array_merge($array,$res); 
				}
			}
			return $array;
		}else{
			return array();
		}
	}

	public function getTxtAllDocs(){
		$arquivos = directory_map($this->path,true,true);
		$imagens = array();
		if(count($arquivos)){
			foreach ($arquivos as $file){
				//pre($file);
				$fileaa =  $path.$file;
				if(file_exists($fileaa) && !is_dir($fileaa)){
				 	$paginas = $this->pdftxt->getPaginas($fileaa);			
				 	$str = $paginas[0];
				 	$matches = array();
				 	//echo($str);
					
					//$type = preg_match_all('/((DARF|GPS)|(CEFIC))/', $str, $matches); #acha tipo
					
					$arr = $this->buscaInteligente($str);
					//pre($arr);
					echo '<hr>';
				}else{
				 	echo "Arquivo nao encontrado ou é uma pasta.";
				}
			}
		}else{
			echo "<br>Nenhum arquivo na pasta.";
		}
	}

	public function buscaInteligente($str){
		$dados = array();
		$matches = array();
		preg_match_all('/(\d{3}.?\d{3}.\d{3}\-\d{2})/', $str, $matches); #acha cpf
		if(count($matches)>0){
			if(count($matches[0])>0){
				$dados['cpf'] = $matches[0];
			}
		}
		if(isset($dados['cpf']) && count($dados['cpf'])>0){
			$dados['cpf'] = array_unique($dados['cpf']);
		}

		$matches = array();
		preg_match_all('/(\d{2}.\d{3}.\d{3}\/\d{4}-\d{2})/', $str, $matches); #acha cnpj
		if(count($matches)>0){
			if(count($matches[0])>0){
				$dados['cnpj'] = $matches[0];
			}
		}
		if(isset($dados['cnpj']) && count($dados['cnpj'])>0){
			$dados['cnpj'] = array_unique($dados['cnpj']);
		}

		$matches = array();
		//(^(8|0)\d{10}([\s-])?\d{1}\s?\d{11}([\s-])?\d{1}\s?\d{11}([\s-])?\d{1}\s?\d{11}([\s-])?\d{1})
		preg_match('/(\d{11}([\s-])?\d{1}\s?\d{11}([\s-])?\d{1}\s?\d{11}([\s-])?\d{1}\s?\d{11}([\s-])?\d{1})/', $str, $matches); #acha codigo concessionarias
		if(count($matches)>0){
			$dados['cod_concess'] = $matches[0];
		}

		$matches = array();
		//([^8]\d{5}[.]?\d{5}\s?\d{5}[.]?\d{6}\s?\d{5}[.]?\d{6}\s?\d{1}\s?\d{14})\b
		preg_match("/(\d{5}[.]?\d{5}\s?\d{5}[.]?\d{6}\s?\d{5}[.]?\d{6}\s?\d{1}\s?\d{14})/", $str, $matches); #acha codigo de barras
		if(count($matches)>0){
			$type = (int) substr($matches[0], 0, 1);
			if($type!=8){
				$dados['cod_barras'] = $matches[0];
			}
		}

		if(!isset($dados['cod_barras'])){
			$type_concess = 0;
			if(isset($dados['cod_concess'])){
				$type_concess = (int) substr($dados['cod_concess'], 0, 2);
			}else{
				$matches = array();
				preg_match_all('/((\d{1,})?\.?(\d{1,})?\.?\d{1,}\,\d{1,})/', $str, $matches); #acha valores
				if(count($matches)>0){
					$dados['valor'] = $matches[0];
				}
				if(isset($dados['valor']) && count($dados['valor'])>0){
					$dados['valor'] = array_unique($dados['valor']);
					$array = array();
					foreach ($dados['valor'] as $v) {
						$array[] = moeda2BD($v);
					}
					$dados['valor'] = max($array);
				}
			}

			$matches = array();
			preg_match_all('/(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/(19|20)\d\d/', $str, $matches); #acha data
			if(count($matches)>0){
				$dados['data'] = $matches[0];
			}
			if(isset($dados['data']) && count($dados['data'])>0){
				$dados['data'] = array_unique($dados['data']);
				$aux = array();
				foreach ($dados['data'] as $value) {
					$aux[] = data2BD($value); 
				}
				$dados['data'] = $aux;
			}

			if($type_concess==85 | $type_concess==84){
				$dados['data'] = max($dados['data']);
			}else{
				/*
				81. Prefeituras;
				82. Saneamento;
				83. Energia Elétrica e Gás;
				84. Telecomunicações;
				85. Órgãos Governamentais;
				86. Carnes e Assemelhados ou demais Empresas / Órgãos que serão identificadas através do CNPJ.
				87. Multas de trânsito
				89. Uso exclusivo do banco
				*/
				if($type_concess==81 | $type_concess==82 | $type_concess==83 | $type_concess==86 | $type_concess==87 | $type_concess==89){
					if(count($dados['data'])>1){
						$aux = array();
						$max = max($dados['data']);
						foreach ($dados['data'] as $val) {
							if(!in_array($val,$aux) && $max!=$val){
								if(date($max)<date($val)){
									$aux[] = $val;
									$max = $val;
								}
							}
						}
						$dados['data'] = (count($aux)>0) ? max($aux) : $max ; 
					}else{
						$dados['data'] = $dados['data'][0];
					}
				}else{
					$dados['data'] = (count($dados['data'])>0) ? $dados['data'] : '' ; 
				}
			}
		}

		return $dados;
	}
}