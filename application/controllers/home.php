<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 
/**
 * @property enquete $enquete Classe de usuário
 * @property Doctrine $doctrine Biblioteca ORM
 */

/**
	 * Método principal do mini-crud
	 * 
 	 */
    /**
	 * Método principal do mini-crud
	 * @param nenhum
	 * @return view
	 */	
class Home extends MY_Controller {
var $chavePrimaria = "id"; 
	

	
     // require_once ('controllers/login.php');     
     //$banana = $usuario->minhaFuncaoDaClasseUm();


	public function index()	{
		require_once "application/controllers/login.php";
		$us = new Login();
		$sessao = $us->logar = $this->session->userdata('usuario');
		$data['us'] = $sessao;

		//$data['enquete'] = $this->doctrine->em->getRepository('enquete')->findAll();
		$data['enquete']= $this->doctrine->em->getRepository("Entity\Enquete")->findAll();
             						
		$this->load->view('admin/home', $data);
	}


	public function restserver()	{



		$list = array();
		$list[0]['userId'] = 1;
		$list[0]['title'] = "Vamos pegar este rest e botar na API!!![-_-]";  
		$list[0]['body'] = "TESTE DO REST com JSON!!!"; 
		echo $myJSON = json_encode($list[0]) . "<br/>";
		exit();


		$this->load->view('admin/restserver', $dados);
	}





	/**
	* Método principal do mini-crud
	* @param nenhum
	* @return view
	*/	 	 
	public function Cadastrar(){
   		//$logged = $this->session->userdata('logged');   

   		$enquete = $this->doctrine->em->getRepository("Entity\Subenquete")->findBy(array($this->chavePrimaria=>0));
	 	$enquete['enquete'] = new Entity\Enquete();        			
        //$data['protocolointerno'] = new Entity\Protocolointerno;
	 	$this->load->view('admin/cadastro' ,$enquete);				
				//redirect(site_url('admin/cadastro',$usuario));  
				//redirect('cadastro',$usuario);
			//}		
	}


	 /**
	 * Método principal do mini-crud
	 * @param nenhum
	 * @return view
	 */	 
	 public function Salvar(){

	 	$post = $_POST;
   		  
	 	$enquete['enquete'] = new Entity\Enquete();   
	 	$enquete['enquete']->setDescricao($post['descricao']);
	 	                  

	 	$this->doctrine->em->persist($enquete['enquete']);
	 	$this->doctrine->em->flush(); 

	  	//exit(); 
	 	redirect();
	 	redirect(site_url('admin/home',$enquete));
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
		 $data['enquete'] = $this->doctrine->em->getRepository("Entity\Enquete")->findOneBy(array('id'=>$id));
		//echo($id);
		//exit();
		if ($data['enquete'] instanceof enquete)
        {
          //  echo ' Nome: ' . $usuario['usuarios']->getNome() . '<br>';
        }
        else
        {
            echo 'Não Localizado(a)';
        }
        // exit();		
		// Carrega a view passando os dados do registro
		$this->load->view('admin/editar',$data);
	}


	/**
	 * Método principal do mini-crud
	 * @param nenhum
	 * @return view
	*/	 
   public function Atualizar(){
   		$post = $_POST;
   		
		var_dump($post);
		$enquete=$enquete['enquete'] = $this->doctrine->em->getRepository("Entity\Enquete")->findOneBy(array('id'=>$post['id']));

        $enquete->setDescricao($post['descricao']);
       
		//$usuario = new usuario();          
          $this->doctrine->em->persist($enquete);
          $this->doctrine->em->flush(); 

			if(!$post['id']){
				echo 'Não foi possivel editar usuario';
			}else{
				echo 'OK.Usuário Editado com sucesso!!!!';
				// Redireciona o usuário para a home
				redirect();
			}		
		// Carrega a view para edição
		$this->load->view('editar',$enquete);
	}



	 /**
     * Localiza o usuario para ser editado
     *
     * @param int $id
     */
	public function Excluir($id){		

		  $usuario['usuarios'] = $this->doctrine->em->getRepository("Entity\Enquete")->findOneBy(array('id'=>$id));	
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


   





	





