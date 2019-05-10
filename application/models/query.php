<?php
class Query extends CI_Model{
    public function gerarResultadoSQL($sql="",$showSQL = false){    
        if($showSQL){
            echo "<pre>$sql</pre>";
        }
        try {
            $query = $this->db->query($sql);        
            $elements = array();
            if($query->num_rows()==0){
                return $elements;
            }
            foreach($query->result_array() as $row){
                $elements[] = $row;
            }
            $query->free_result();
            return $elements;
        } catch (Exception $e) {                
            log_message("ERROR", $e->getMessage()); 
            echo "\n\n<br>".$e->getMessage()."<br>\n\n";
        }
        return array();
    }       
}