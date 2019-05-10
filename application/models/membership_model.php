<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Membership_model extends CI_model{
	




	
function validate($username = '', $password = ''){

	/*$query['usuarios'] =$this->db->where('username', $this->input->post('username'));
	$query['usuarios'] =$this->db->where('password', md5($this->input->post('password')));         
	$query['usuarios'] =$this->db->where('status', 1); // Verifica o status do usuário         
	$query['usuarios'] =$this->db->get('membership');  
	if ($query['usuarios']->num_rows == 1) {              
		return true; */
	// RETORNA VERDADEIRO

		if( !$username && !$password ) {
			
			return false;
			
		} else {

			$query['usuarios'] =$this->db->where('username', $this->input->post('username'));
			$query['usuarios'] =$this->db->where('password', md5($this->input->post('password')));
	  		$query['usuarios'] =$this->db->where('status', 1); // Verifica o status do ário  
	  		//$query['usuarios'] =$this->db->where('tipo', 'administrador');       
	  		$query['usuarios'] =$this->db->get('membership');

			//$this->db->where(array('username'=>$username, 'password'=>md5($password)));
			//$res = $this->db->get('membership'); // coletando usuarios no banco

  		if($query['usuarios']->num_rows()==1){

  			return true;

  		}else{

  		return false;

  		}
	}
}     
# VERIFICA SE O USUÁRIO ESTÁ LOGADO     
function logged(){         
	$logged = $this->session->userdata('logged');         
		if (!isset($logged) || $logged != true) {             
			echo 'Usuário ou Senha Inválidos!';  
			//redirect(site_url('login'));           
			die();         
		}     
	}
}
