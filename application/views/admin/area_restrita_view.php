<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mini Crud Codgniter Doctrine</title>   
     <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap.min.css" rel="stylesheet">
     <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap-theme.min.css" rel="stylesheet">	
</head>
<body>
	
	<div class="page-header">
		<h1>LOGADO!</h1>

	</div>
   <!-- <form method="post" action="<?=base_url()?>login/Atualizar" enctype="multipart/form-data">-->
  <div class="col-md-4">
   <p>Olá, <?= $username ?>!</p>
   <?= $mensagem ?>
  <!-- $data['username'] -->
   <!--$this->session->unset_userdata('some_name');-->
	<!--Cliqui aqui para deslogar: <a href="<?=base_url()?>login/">Deslogar</a></p>-->
	<!--<form method="post" action="<?=base_url()?>home/Atualizar" enctype="multipart/form-data">-->
	<a class="btn btn-warning btn-xs" href="<?php echo base_url() . 'login'   ?>">Deslogar</a>			 
</div>	
		
</body>
</html>