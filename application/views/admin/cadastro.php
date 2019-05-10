<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mini-Crud com Bootstrap e CodeIgniter 3.0-Vue</title>   
     <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap.min.css" rel="stylesheet">
     <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap-theme.min.css" rel="stylesheet">

	

<div class="container">
	<div class="page-header">
		<h1>Cadastrar Enquete</h1>
	</div>
	

	<form method="post" action="<?=base_url()?>home/Salvar" enctype="multipart/form-data">
      
		<div class="col-md-6">
			    <label>Nome:</label>
				<input type="text" name="descricao" class="form-control" value="<?=$enquete->getDescricao() ? $enquete->getDescricao(): '' ?>" required/>
		</div>

		
		<div class="panel-body">

 </div>
		
<div class="col-md-4">
	<label><em>Todos os campos são obrigatórios.</em></label>
	<div class="clearfix"></div>
	<input type="submit" value="Salvar" class="btn btn-success" />
</div>
	</form>
</div>


