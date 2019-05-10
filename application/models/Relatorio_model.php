<?php
class Relatorio_modelo extends CI_Model {
 
    function __construct() {
        parent::__construct();
        $this->table = 'usuario';
    }
 
    /**
    * Formata os usuario para exibição dos dados na home
    *
    * @param array $usuario Lista dos usuarios a serem formatados
    *
    * @return array
    */
    function relatorioUsuarios() {
        //$this->load->database();
       // $usuario['usuarios'] = $this->db->get('tblalumno');
       $usuario['usuarios'] = $this->doctrine->em->getRepository('usuario')->findAll();
       //$this->load->view('home', $usuario); 
     // return $usuario->result();
    }
}
