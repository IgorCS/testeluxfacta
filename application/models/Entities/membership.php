<?php


/**
 *
 * @Entity
 * @Table(name="membership")
 */
class membership
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    public $id = 0;


    /**
     * @Column(type="string", columnDefinition="VARCHAR(50) NOT NULL")
     */
    public $username= 0;


    /**
     * @Column(type="string", columnDefinition="VARCHAR(50) NOT NULL")
     */
    public $password = 0;



    /**
     * @Column(type="integer", columnDefinition="VARCHAR(50) NOT NULL")
     */
    public $status = 0;



    

    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }


    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

   

    

}


/* End of file membership.php */
/* Location: ./application/model/membership.php */