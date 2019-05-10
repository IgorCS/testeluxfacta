<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property usuario $usuario Classe de usuário
 * @property Doctrine $doctrine Biblioteca ORM
 */
 
class Report extends CI_Controller {

    var $chavePrimaria = "id";
    function __construct(){
        parent::__construct();      
        $this-> url_controller = "/report";
        //$this->load->model("auto");
        $this->load->model("query");
        //$this->verifica_permissao();
    } 
 
    public function imprime($id){         
         $this->benchmark->mark('code_start');
         $lista = $this->query->gerarResultadoSQL(
          "SELECT e.id AS id ,
                se.id AS idSubEnquete, 
                se.idEnquete as idEnquete,
                e.descricao AS descricao,
                se.descricao AS subEnqueteDescricao, 
                se.nota AS nota, 
                se.STATUS AS status 
            FROM enquete e INNER JOIN subenquete se ON (se.idEnquete = e.id) where se.idEnquete = ".$id."
            ORDER BY se.id DESC");

        if(count($lista) <= 0){
          echo "<script>alert('Não foram encontrados Protocolos Internos com os dados informados.');window.close()</script>";
        }

        $enqueteArray = array();
        foreach ($lista as $value){
          $enqueteArray[] = array(
           'id'        =>$value['id'],
           'descricao' =>$value['descricao'],
           'subEnqueteDescricao' =>$value['subEnqueteDescricao'],
           'nota' =>$value['nota'],
           'status' =>$value['status'],           
        );
       }
      $jsonEnquete=array();
        foreach ($enqueteArray as $key => $enquete) {
                           
            $jsonEnquete[]=$data['enquete'][$enquete['subEnqueteDescricao']][] = $enquete;                              
        } 

        $data=array();
        $data['subEnqueteDescricao']=json_encode($jsonEnquete);      
        $this->load->view('admin/imprimeEnquete_view' ,$data); 
        $this->benchmark->mark('code_end');
        $data['geradoEm'] = "Gerado em: ".$this->benchmark->elapsed_time('code_start', 'code_end')." segundos";
    }
}

