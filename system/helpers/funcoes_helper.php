<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Arrays do feriado do ano
 *
 * 
 *
 * @access	public
 * @param	string	ano
 * @return	array
 */
if ( ! function_exists('feriadosAno')){

	function feriadosAno($ano = null){
		if ($ano === null){
			$ano = intval(date('Y'));
		}
	 
		$pascoa     = easter_date($ano); // Limite de 1970 ou após 2037 da easter_date PHP
		$dia_pascoa = date('j', $pascoa);
		$mes_pascoa = date('n', $pascoa);
		$ano_pascoa = date('Y', $pascoa);
		
		$feriados = array(
			// Tatas Fixas dos feriados Nacionail Basileiras
			mktime(0, 0, 0, 1,  1,   $ano), // Confraternização Universal - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 4,  21,  $ano), // Tiradentes - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 5,  1,   $ano), // Dia do Trabalhador - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 9,  7,   $ano), // Dia da Independência - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 10,  12, $ano), // N. S. Aparecida - Lei nº 6802, de 30/06/80
			mktime(0, 0, 0, 11,  2,  $ano), // Todos os santos - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 11, 15,  $ano), // Proclamação da republica - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 12, 25,  $ano), // Natal - Lei nº 662, de 06/04/49
			
			// These days have a date depending on easter
			mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 48,  $ano_pascoa),//2ºferia Carnaval
			mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 47,  $ano_pascoa),//3ºferia Carnaval	
			mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 2 ,  $ano_pascoa),//6ºfeira Santa  
			mktime(0, 0, 0, $mes_pascoa, $dia_pascoa     ,  $ano_pascoa),//Pascoa
			mktime(0, 0, 0, $mes_pascoa, $dia_pascoa + 60,  $ano_pascoa),//Corpus Cirist
		);
		
		sort($feriados);
		foreach($feriados as $key=>$val){
			$feriados[$key] = date("Y-m-d",$val);
		}
		$feriados[] = '2017-12-30'; #recesso dos bancos final de ano
		//$feriados[] = '2017-08-16'; #aniversario de teresina
		//$feriados[] = '2017-08-15'; #aniversario de fortaleza
		return $feriados;
	}
}

/**
 * Retorno se o dia e' util
 *
 * 
 *
 * @access	public
 * @param	date	data
 * @return	boolean
 */
if ( ! function_exists('isDiaUtil')){
	function isDiaUtil($data){
		$timestamp = strtotime($data);
		if(in_array($data,feriadosAno(date("Y",strtotime($data)))))
			return false;
		$dia = date('N', $timestamp);		
		if ($dia >= 6)
			return false;
		else
			return true;		
	}
}

/**
 * Retorna o proximo dia util a uma data
 *
 * 
 *
 * @access	public
 * @param	date	date
 * @param	string	saida
 * @return	string
 */
if ( ! function_exists('proximoDiaUtil')){
	function proximoDiaUtil($data, $saida = 'Y-m-d'){
		$data = new DateTime($data);
		while(!isDiaUtil($data->format('Y-m-d')))
			$data->modify('+1 day');
		
		return $data->format($saida);
	}
}

if ( ! function_exists('data2input')){
	function data2input($data, $saida = 'd/m/Y'){
		if($data=='') return '';
		$data = new DateTime($data);
		return $data->format($saida);
	}
}

if ( ! function_exists('data2BD')){
	function data2BD($data){
		if(!validateDate($data)){
			if($data<>''){
				$txt = explode("/",$data);
				if(count($txt)>0){
					return $txt[2]."-".$txt[1]."-".$txt[0];
				}else{
					return $data;
				}
			}
		}else{
			return $data;
		}
		return NULL;
	}
}

if ( ! function_exists('moeda2Input')){
	function moeda2Input($num){
		//if($num!=''&&is_numeric($num)){
		if(is_numeric($num)){
			$num = number_format($num,2,',','.');
		}
		return $num;
	}
}

if ( ! function_exists('moeda2BD')){
	function moeda2BD($num){
		if($num!=''){	
			$num = str_replace(".","",$num);	
			$num = str_replace(",",".",$num);		
		}else $num = 0;
		return $num;
	}
}

if ( ! function_exists('echo_memory_usage')){
	function echo_memory_usage() { 
        $mem_usage = memory_get_usage(true);        
        if ($mem_usage < 1024) 
            return $mem_usage." bytes"; 
        elseif ($mem_usage < 1048576) 
            return round($mem_usage/1024,2)." kilobytes"; 
        else 
            return round($mem_usage/1048576,2)." megabytes";
    }
}

if ( ! function_exists('validateDate')){
	function validateDate($date, $format = 'Y-m-d'){
		if(is_array($date))
			return false;
	    $d = DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) == $date;
	}
}
/*
diferenca em dias
*/
if ( ! function_exists('dateDiff')){
	function dateDiff($date1, $date2){
		$time = new DateTime($date1);
		$diff = $time -> diff( new DateTime($date2),true);
		return $diff->days;
	}
}

/*
diferenca em dias positivo ou negativo
*/
if (! function_exists('dateDiff2')){
	function dateDiff2($date1, $date2){
		$data_inicial = $date1;
		$data_final = $date2;
		$time_inicial = strtotime($data_inicial);
		$time_final = strtotime($data_final);
		$diferenca = $time_final - $time_inicial;
		$dias = floor( $diferenca / (60 * 60 * 24));
		return $dias;
	}
}

/*
diferenca em dias
*/
if ( ! function_exists('dateDiff3')){
	function dateDiff3($date1, $date2){
		$time = new DateTime($date1);
		$diff = $time -> diff( new DateTime($date2),true);
		return $diff;
	}
}

/*
diferenca em dias positivo ou negativo
*/
function dias_uteis($datainicial,$datafinal=null){
	if (!isset($datainicial)) return false;
	$segundos_datainicial = strtotime(str_replace("/","-",$datainicial));
	if (!isset($datafinal)) $segundos_datafinal=time();
	else $segundos_datafinal = strtotime(str_replace("/","-",$datafinal));
	$dias = abs(floor(floor(($segundos_datafinal-$segundos_datainicial)/3600)/24 ) );
	$uteis=0;
	for($i=1;$i<=$dias;$i++){
	    $diai = $segundos_datainicial+($i*3600*24);
	    $w = date('w',$diai);
	    if ($w>0 && $w<6){ $uteis++; }
	}
	return $uteis;
}

if ( ! function_exists('add_date')){
	// function add_date($givendate,$day=0,$mth=0,$yr=0) {
		
	// 	$date = new DateTime($givendate);
	// 	if($day>0){
	// 		$date->add(new DateInterval('P'.$day.'D'));
	// 	}
	// 	if($mth>0){
	// 		$date->add(new DateInterval('P'.$mth.'M'));
	// 	}
	// 	if($yr>0){
	// 		$date->add(new DateInterval('P'.$yr.'Y'));
	// 	}
		
	// 	return $date->format('Y-m-d');

	// 	$cd = strtotime($givendate);
	// 	$newdate = date('Y-m-d', mktime(date('h',$cd),date('i',$cd), date('s',$cd), date('m',$cd)+$mth,date('d',$cd)+$day, date('Y',$cd)+$yr));
	// 	return $newdate;
 //    }

	function add_date($givendate,$day=0,$mth=0,$yr=0) {
		$cd = strtotime($givendate);
		$newdate = date('Y-m-d', mktime(date('h',$cd),date('i',$cd), date('s',$cd), date('m',$cd)+$mth,date('d',$cd)+$day, date('Y',$cd)+$yr));
		return $newdate;
    }
}

if ( ! function_exists('getUltimoDiaMes')){
	function getUltimoDiaMes($date){
		$mes = date('m',strtotime($date));
		$ano = date('Y',strtotime($date));
		return "$ano-$mes-".cal_days_in_month(CAL_GREGORIAN, $mes , $ano);
	}	
}

if ( ! function_exists('mask')){
	function mask($val, $mask){
		$maskared = '';
		$k = 0;
		for($i = 0; $i<=strlen($mask)-1; $i++){
			if($mask[$i] == '#'){
				if(isset($val[$k]))
					$maskared .= $val[$k++];
			}else{
				if(isset($mask[$i]))
					$maskared .= $mask[$i];
			}
		}
		return $maskared;
	}
}

if ( ! function_exists('pre')){
	function pre($obj){
		echo "<pre>";	
		print_r($obj);
		echo "</pre>";
	}
}

if ( ! function_exists('linkOperadoraFone')){
	function linkOperadoraFone($numero){
		if($numero!=''){
			$numero = str_replace(" ","",$numero);
			return '<a href="http://'.$_SERVER['HTTP_HOST'].'/migrar/consutaloperadora/'.$numero.'" class="glyphicon glyphicon-earphone" target="_blank">&nbsp;</a>';
		}
		return "";
	}
}

if ( ! function_exists('validarEmail')){
	function validarEmail($email_a){
		if (filter_var($email_a, FILTER_VALIDATE_EMAIL)) {
		    return true;
		}
		return false;
	}
}

if ( ! function_exists('alerta')){
	function alerta($msn,$ok=false,$div='topo'){
		echo "<script type='text/javascript'>";
			if($ok){
				echo "alerta('$msn',true,'$div');";
			}else{
				echo "alerta('$msn',false,'$div');";
			}
		echo "</script>";
	}
}

/*
Autor:  Marcos Iran
Data:   27/05/2016
Hora:   

Alterado por:   Natanael Diego
Data alteracao: 27/05/2016
Hora alteracao: 17:39
Funcao: 		incluido a expressao regular.
*/ 
if ( ! function_exists('verificaCPF')){
	function verificaCPF($cpf){

		if (preg_match('/^[0-9]{3,3}([.]?[0-9]{3,3})([.]?[0-9]{3,3})([-]?[0-9]{2,2})?$/', $cpf)){
			$cpf        = preg_replace("/[^0-9]/", "", $cpf);
			$digitoUm   = 0;
			$digitoDois = 0;
			for($i = 0, $x = 10; $i <= 8; $i++, $x--){
				$digitoUm += $cpf[$i] * $x;
			}
			for($i = 0, $x = 11; $i <= 9; $i++, $x--){
				if(str_repeat($i, 11) == $cpf){
					return false;
				}
				$digitoDois += $cpf[$i] * $x;
			}
			$calculoUm  = (($digitoUm%11) < 2) ? 0 : 11-($digitoUm%11);
			$calculoDois = (($digitoDois%11) < 2) ? 0 : 11-($digitoDois%11);
			if($calculoUm <> $cpf[9] || $calculoDois <> $cpf[10]){
				return false;
			}
			return true;
		}else{
			return false;
		}

	}
}

/*
Autor:  Marcos Iran
Data:   27/05/2016
Hora:   

Alterado por:   Natanael Diego
Data alteracao: 27/05/2016
Hora alteracao: 17:39
Funcao: 		incluido a expressao regular.
*/ 

if ( ! function_exists('verificaCNPJ')){
	function verificaCNPJ($cnpj){

		if (preg_match('/^[0-9]{2}([.]?[0-9]{3})([.]?[0-9]{3})\/\d{4}([-]?[0-9]{2})$/', $cnpj)){

			$cnpj = trim($cnpj);
			$cnpj = str_replace(".", "", $cnpj);
			$cnpj = str_replace(",", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace("/", "", $cnpj);

			if (strlen($cnpj) <> 14) return 0;

			$soma1 = ($cnpj[0] * 5) +
			($cnpj[1] * 4) +
			($cnpj[2] * 3) +
			($cnpj[3] * 2) +
			($cnpj[4] * 9) +
			($cnpj[5] * 8) +
			($cnpj[6] * 7) +
			($cnpj[7] * 6) +
			($cnpj[8] * 5) +
			($cnpj[9] * 4) +
			($cnpj[10] * 3) +
			($cnpj[11] * 2);
			$resto = $soma1 % 11;
			$digito1 = $resto < 2 ? 0 : 11 - $resto;
			$soma2 = ($cnpj[0] * 6) +
			($cnpj[1] * 5) +
			($cnpj[2] * 4) +
			($cnpj[3] * 3) +
			($cnpj[4] * 2) +
			($cnpj[5] * 9) +
			($cnpj[6] * 8) +
			($cnpj[7] * 7) +
			($cnpj[8] * 6) +
			($cnpj[9] * 5) +
			($cnpj[10] * 4) +
			($cnpj[11] * 3) +
			($cnpj[12] * 2);
			$resto = $soma2 % 11;
			$digito2 = $resto < 2 ? 0 : 11 - $resto;
			return (($cnpj[12] == $digito1) && ($cnpj[13] == $digito2));
		}else{
			return 0;
		}
	}
}

if (!function_exists('_dateBetween')){
	function _dateBetween($dataInicial, $dataFinal, $dataComparada){
		if($dataInicial == null || $dataFinal == null || $dataComparada == null ){
			return false;
		}
		
		$maiorIgual = compareData($dataComparada, $dataInicial) >= 0; 
		$menorIgual = compareData($dataComparada, $dataFinal) <= 0; 
		return $maiorIgual && $menorIgual;
	}
}

if (!function_exists('compareData')){
	function compareData($data1, $data2) {
		$ano1 = @date("Y", strtotime($data1));
		$ano2 = @date("Y", strtotime($data2)); 
		$mes1 = @date("m", strtotime($data1)); 
		$mes2 = @date("m", strtotime($data2)); 
		$dia1 = @date("d", strtotime($data1));
		$dia2 = @date("d", strtotime($data2));

		if ($ano1 == $ano2) { // Se os anos são iguais, verificamos os meses
			if ($mes1 == $mes2) { // Se os meses são iguais, verificamos os dias
				if ($dia1 == $dia2) {
					return 0;
				} // Se os dias são iguais, as datas são iguais
				else if ($dia1 > $dia2) {
					return 1;
				} // Se dia1 maior que dia2, data1 vem apos data2
				else {
					return -1;
				} // Caso contrário, data2 vem apos data1
			} else if ($mes1 > $mes2) { // Se $mes1 é maior que mes2, então data1 vem apos data2
				return 1;
			} else { 
				return -1;
			} // Caso contrário, data2 vem apos data1
		} else if ($ano1 > $ano2) {
			return 2;
		} else { // Se ano1 é maior que ano2, então data1 vem apos data2
			return -1;
		} // Caso contrário, data2 vem apos data1
	}
}
if (!function_exists('abrePagina')){
	function abrePagina($pagina,$div="container",$uid=""){
		echo "<script type='text/javascript'>";		
		echo "abrir_div('$pagina','$div');";
		echo "</script>";
	}
}

// a function for comparing two float numbers  
// float 1 - The first number  
// float 2 - The number to compare against the first  
// operator - The operator. Valid options are =, <=, <, >=, >, <>, eq, lt, lte, gt, gte, ne  
if (!function_exists('compareFloatNumbers')){
	function compareFloatNumbers($float1, $float2, $operator='=')  
	{  
	    // Check numbers to 5 digits of precision  
	    $epsilon = 0.00001;  
	      
	    $float1 = (float)$float1;  
	    $float2 = (float)$float2;  
	      
	    switch ($operator)  
	    {  
	        // equal  
	        case "=":  
	        case "eq":  
	        {  
	            if (abs($float1 - $float2) < $epsilon) {  
	                return true;  
	            }  
	            break;    
	        }  
	        // less than  
	        case "<":  
	        case "lt":  
	        {  
	            if (abs($float1 - $float2) < $epsilon) {  
	                return false;  
	            }  
	            else  
	            {  
	                if ($float1 < $float2) {  
	                    return true;  
	                }  
	            }  
	            break;    
	        }  
	        // less than or equal  
	        case "<=":  
	        case "lte":  
	        {  
	            if (compareFloatNumbers($float1, $float2, '<') || compareFloatNumbers($float1, $float2, '=')) {  
	                return true;  
	            }  
	            break;    
	        }  
	        // greater than  
	        case ">":  
	        case "gt":  
	        {  
	            if (abs($float1 - $float2) < $epsilon) {  
	                return false;  
	            }  
	            else  
	            {  
	                if ($float1 > $float2) {  
	                    return true;  
	                }  
	            }  
	            break;    
	        }  
	        // greater than or equal  
	        case ">=":  
	        case "gte":  
	        {  
	            if (compareFloatNumbers($float1, $float2, '>') || compareFloatNumbers($float1, $float2, '=')) {  
	                return true;  
	            }  
	            break;    
	        }  
	        case "<>":  
	        case "!=":  
	        case "ne":  
	        {  
	            if (abs($float1 - $float2) > $epsilon) {  
	                return true;  
	            }  
	            break;    
	        }  
	        default:  
	        {  
	            die("Unknown operator '".$operator."' in compareFloatNumbers()");     
	        }  
	    }  
	      
	    return false;  
	}
}
if (!function_exists('adiciona_9_digito')){
	function adiciona_9_digito($tel){
		//verificando se é celular
		$array_pre_numero = array ("9","8","7");
		// retirando espaços
		$tel = trim($tel);
		$telefone = "";
		// seria melhor cirar uma white list.
		// tratando manualmente
		$tel = str_replace("-", "", $tel);
		$tel = str_replace("(", "", $tel);
		$tel = str_replace(")", "", $tel);
		$tel = str_replace("_", "", $tel);
		$tel = str_replace(" ", "", $tel);
		$tel = str_replace(".", "", $tel);
		//---------------------

		$tamanho = strlen($tel);

		// if(substr($tel,0,2)==61){			
		// 	return $tel;
		// }

		// maior
		if($tamanho  > '10'){
			// não faz nada
			$telefone = $tel;
		}		
		//igual
		if($tamanho == '10'){
			$verificando_celular = substr($tel, 2, 1);
			if(in_array($verificando_celular, $array_pre_numero)){
				$telefone.= substr($tel, 0, 2);
				$telefone.= "9"; // nono digito
				$telefone.= substr($tel, 2);
			}else{
				$telefone = $tel;
			}
		}
		//menor
		if($tamanho < '10'){
			// não faz nada
			$telefone = $tel;
		}
		if($tamanho == '8'){
			// não faz nada
			$telefone = adiciona_9_digito("86".$tel);
		}
		
		return $telefone;
	}
}

if (!function_exists('removeDeletaArquivo')){
	function removeDeletaArquivo($file){
		try{
    		unlink($file);
    	} catch (Exception $e) {
    		log_message("error", 'Erro ao apagar arquivo '.$e->getMessage());
    	}
	}
}

if ( ! function_exists('selectDatas')){
	function selectDatas($id="",$name="",$values=array(),$title="Digite um Data",$obrigatorio=true,$multiple=false,$_str="",$class="")
	{
		$ci =& get_instance();		
		$elements = $ci->config->item('datasClassecon');		
		$str = "<select $_str id=\"$id\" name=\"$name\" title=\"$title\" ".($obrigatorio?'required':'')." class=\"input-select2 form-control $class\" ".($multiple ? 'multiple' : '').">";
		$str .= "<option value=\"\">$title</option>";		
		foreach($elements as $key=>$val){			
			$str .= "<option ".(in_array($key,$values)||in_array($val,$values) ? 'selected' : '')." value=\"".$key."\">".str_pad($val,2,'0',STR_PAD_LEFT)."</option>";
		}
		$str .= "</select><script>exibe_select2('$id');</script>";
		return $str;
	}
}

/**
 * 
 *
 * Colocado aqui por: Natanael Diego
 * Data: 04/07/2017
 * Hora: 17:42
 * 
 * Library's ordenacao.
 *
 * @access	public
 * @param	array	the URL
 * @param	string	coluna para ordenar
 * @return	string	ordem
 */
if ( ! function_exists('array_sort_by_column'))
{
	function array_sort_by_column(&$arr, $col, $dir = 'desc') {
		
		if(strtolower($dir)=='desc')
			$dir = SORT_DESC;
		else
			$dir = SORT_ASC;
			
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}
	
		return array_multisort($sort_col, $dir, $arr);
	}
}
if ( ! function_exists('modulo_10ItauListagem')){
	function modulo_10ItauListagem($num) { 
		$numtotal10 = 0;
        $fator = 2;

        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo (falor 10)
            // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
            $temp = $numeros[$i] * $fator; 
            $temp0=0;
            foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
            $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2; // intercala fator de multiplicacao (modulo 10)
            }
        }
		
        // várias linhas removidas, vide função original
        // Calculo do modulo 10
        $resto = $numtotal10 % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }
		
        return $digito;		
	}
}
if ( ! function_exists('geraSenha')){
	/**
	* Função para gerar senhas aleatórias
	* @param integer $tamanho Tamanho da senha a ser gerada
	* @param boolean $maiusculas Se terá letras maiúsculas
	* @param boolean $numeros Se terá números
	* @param boolean $simbolos Se terá símbolos
	* @return string A senha gerada */
  	function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true){
	    $lmin = 'abcdefghijklmnopqrstuvwxyz';
	    $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $num = '1234567890';
	    $retorno = '';
	    $caracteres = '';
	    $caracteres .= $lmin;
	    if ($maiusculas) $caracteres .= $lmai;
	    if ($numeros) $caracteres .= $num;
	    $len = strlen($caracteres);
	    for ($n = 1; $n <= $tamanho; $n++) {
	        $rand = mt_rand(1, $len);
	        $retorno .= $caracteres[$rand-1];
	    }
	    return $retorno;
  	}
}
if ( ! function_exists('hiddenString')){
    /**
    * Oculta parte de un string
    * @param  string  $str   Texto a ocultar
    * @param  integer $start Cuantos caracteres dejar sin ocultar al inicio
    * @param  integer $end   Cuantos caracteres dejar sin ocultar al final
    * @author Jodacame 
    * @return string */
    function hiddenString($str, $start = 1, $end = 1){
        $len = strlen($str);
        return substr($str, 0, $start) . str_repeat('*',$len - ($start + $end)) . substr($str, $len - $end+1, $end);
    }
}

if ( ! function_exists('valorPorExtenso')){
    /**
    * Oculta parte de un string
    * @param  double  $valor 
    * @param  bolean $bolExibirMoeda 
    * @param  bolean $bolPalavraFeminina 
    * @author Ramon Dev 
    * @return string */
    function valorPorExtenso( $valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false ){
        //$valor = self::removerFormatacaoNumero( $valor );
        
        $singular = null;
        $plural = null;

        if ( $bolExibirMoeda ){
            $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
        }else{
            $singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural = array("", "", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
        }

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");


        if ( $bolPalavraFeminina ){
            if ($valor == 1) {
                $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
            }else {
                $u = array("", "um", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
            }
            $c = array("", "cem", "duzentas", "trezentas", "quatrocentas","quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");
        }

        $z = 0;

        $valor = number_format( $valor, 2, ".", "." );
        $inteiro = explode( ".", $valor );

        for ( $i = 0; $i < count( $inteiro ); $i++ ) {
            for ( $ii = mb_strlen( $inteiro[$i] ); $ii < 3; $ii++ ) {
                $inteiro[$i] = "0" . $inteiro[$i];
            }
        }

        // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
        $rt = null;
        $fim = count( $inteiro ) - ($inteiro[count( $inteiro ) - 1] > 0 ? 1 : 2);
        for ( $i = 0; $i < count( $inteiro ); $i++ ){
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
            $t = count( $inteiro ) - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ( $valor == "000"){
                $z++;
            }elseif ( $z > 0 ){
                $z--;
            }
                
            if ( ($t == 1) && ($z > 0) && ($inteiro[0] > 0) ){
                $r .= ( ($z > 1) ? " de " : "") . $plural[$t];
            }
                
            if ( $r ){
                $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
            }
        }

        $rt = mb_substr( $rt, 1 );

        return($rt ? trim( $rt ) : "zero");
    }
}
/* End of file array_helper.php */
/* Location: ./system/helpers/array_helper.php */