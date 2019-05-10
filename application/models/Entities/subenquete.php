<?php
namespace Entity;
/**
* subenquete
*
* @Entity
* @Table(name="subenquete")
* @author Igor
*/
class subenquete {

 /**
 * @Id
 * @Column(type="integer", nullable=false)
 * @GeneratedValue(strategy="IDENTITY")
 */
 public $id;


 /**
 * @ManyToOne(targetEntity="enquete")
 * @JoinColumn(name="idEnquete", referencedColumnName="id")
 */
 public $idEnquete; 

 
 /**
 * @Column(name="descricao", type="string",  nullable=false)
 */
 public $descricao;

  
 
 /**
 * @Column(name="nota", type="string", nullable=false)
 */
 public $nota;


 /**
 * @Column(name="status", type="string", nullable=false)
 */
 public $status;


 public function getId(){
 	return $this->id;
 }


  public function getIdEnquete(){				
 	return $this->idEnquete;
 }


 public function setidEnquete($idEnquete){				
 	$this->idEnquete = $idEnquete;
 	return $this->idEnquete;
 } 


 public function getDescricao(){          
 	return $this->descricao;
 }


 public function setDescricao($descricao){            
 	$this->descricao = $descricao;
 	return $this->descricao;
 } 


 public function getNota(){         
 	return $this->nota;
 }

 public function setNota($note){            
 	$this->nota = $nota;
 	return $this->nota;
 }
 

 public function getStatus(){
 	return $this->status;
 }

 public function setStatus($status){
 	$this->status = $status;
 	return $this->status;
 }

}