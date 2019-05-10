<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//Registra o autoload das classes
spl_autoload_register('Autoload_Register::register');

/**
 * Executa o autoload de classes que não são do CI nem de PEAR
 */
class Autoload_Register
{

    const CI_PREFIX = "CI_";
    const PEAR = 'PEAR';

    /**
     * Faz o registro
     *
     * @param string $classe
     * @return void
     */
    public static function register($classe)
    {
        $paths = array();
        $paths[] = APPPATH . 'models/dao';
        $paths[] = APPPATH . 'models/Entities';
        $paths[] = APPPATH . 'models/graficos';

        //Classes do CI e do PEAR são ignoradas
        if (strstr($classe, self::CI_PREFIX) || stristr($classe, self::PEAR))
        {
            //Nada a fazer
            return;
        }

        //Remove o namesapce
        $vetClasse = explode('\\', $classe);
        $classe = $vetClasse[count($vetClasse)-1];

        //Garante que "$paths" seja um array
        if (is_array($paths) && count($paths) > 0)
        {
            //Localiza arquivos nos paths determinados no inicio da classe
            foreach ($paths as $dir)
            {
                $files = self::find($dir, $classe . EXT);

                //Se tiver arquivos a serem incluidos, executa o require_once para cada um deles
                if (is_array($files) && count($files) > 0)
                {
                    foreach ($files as $file)
                    {
                        if (is_file($file))
                        require_once $file;
                    }
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Procura por arquivos
     *
     * @param string $dir
     * @param string $pattern
     * @return array
     */
    private static function find($dir, $pattern)
    {
        // Elimina caracteres inválidos para comandos shell
        $dir = escapeshellcmd($dir);

        // pega a lista de todas as coincidencias no diretorio corrente
        $files = glob("$dir/$pattern");

        // procura a lista de todos os sub diretorios no diretorio corrente
        // diretorios iniciados por um ponto também são incluidos
        foreach (glob("$dir/$pattern", GLOB_BRACE | GLOB_ONLYDIR) as $sub_dir)
        {
            $arr = self::find($sub_dir, $pattern);  // chamada revursiva
            $files = array_merge($files, $arr); // faz um merge no array com os arquivos do sub diretorio
        }

        // retorna todos os arquivos encontrados
        return $files;
    }

}