<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" meta http -equiv="pragma" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mini Crud Codgniter Doctrine</title>   
     <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap.min.css" rel="stylesheet">
     <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap-theme.min.css" rel="stylesheet">	
</head>
<body>
	<div class="container">
	<div class="page-header">
		<h1>RestServer</h1>
	</div>
		<h1 class="text-center"></h1>
		<div class="col-md-12">
			<div class="row">
				 
<a class="btn btn-warning btn-xs" href="<?php echo base_url()?>login/logout">Sair</a>
<!--action="<?=base_url()?>home/Salvar"-->
				 
				  <!--<p>Bem Vindo,<?php echo $this->session->userdata['usuario'];?>!</p>-->

				  <!--<p>Olá, <?php echo $user[0]['username'];?>!</p>-->

				  <!--<h1><?php echo $heading;?></h1>-->
				 
	       </div>			

        <!--<div class="col-md-4">
			<div class="form-group">
				<label>Nome:</label>
				<input type="text" name="nome" class="form-control" value="" required/>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Email:</label>
				<input type="email" name="email" class="form-control" value="" required/>
			</div>
		</div>

		<div class="col-md-4">
			<label><em>Todos os campos são obrigatórios.</em></label>
			<div class="clearfix"></div>
			<input type="submit" value="Salvar" class="btn btn-success btn-xs" />
		</div>
			<div class="row">
				<h3></h3>
			</div>
			<div class="row">
			<h3></h3>-->

 
			
				<table class="table table-striped table-hover table-bordered">
				<caption>Cadastros:</caption>
					<thead>
						<tr>
							<th>Código</th>
							<th>Nome</th>
							<th>Email</th>
							<th>Cellular</th>
							<th>Idade</th>	
							<th>Ações</th>						
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
			              <!-- <td><?= anchor("cadastro/edit/$cadastro->id", "Editar") ?>
		<a href="#" class='confirma_exclusao' data-id="<?= $item->id ?>" data-nome="<?= $item->nome ?>" />Excluir</a>.
		</td>-->
			                
			                <td class="actions">
                           <!--<a class="btn btn-success btn-xs" href="view.html">Visualizar</a>-->
                            
   <!-- <a class="btn btn-warning btn-xs" href="<?=base_url()?>home/Editar">Edit_test</a>-->
   <!-- <a class="btn btn-info btn-xs"    href="<?php echo base_url() . 'relatorio/Relatorio/' . $item->getId(); ?>">Imprime</a>  --> 
   <!-- <a class="btn btn-info btn-xs"    href="<?php echo base_url() . 'report/Imprime/' . $item->getId(); ?>">Imprime</a>     
    <a class="btn btn-warning btn-xs" href="<?php echo base_url() . 'home/Editar/' . $item->getId(); ?>">Editar</a>    
    <a class="btn btn-danger btn-xs"  href="<?php echo base_url() . 'home/Excluir/'. $item->getId(); ?>">Excluir</a> -->   
                         </td>
						</tr>	
						<?php endforeach; ?>						
					</tbody>
				</table>
				
				
			</div>
		</div>	
	</div>
<div class="modal fade" id="modal_confirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Confirmação de Exclusão</h4>
      </div>
      <div class="modal-body">
        <p>Deseja realmente excluir o registro <strong><span id="nome_exclusao"></span></strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Agora não</button>
        <button type="button" class="btn btn-danger" id="btn_excluir">Sim. Acabe com ele</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?= base_url('assets/js/jquery.js') ?>"></script>	
<script src="<?= base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>

	<script>
	
		var base_url = "<?= base_url(); ?>";
	
		$(function(){
			$('.confirma_exclusao').on('click', function(e) {
			    e.preventDefault();
			    
			    var nome = $(this).data('nome');
			    var id = $(this).data('id');
			    
			    $('#modal_confirmation').data('nome', nome);
			    $('#modal_confirmation').data('id', id);
			    $('#modal_confirmation').modal('show');
			});
			
			$('#modal_confirmation').on('show.bs.modal', function () {
			  var nome = $(this).data('nome');
			  $('#nome_exclusao').text(nome);
			});	
			
			$('#btn_excluir').click(function(){
				var id = $('#modal_confirmation').data('id');
				document.location.href = base_url + "index.php/cadastro/delete/"+id;
			});					
		});
	</script>
</body>
</html>