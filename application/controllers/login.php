<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Login extends CI_Controller{



	public function index(){

		$this->load->view('login/login_view');
	}
	
	public function logar(){

		$username = $this->input->post("username");
		$password = sha1($this->input->post("password"));

			//Se o usuário e senha combinarem, então basta eu redirecionar para a url base, pois agora o usuário irá passa
			//pela verificação que checa se ele está logado.
		if ($username && $password) {
			$this->load->model('membership_model'); // carregamos o model		
			$verifica = $this->membership_model->validate($username, $password);
			//exit();
			if(!$verifica) {				

				redirect(base_url('login'));
				
			}

			if ($verifica === true) {

			$user_array = array("username" => $this->input->post('username')); //exit(); 
			$this->session->set_userdata('nome_usuario',array('username' => $this->input->post('username')));

			$this->session->set_userdata($user_array);
			$usuario = $this->session->userdata('nome_usuario');
			$chamar['usuario'] = $usuario['username'];
			//echo($chamar['usuario']);
			//exit();
			$this->session->set_userdata( 'usuario', $username );
			$this->session->set_userdata( 'logado', 1 );
			redirect(base_url());
		} else {
				//caso a senha/usuário estejam incorretos, então mando o usuário novamente para a tela de login com uma mensagem de erro.
			echo('Usuario ou Senha Incorretos');
			$this->load->view("login_view");
		}
	}
		/*
		 * Aqui eu destruo a variável logado na sessão e redireciono para a url base. Como esta variável não existe mais, o usuário
		 * será direcionado novamente para a tela de login.
		 */
	}


	public function Sessao(){
		$us = new Login();
		$us->logar();
	}


	public function logout(){
		$this->session->unset_userdata("logado");
		redirect(base_url());	
	}

}