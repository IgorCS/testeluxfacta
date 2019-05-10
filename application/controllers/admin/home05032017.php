<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');



/**
 * @property usuario $usuario Classe de usuário
 * @property Doctrine $doctrine Biblioteca ORM
 */
class Home extends CI_Controller {
 function __construct(){
        parent::__construct();		
		$this->load->model('membership_model','membership');
		$this->membership->logged();
	}



	/**
	 * Método principal do mini-crud
	 * 
 	 */
    /**
	 * Método principal do mini-crud
	 * @param nenhum
	 * @return view
	 */	
	public function index()
	{

	  $chamar['usuarios'] = $this->doctrine->em->getRepository('usuario')->findAll();
	  $this->load->view('admin/home', $chamar);
	 // $this->load->view('admin/home', $stringUser);       
    }


  


   /**
  * Processa o formulário para salvar os dados
  */
    /**
	 * Método principal do mini-crud
	 * @param nenhum
	 * @return view
	 */	 
    public function Editar($id){
		// Recupera o ID do registro - através da URL - a ser editado
		//$id = $this->uri->segment(2);
		/*echo ('testes');//$id = $this-> uri->segment(2);
		exit();*/
		// Se não foi passado um ID, então redireciona para a home
		if(is_null($id))
		redirect();		
		// Recupera os dados do registro a ser editado		
		 $usuario['usuarios'] = $this->doctrine->em->getRepository('usuario')->findOneBy(array('id'=>$id));
		//echo($id);
		//exit();
		if ($usuario['usuarios'] instanceof usuario)
        {
          //  echo ' Nome: ' . $usuario['usuarios']->getNome() . '<br>';
        }
        else
        {
            echo 'Usuário não localizado';
        }
       // exit();
		
		// Carrega a view passando os dados do registro
		$this->load->view('editar',$usuario);

	}


   /**
	 * Método principal do mini-crud
	 * @param nenhum
	 * @return view
	 */	 
   public function Atualizar(){
   	$post = $_POST;
   //	var_dump($post['id']);		
			// Checa o status da operação gravando a mensagem na seção
   	        
			// Atualiza os dados no banco recuperando o status dessa operação
			//$status = $usuario['usuarios'] instanceof usuario;
$users=$usuario['usuarios'] = $this->doctrine->em->getRepository('usuario')->findOneBy(array('id'=>$post['id']));
//var_dump($usuario['usuarios']->getId());
//exit();
//$usuario = new usuario();
        $users ->setNome($post['nome']);
        $users ->setEmail($post['email']);
        $users ->setTelefone($post['telefone']);
        $users ->setCelular($post['celular']);
        $users ->setIdade($post['idade']);
		//$usuario = new usuario();          
          $this->doctrine->em->persist($usuario['usuarios']);
          $this->doctrine->em->flush(); 

			if(!$post['id']){
				echo 'Não foi possivel editar usuario';
			}else{
				echo 'OK.Usuário Editado com sucesso!!!!';
				// Redireciona o usuário para a home
				redirect();
			}		
		// Carrega a view para edição
		$this->load->view('editar',$usuario);
	}

    /**
	 * Método principal do mini-crud
	 * @param nenhum
	 * @return view
	 */	 
   public function Salvar(){

   	$post = $_POST;
   //var_dump($post);
   	//exit();  
        $usuario['usuarios'] = new usuario();
        $usuario['usuarios']->setNome($post['nome']);
        $usuario['usuarios']->setEmail($post['email']);  
        $usuario['usuarios']->setCelular($post['celular']);
        $usuario['usuarios']->setTelefone($post['telefone']); 
        $usuario['usuarios']->setIdade($post['idade']);                             
                  
         $this->doctrine->em->persist($usuario['usuarios']);
         $this->doctrine->em->flush(); 
         redirect();			
		// Carrega a view 
		//$this->load->view('cadastro',$usuario);
        //$this->load->view('cadastro',$usuario);

	}
    /**
	 * Método principal do mini-crud
	 * @param nenhum
	 * @return view
	 */	 	 
   public function Cadastrar(){
        $usuario['usuarios'] = new usuario();        			
		// Carrega a view 
		$this->load->view('cadastro',$usuario);
	}


    /**
     * Localiza o usuario para ser editado
     *
     * @param int $id
     */
	public function Excluir($id){
		

		$usuario['usuarios'] = $this->doctrine->em->getRepository('usuario')->findOneBy(array('id'=>$id));		
          $this->doctrine->em->remove($usuario['usuarios']);
          $this->doctrine->em->flush(); 
		// Checa o status da operação gravando a mensagem na seção
		if(!$usuario!=null){
				echo 'Não foi possivel excluir o usuario';
			}else{
				echo 'Usuário Excluído com sucesso!!!!';
				// Redireciona o usuário para a home
				redirect();
			}	
		// Redirecionao o usuário para a home
		/*
		if ($entity != null){
                $em->remove($entity);
                $em->flush();
		*/
	}


}


	





