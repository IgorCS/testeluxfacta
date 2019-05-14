<?php
namespace Cnab\Remessa\Cnab400;

class Detalhe extends \Cnab\Format\Linha
{
	public function __construct(\Cnab\Remessa\IArquivo $arquivo, $possue2Desconto = false)
    {
    	$codigo_banco = $arquivo->codigo_banco;
        $yamlLoad = new \Cnab\Format\YamlLoad($codigo_banco);
        if(!$possue2Desconto){
        	$yamlLoad->load($this, 'cnab400', 'remessa/detalhe');
        }else{
        	$yamlLoad->load($this, 'cnab400', 'remessa/detalhedesconto2');
        }
	}
}