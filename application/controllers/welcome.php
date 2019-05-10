<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @property usuario $usuario Classe de usuário
 * @property Doctrine $doctrine Biblioteca ORM
 */
class Welcome extends CI_Controller
{


    // ------------------------------------------------------------------------

    public function index($param = '')
    {
        


      // $this->salvar();
       $this->localizar('id', $param);
       $this->localizar_por_nome('igor');
       $this->localizar_por_idade(25, 30);      
       $this->salvar();
        //$variaveis['cadastros'] = $this->m_cadastros->get();
       $this->load->view('home');
    }

    // ------------------------------------------------------------------------

    public function salvar()
    {
        $usuario = new usuario();
        $usuario->setNome('Natanael');
        $usuario->setEmail('natanael@mail.com.br');
        $usuario->setTelefone('11-1122-3345');
        $usuario->setCelular('11-9988-7765');
        $usuario->setIdade(26);
       
        $this->doctrine->em->persist($usuario);
        $this->doctrine->em->flush();
    }

    // ------------------------------------------------------------------------

    /**
     * Localiza o usuario para ser editado
     *
     * @param int $id
     */
    public function editar($id)
    {
        $usuario = $this->doctrine->em->find('usuario', $id);

        if ($usuario instanceof usuario)
        {
            echo ' Nome: ' . $usuario->get_nome() . '<br>';
        }
        else
        {
            echo 'Usuário não localizado';
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Consulta pelo campo e valor solicitado
     *
     * @param string $campo Nome do campo onde se deseja fazer a busca
     * @param mixed $valor Valor que deve ser localizado
     * @link http://doctrine-orm.readthedocs.org/en/latest/reference/working-with-objects.html#by-simple-conditions
     */
    public function localizar($campo, $valor)
    {
        $usuario = $this->doctrine->em->getRepository('usuario')->findOneBy(array($campo => $valor));

        if ($usuario instanceof usuario)
        {
            echo 'Nome: ' . $usuario->get_nome() . '<br>';
        }
        else
        {
            echo 'Usuário não localizado';
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Localiza pelo nome do usuário
     *
     * @param String $nome Nome do usuário
     * @link http://doctrine-orm.readthedocs.org/en/latest/reference/working-with-objects.html#by-simple-conditions
     */
    public function localizar_por_nome($nome)
    {
        $usuario = $this->doctrine->em->getRepository('usuario')->findOneByNome($nome);

        if ($usuario instanceof usuario)
        {
            echo ' NOME: ' . $usuario->getNome() . '<br>';
        }
        else
        {
            echo 'Usuário não localizado';
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Localiza um usuário que tenha a idade entre dois valores
     *
     * @param type $idade_menor
     * @param type $idade_maior
     * @link http://doctrine-orm.readthedocs.org/en/latest/reference/working-with-objects.html#by-dql DQL
     */
    public function localizar_por_idade($idade_menor, $idade_maior)
    {
        $usrs = $this->doctrine
                ->em
                ->createQuery("SELECT u FROM usuario u WHERE u.idade >= {$idade_menor} and u.idade <= {$idade_maior}")
                ->getResult();

        if (is_array($usrs) && count($usrs) > 0)
        {
            foreach ($usrs as $i => $usuario)
            {
                if ($usuario instanceof usuario)
                {
                    echo "{$i} - Nome: {$usuario->getNome()}<br>";
                    echo "{$i} - Email:{$usuario->getEmail()}<br>";
                    //echo "{$i} - Telefone:{$usuario->get_telefone()}<br>";
                    echo "{$i} - Cellular:{$usuario->getCelular()}<br>";
                    echo "{$i} - Idade:{$usuario->getIdade()}<br>";
                    echo "------------------------------------------------------------------<br>";                    

                }
            }
        }

    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */