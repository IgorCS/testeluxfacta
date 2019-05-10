<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" meta http -equiv="pragma" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mini Crud Codgniter Doctrine</title>   
    
</head>
<body>
	<div class="container">
	<div class="page-header">
		<h1>Rest_Json</h1>
	</div>
		<h1 class="text-center"></h1>
		<div class="col-md-12">
			<div class="row">
				 
<a class="btn btn-warning btn-xs" href="<?php echo base_url()?>login/logout">Sair</a>
<!--action="<?=base_url()?>home/Salvar"-->
				<!--  <p>Olá, <?php echo $us;?>!</p>-->
				  <!--<p>Bem Vindo,<?php echo $this->session->userdata['usuario'];?>!</p>-->

				  <!--<p>Olá, <?php echo $user[0]['username'];?>!</p>-->

				  <!--<h1><?php echo $heading;?></h1>-->
				 
	       </div>			

      

 
			
				<table class="table table-striped table-hover table-bordered">
				<caption></caption>
					<thead>
						<tr>
							<th>Código</th>
							<th>Nome</th>
							<th>Email</th>
							<th>Cellular</th>
							<th>Idade</th>	
						</tr>
					</thead>
					<tbody>	
					<?php foreach($usuarios as $item): ?>					
						<tr>							
							<td><?= $item->getId() ?></td>
			                <td><?= $item->getNome() ?></td>
			                <td><?= $item->getEmail() ?></td>
			                <td><?= $item->getCelular() ?></td>
			                <td><?= $item->getIdade() ?></td>	
			             
						<?php endforeach; ?>						
					</tbody>
				</table>
				
				
			</div>
		</div>	
	</div>

</body>
</html>