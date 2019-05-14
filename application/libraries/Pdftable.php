<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
include_once 'plugins/php/fpdf_table.php';
 
/*class pdf {
    
    function pdf()
    {
        $CI = & get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }
 
    function load($param=NULL)
    {
        		
		include_once '/home/panet/classecon.panet.com.br/plugins/php/mpdf_lib/mpdf.php';
         
        if ($params == NULL)
        {
            $param = '"en-GB-x","A4","","",10,10,10,10,6,3';         
        }
         
        return new mPDF($param);
    }
}*/

/*
	Alterado por:   Natanael Diego
	Data alteracao: 28/08/2017
	Hora alteracao: 17:04
	Funcao:         Foi incluido mais um campo chamado: txtHeaderEspecifico, esse campo foi incluido por causa do relatorio: demonstrativo de entrada e saida.
*/
class Pdf_table extends PDF_MC_Table{
	var $txtHeader = array();
	var $headerColunas;
	var $nomeRelatorio = "";
	var $notaDeRodape = "";
	var $logoAdministradora = "";
	var $txtHeaderEspecifico = false;//demonstrativo_entrada_saida
	
	function Pdftable(){
        $CI = & get_instance();		
        log_message('Debug', 'PDF_MC_Table class is loaded.');
    }
	
	// Page header
	function Header(){
		$this->SetY(2);
		$this->SetFont('arial','',7);
		$CI =& get_instance();
		$usuario = $CI->session->userdata('dados_usuario_logado');
		// $this->Cell(($this->CurOrientation=='L'?280:190),
		// 			5,
		// 			"Classecon ".$CI->config->item('versaoClassecon')." - ".date("d/m/Y H:i:s")." - ". utf8_decode("Usuário: ".$usuario['nomeLogado'].""),
		// 			0,
		// 			2,
		// 			'R');
		
		$this->Ln(0);
		//$this->Ln(2);
		if($this->logoAdministradora!='')
			$this->Image($this->logoAdministradora,10,3,0,20);
		else
			$this->Image(base_url().'/images/predial-marca.jpg',10,3,40,20);
				
		$this->SetFont('arial','B',12);
		$this->SetDrawColor(255,255,255);
		#$this->SetDrawColor(1,1,1);
		
		$this->Cell(45,20,"",0,0,'C'); #logo
		//$this->Cell(45,15,"",1,0,'C'); // Natanael

		//$this->Cell(($this->CurOrientation=='L'?235:145),15,$this->nomeRelatorio,1,0,'C');
		$this->MultiCell(($this->CurOrientation=='L'?200:110),($this->CurOrientation=='L'?15:7.5),$this->nomeRelatorio,1,'C');
		
		if($this->CurOrientation=='L'){
			$this->setXY(255,9);
		}else{
			$this->setXY(165,9);
		}

		$this->SetFont('arial','',7);
		$this->MultiCell(35,3,"Classecon ".$CI->config->item('versaoClassecon')."\n".date("d/m/Y H:i:s")."\n". utf8_decode("Usuário: ".$usuario['nomeLogado'].""),1,'R'); #info
		//$this->setY(24);

		if($this->txtHeaderEspecifico){
			$this->setY(10);
		}else{
			$this->setY(15);
		}

		$this->SetFont('arial','B',12);
		//$this->Ln(15);
		//$this->Ln(2);
		$this->SetFont('arial','',9);

		if($this->txtHeaderEspecifico){			
			$this->setX(60);
			$str_d = array();
			for($i=0;$i<count($this->txtHeader);$i++){			
				//$this->Cell(190,5,utf8_decode($this->txtHeader[$i]),1,10,'RL');
				$str_d[] = utf8_decode($this->txtHeader[$i]);
			}
			$this->MultiCell(100,5,implode(', ',$str_d),0,'C');
			$this->Ln(9);
		}else{
			$str_d = array();
			for($i=0;$i<count($this->txtHeader);$i++){			
				$str_d[] = utf8_decode($this->txtHeader[$i]);
			}
			$this->setX(90);
			//$this->SetDrawColor(0,0,0);
			//$this->Cell(80,5,implode(', ',$str_d),1,1,'C');
			$this->MultiCell(130,5,implode(', ',$str_d),0,'C');
			$this->Ln(5);
		}
		
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(255,255,255);
		if(count($this->headerColunas)>0){
			$this->SetFont('arial','B');
			$this->Row($this->headerColunas);
		}
		$this->SetFont('arial','',9);
		$this->SetDrawColor(1,1,1);
		$this->SetFillColor(1,1,1);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x,$y, ($this->CurOrientation=='L'?290:200), $y);
	
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(255,255,255);	
		$this->Ln(1);		
	}
	
	// Page footer
	function Footer(){		
		$this->SetDrawColor(1,1,1);
		$this->SetFillColor(1,1,1);		
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x,($this->CurOrientation=='L'?198:285), ($this->CurOrientation=='L'?290:200), ($this->CurOrientation=='L'?198:285));
										
		// arial italic 8
		$this->SetFont('arial','I',8);
						
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		
		if($this->notaDeRodape!=''){
			$this->Cell(0,10,utf8_decode($this->notaDeRodape),0,0,'L');	
		}
						
		// Page number				
		$this->Cell(0,10,$this->PageNo(),0,0,'R');		
	}
			
}

/*
	Alterado por:   Natanael Diego
	Data alteracao: 28/08/2017
	Hora alteracao: 17:04
	Funcao:         Foi incluido mais um campo chamado: especifico, esse campo foi incluido por causa do relatorio: demonstrativo de entrada e saida.
*/
class Pdftable{		
	
	function gerarPDF($data, $w=null, $aligns=null, $filename = null, $titulo="Relatório", $detalhes = null, $rodape=null,$orientation = "L", $fontSize = '',$anexos_array=array(),$imagens = array(),$logoAdministradora='',$saida="I",$assinaturas_array=array(), $especifico = ''){
		
		$pdf = new Pdf_table($orientation, 'mm', 'A4');
		if($fontSize!='')
			$pdf->SetFont('arial','',$fontSize);
		$pdf->nomeRelatorio = utf8_decode($titulo);	
		
		if($logoAdministradora!='')	
			$pdf->logoAdministradora = $logoAdministradora;
		
		if($especifico){
			$pdf->txtHeaderEspecifico = $especifico;
		}

		if(is_array($detalhes))
			$pdf->txtHeader = $detalhes;
		
		if($aligns)
			$pdf->SetAligns($aligns);
			
		if($rodape!='')
			$pdf->notaDeRodape = $rodape;
		
		$header = array_keys($data[0]);
		$header = array_values($header);
		$header = array_map("utf8_decode",$header);				
						
		$pdf->SetWidths($w);
		//cabeçalho das coluns
		$pdf->headerColunas = $header;
		
		$pdf->AliasNbPages();
		$pdf->AddPage();
										
		//$pdf->SetFillColor(229, 229, 229);
		
		foreach($data as $row){
						
			$row = array_values($row);
			$row = array_map("utf8_decode",$row);			
			//$pdf->SetFont('arial','',9);
			$pdf->SetDrawColor(255,255,255);
			$pdf->SetFillColor(254,254,254);						
			$pdf->Row($row);
			$pdf->Ln(1);			
		}
		
		/*$pdf->Ln(1);
		$pdf->Ln(1);
		$pdf->Ln(1);
		*/
		if(count($anexos_array)>0){
			$pdf->headerColunas = null;
			$pdf->AddPage();
			foreach($anexos_array as $anexos){
				if($anexos['align'])
					$pdf->SetAligns($anexos['align']);
					
				$pdf->SetWidths($anexos['w']);
				
				$pdf->SetFont('arial','B');
				$pdf->SetFillColor(220,220,220);
				$pdf->cell(0,5,$anexos['titulo'],1,0,'L',1);
				$pdf->Ln();
				$pdf->SetDrawColor(10,0, 0);
				$header = array_keys($anexos['dados'][0]);
				$header = array_values($header);
				$header = array_map("utf8_decode",$header);
				$pdf->RowAnexo($header);
				$pdf->Ln(1);
				foreach($anexos['dados'] as $anexo){
					$pdf->SetDrawColor(10,0, 0);
					$row = array_values($anexo);
					$row = array_map("utf8_decode",$row);
					$pdf->RowAnexo($row);
					$pdf->Ln(2);
				}
			}
		}	
		
		if(count($imagens)>0){
			//$pdf->AddPage();
			foreach($imagens as $img){
				$pdf->Ln();
				$pdf->Ln();			
				$pdf->Image($img,17);
			}
		}

		//$assinaturas_array = array('marcos iran','teste da silva souro');

		/*
			Alterado por:   Natanael Diego
	    	Data alteracao: 10/04/2017
	      	Hora alteracao: 15:05
	      	Funcao:         Foi incluido a condicao no sindico para exibir ou não a assinatura dele.
		*/
		if(count($assinaturas_array)>0){
			$pdf->headerColunas = null;
			$pdf->AddPage();			
			$pdf->SetFont('arial','B');
			$linha_h = 70;
			if(isset($assinaturas_array['gestao'])&&count($assinaturas_array['gestao'])>0){
				$gestao = $assinaturas_array['gestao'];

				if(isset($gestao['sindico'])){
					$pdf->Line(55,$linha_h,160,$linha_h);
					$pdf->setXY(55,$linha_h);
					$gestao['sindico'] = trim(str_ireplace('(Síndico Contratado)','',$gestao['sindico']));				
					$pdf->MultiCell(100,5,utf8_decode(strpos($gestao['sindico'],'(')>0 ? $gestao['sindico'] : $gestao['sindico']." - Síndico"),0,'C');
					$pdf->Ln();
					$linha_h += 20;
				}
				
				if(isset($gestao['subsindico'])){
					$pdf->Line(55,$linha_h,160,$linha_h);
					$pdf->setXY(55,$linha_h);
					$gestao['subsindico'] = trim(str_ireplace('(SubSíndico Contratado)','',$gestao['subsindico']));
					$pdf->MultiCell(100,5,utf8_decode(strpos($gestao['subsindico'],'(')>0?$gestao['subsindico']:$gestao['subsindico']." - Subsíndico"),0,'C');
					$pdf->Ln();
					$linha_h += 20;
				}
			}

			if(isset($assinaturas_array['conselho'])&&count($assinaturas_array['conselho'])>0){
				foreach ($assinaturas_array['conselho'] as $nome) {				
					$pdf->Line(55,$linha_h,160,$linha_h);
					$pdf->setXY(55,$linha_h);
					$pdf->MultiCell(100,5,utf8_decode($nome." - Conselheiro"),0,'C');
					$pdf->Ln();
					$linha_h += 20;
				}
			}
		}
		
		if($filename==null)
			$filename = "relatorio";
		return $pdf->Output("$filename.pdf",$saida);
	}




	/*
    Autor:  Natanael Diego
    Data:   28/09/2016
    Hora:   11:36
    Funcao: Criado com base em um exemplo ja existente, foi realizado algumas modificacoes com o objetivo de melhorar o layout e ser o mais parecido possivel com o pdf gerado com a lib do mpdf.
    */
	function gerarPDF2($data, $w=null, $aligns=null, $filename = null, $titulo="Relatório", $detalhes = null, $rodape=null,$orientation = "L", $fontSize = '', $bordas = true, $fontSizecontent = '',$anexos_array=array(),$imagens = array(),$logoAdministradora='',$saida="I",$assinaturas_array=array()){
		//$pdf= new PDF("L","pt","A4");
		$pdf = new Pdf_table($orientation, 'mm', 'A4');

		if($fontSize!=''){
			$pdf->SetFont('arial','',$fontSize);
		}

		$pdf->nomeRelatorio = utf8_decode($titulo);	
		
		if($logoAdministradora!='')	
			$pdf->logoAdministradora = $logoAdministradora;
		
		if(is_array($detalhes))
			$pdf->txtHeader = $detalhes;
		
		if($aligns)
			$pdf->SetAligns($aligns);
			
		if($rodape!='')
			$pdf->notaDeRodape = $rodape;		
		
		$header = array_keys($data[0]);
		$header = array_values($header);
		$header = array_map("utf8_decode",$header);
		
						
		$pdf->SetWidths($w);
		//cabeçalho das coluns
		$pdf->headerColunas = $header;

		$pdf->AliasNbPages();
		$pdf->AddPage();		
		
		//$pdf->SetFont('arial','',10);
		//Tras o cabecalho
		foreach($data as $key => $row){
			if($key == 0){
				$cabecalho = array_keys($data[0]);						
				foreach($cabecalho as $ke => $rows){
					
					//$pdf->Cell($w[$ke],20,utf8_decode($cabecalho[$ke]),0,0,"C");
					
				}
				//$pdf->Ln();					
			}
		}
		
		//Verifico se o conteudo tera borda ou nao.
		if($bordas == true){
			$tipoBorda = 2;
		}else{
			$tipoBorda = 1;
		}
		
		//Verifica se foi passado valor para o tamanho do conteudo.
		if($fontSizecontent!=''){
			$pdf->SetFont('arial','',$fontSizecontent);
		}else{
			$pdf->SetFont('arial','',7);
		}
		//Tras o conteudo
		foreach($data as $key => $row){
			
			$row = array_values($row);				
			foreach($cabecalho as $ke => $rows){

				//Verifico se a cor sera zebrada.
				if($key % 2 == 0){
					$statu = true;
				}else{
					$statu = false;
				}				

				//Responsavel por colocar a palavra em negrito
				if(preg_match("/(<strong>)/",$row[$ke])){
					$pdf->SetFont('arial','B');
					$row[$ke] = str_replace('<strong>','',$row[$ke]);
					$row[$ke] = str_replace('</strong>','',$row[$ke]);
				}else{
					$pdf->SetFont('arial','');
				}

				//Responsavel por colocar a palavra em vermelho.
				if(preg_match("/(<red>)/",$row[$ke])){
					$pdf->SetTextColor(226,0,0);
					$row[$ke] = str_replace('<red>','',$row[$ke]);
					$row[$ke] = str_replace('</red>','',$row[$ke]);
				}else{
					$pdf->SetTextColor(0,0,0);
				}

				//Responsavel por colocar a cor de fundo cinza.
				if(preg_match("/(<b>)/",$row[$ke])){
					$pdf->SetFillColor(220,220,220);
					$row[$ke] = str_replace('<b>','',$row[$ke]);
					$row[$ke] = str_replace('</b>','',$row[$ke]);
					$statu = true;
					
				}else{
					//Responsavel por colocar a cor de fundo azul.
					$pdf->SetFillColor(249,249,249);
				}

				//Responsavel por mostrar a linha do conteudo.
				$pdf->Cell($w[$ke],5,utf8_decode($row[$ke]),$tipoBorda,0,$aligns[$ke], $statu);
			}
			$pdf->Ln();
		}
		
		if($filename==null)
			$filename = "relatorio";
		return $pdf->Output("$filename.pdf",$saida);
		//return $pdf->Output();
		
	}







		
}