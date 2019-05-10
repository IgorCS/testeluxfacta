<?php

namespace Entity;
/**
 * enquete
 * @Entity
 * @Table(name="enquete")
 */

//use Doctrine\Common\Collections\ArrayCollection;


class enquete
{
    /**
    * @Id
    * @Column(type="integer", nullable=false)
    * @GeneratedValue(strategy="IDENTITY")
    */
    public $id;


    /**
     * @Column(type="string", columnDefinition="VARCHAR(200) NOT NULL")
     */
    public $descricao = ''; 


    /**
    * @OneToMany(targetEntity="subenquete", mappedBy="idEnquete" ,fetch="EXTRA_LAZY")
    * @OrderBy({"id" = "ASC"})
    **/ 
    private $telas;
    

    
    public function getId()
    {
        return $this->id;
    }

   

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getTelas(){
        return $this->telas;
    }

    public function setTelas($telas){           
        $this->telas = $telas;
        return $this->telas;
    }  

    public function getDescricaoTelas(){            
        $descricao = '';    
        foreach ($this->telas as $item){
            $descricao.= $item->getDescricao();
        //return $this->valorTotal !='' ? number_format($this->valorTotal,2,',','.') : '0,00';
        }
        return $descricao;
    }    

}


/* End of file enquete.php */
/* Location: ./application/model/enquete.php */