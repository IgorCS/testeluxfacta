<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


  /**
  * @property 
  * @property 
  */


  /**
	 * @param nenhum
	 * @return view
	 */	
class Restserver extends CI_Controller {

    	 /**
       * Método principal 
       * 
       */
        /**
       * @param nenhum
       * @return view
       */ 
        public function index()
        {   
       // $chamar['usuarios'] = $this->doctrine->em->getRepository('usuario')->findAll();
        //$this->load->view('home', $chamar);       
        }


         // require_once ('controllers/login.php');     
         //$banana = $usuario->minhaFuncaoDaClasseUm();

      	/*public function rest()	{  		   //$dados=""; 
    	 
         //$dados = $this->doctrine->em->getRepository('usuario')->findAll();
          	                           
        $dados = array();
        $array[0]['id'] =  1; 
        $array[0]['nome'] =  "IgoCS"; 
        $array[0]['email'] =  "igorcs@mail.com"; 

        $array[1]['id'] =  2; 
        $array[1]['nome'] =  "testes_2"; 
        $array[1]['email'] =  "testes_2@mail.com"; 

       
    array_filter($array, function(&$array) {});

    $data = json_encode(array($array[0],$array[1])); 

    echo $data;

    }*/

    public function rest(){	 
      $dados = $this->doctrine->em->getRepository("Entity\Enquete")->findAll();                   
      $data = json_encode($dados); 
          //echo'acessou aqui';
      echo $data;
    }

}














