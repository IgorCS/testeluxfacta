<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="pt-br">
<body>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Enquete</title>   
     <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap.min.css" rel="stylesheet">
     <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap-theme.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css" media="screen"/>
	<script src="js/jquery-2.1.3.js"></script>	
</head>
<div id="loader"></div>
  <div id="content">
		<div class="page-header">
			<h1>Editar/Cadastrar - Enquete</h1>
		</div>	

		<form method="post" action="<?=base_url()?>home/Atualizar" enctype="multipart/form-data">
	      
			<div class="col-md-4">
				<div class="form-group">
					<label>Nome:</label>
					<input type="text" name="descricao" class="form-control" value="<?=$enquete->getDescricao()?>" required/>
					
				</div>
			</div>    
			
			<div class="col-md-4">
				<label><em>Todos os campos são obrigatórios.</em></label>
				<div class="clearfix"></div>
				<input type="hidden" name="id" value="<?=$enquete->getId()?>"/>
				<input type="submit" value="Salvar" class="btn btn-success" />
			</div>

		</form>
	</div>
</div>
    <script type="text/javascript">
		// Este evendo é acionado após o carregamento da página
		$(window).on('load', function () {
			//Após a leitura da pagina o evento fadeOut do loader é acionado, esta com delay para ser perceptivo em ambiente fora do servidor.
			jQuery("#loader").delay(2000).fadeOut("slow");
		});
	</script>
</body>
</html>

