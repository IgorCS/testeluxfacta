<?php
require('pdfparser/vendor/autoload.php');
class Pdf2Txt{
	public function getPaginas($arquivo){
        $texts   = array();
        @$content = file_get_contents(trim($arquivo));
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseContent($content);
        $pages  = $pdf->getPages();
        
        foreach ($pages as $page) {
            $texts[] = $page->getText();
        }
        return $texts;
    }
}