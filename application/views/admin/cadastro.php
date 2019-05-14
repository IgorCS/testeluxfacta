<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="pt-br">
<<!-- head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mini-Crud com Bootstrap e CodeIgniter 3.0-Vue</title>   
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

</head> -->

<head>
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script> -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" meta http -equiv="pragma" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mini Crud</title>   
    <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap-theme.min.css" rel="stylesheet">
   <!--  -->
    <script src="<?php echo base_url(); ?>includes/bootstrap/js/vue/vue2_5.js" type="text/javascript"></script>

    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
</head>

    
   <script>
            <?php $uid_form = uniqid();?>
           $(document).ready(function() {
            $('.btn_add<?=$uid_form ?>').click(function() {
                var descricao = $("#descricao").val();        
                var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                if(filter.test(descricao)) {
                    $('input.required').each(function() {
                        var name = $("input.required").val();
                        if(name != "") {
                            $('#form').submit();
                        } else {
                            $('#error').show();
                            return false;
                        }
                    });
                } 
                else {
                    $('#error').show();
                    return false;
                }
            });
    });
    
  var conttd = 1;
  function _camposDinamicos(){
    var x = $("#nota").val();
    if (x == ""){       
      alert("Preencha os Campos Descrição e Ordem!");
      return false;
  }else{
      flag = true;    
      if(flag){
        var tabela = "<tr id='td_"+conttd+"'>"+  
                        "<td>"+$("#descricao").val()+"<input name='enquete[descricao][]' id='descricao_"+conttd+"' class='form-control' type=hidden value='"+$("#descricao").val()+"'></td>"+
                        "<td>"+$("#nota").val()+"<input name='enquete[nota][]' id='nota_"+conttd+"' class='form-control' type=hidden value='"+$("#nota").val()+"'></td>"+
                        "<td></td>"+  
                        "<td></td>"+    
                       
                    "</tr>";                      

       
        $("#table_lista_campos").append(tabela);
        $('#descricao').val('');
        $('#nota').val('');        
        conttd++;       
        $("#descricao").focus();      
    }
  }  
}


function validar_form_<?=$uid_form?>(){
        $("#item-form_<?=$uid_form?>").validate().form();
        if($("#item-form_<?=$uid_form?>").valid()){
          sendForm('item-form_<?=$uid_form?>','dv_aux');
          return false; 
      }
} 


var descricao = document.getElementById("descricao");
var nota = document.getElementById("nota");
//var fileUpload = document.getElementById("fileUpload");
var btnEnvia = document.getElementById("btn_add<?=$uid_form ?>");

var onBriefingInput = function (event) {
   if(document.getElementById("nota")==0){
    btnEnvia.disabled = !event.target.value;
   }
}


</script>
	

<div class="container">
	<div class="page-header">
		<h1>Cadastrar Enquete</h1>
	</div>	

	

<form method="post" action="<?=base_url()?>home/Salvar" enctype="multipart/form-data">

<ul id="tabManual" class="nav nav-tabs">   
      <li class="active"><a href="#dados_manual" data-toggle="tab">Dados</a></li>
  </ul>  

  <div class="tab-content">   
      <div class="tab-pane active" id="dados_manual"><br>         
        <div class="row"> 
            <div class="form-group col-md-6">
              <label for="descricao">Descrição*:</label>
              
             
              <input type="text" class="required form-control" id="" name="cadastro[descricao]" placeholder="Preencha com a Descrição" title="Preencha" maxlength="255" value="<?=$enquete->getDescricao() ? $enquete->getDescricao(): '' ?>" />
            </div>                   
      </div> 

		
<!-- <div class="panel-body">

 </div> -->

 <div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">        
      <div class="panel-heading">Telas</div><p>&nbsp;</p>
      <div align="left">
        <? $conttd=''; ?>             
        <div class="form-group col-md-10">
          <label for="descricao">Descrição e Notas da Enquete:</label>
          <input type="text"  class="form-control" id="descricao" value="" maxlength="255" value="" title="Digite a Descrição da Enquete"  />
      </div> 

      <form id="formName">           
          <div class="form-group col-md-2">
            <label for="nota">Nota:</label>
            <input type="text"  min="1" class="form-control" id="nota" maxlength="255" value="" title="Digite a Nota" onkeyup="" pattern="[0-9]*" />
        </div>
    </form>  

</div>        
<div class="col-md-12" >
  <div align="center">
    <p>&nbsp;</p>
    <button type="button" class="btn btn-info" id="btn_add<?=$uid_form ?>" onclick="_camposDinamicos()"><span class="glyphicon glyphicon-plus"></span> Adicionar</button>
    <!--  -->
    <!--  -->
</div>
<p>&nbsp;</p>
</div>

<div class="panel-body">
  <diV>          
    <table class="table">
      <thead> 
        <tr>                  
         <th>Descrição</th> 
         <th>Nota</th>

         
         <th style="text-align: left;">Ações</th>
     </tr>             
 </thead>                    
 <tbody id="table_lista_campos">
    
    <?php if($enquete->getTelas()){ ?>                    
    <? $conttd=1;
    foreach($enquete->getTelas() as $item){  ?>                                            
    <tr id='td_<?= $conttd ?>' >
      <input type="" name="item[id]" id="id" value="<?=$item->getId()!=null ? $item->getId() : 0  ?>"> 

      <td><?= $item->getDescricao()?><input  id='descricao_<?= $conttd ?>' type=hidden class="form-control" value='<?= $item->getDescricao()?>'></td>
      <td><?= $item->getNota()?><input  id='nota_<?= $conttd ?>' class="form-control"  type=hidden value='<?= $item->getNota()?>'></td> 
     
      <? $conttd++;
    } ?>
     <script>conttd = <?= $conttd ?>;</script>
    <? } ?>
        </tbody>
</table>   
</div>
</div>



</div>  
</div>
</div>
		
        <div class="col-md-4">
        	<label><em>Todos os campos são obrigatórios.</em></label>
        	<div class="clearfix"></div>
        <!-- 	<input type="submit" value="Salvar" class="btn btn-success" /> -->
           <input type="submit" value="Salvar" class="btn btn-success" />
        </div>
</form>