<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" meta http -equiv="pragma" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mini Crud</title>   
    <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap-theme.min.css" rel="stylesheet">
   <!--  -->
    <script src="<?php echo base_url(); ?>includes/bootstrap/js/vue/vue2_5.js" type="text/javascript"></script>
</head>
<body>
	<div class="container">
	<div class="page-header">
		<h1>Crud Vue</h1>
	</div>
	<div id="app1" class="text-right" style="float:right;">
		<?php echo'Olá: ';?> <b>{{ message }}</b>
	</div>
		<h1 class="text-center"></h1>
	<div class="col-md-12">
	<div class="row">
	 	<a class="btn btn-success btn-xs" href="<?php echo site_url('home/Cadastrar')?>">Novo Cadastro</a>
	 	<a class="btn btn-danger btn-xs" href="<?php echo site_url('/restserver/rest')?>">JSON</a>
	 	<a class="btn btn-warning btn-xs" href="<?php echo base_url()?>login/logout">Sair</a>	      
<script>
	var app1 = new Vue({
		el: '#app1',
		data: {
			message: '<?php echo $us;?>'
		}
	})
</script>

<div id="app2">
	<div v-if="usuario.tipo === 'admin'">
		<h1>Bem vindo à área administrativa</h1>
	</div>
	
	<div v-else>
		<h1>Bem vindo as Enquetes da Semana</h1>
	</div>
</div>

<script>
	var app2 = new Vue({
		el : "#app2",
		data : {
			usuario : {
				login : "testes",
				tipo : '<?php echo $us;?>'
			}
		}
	})
</script>

<!-- // Define um novo componente chamado todo-item -->
			
<table class="table table-striped table-hover">
	<div class="row">
		<caption>Cadastros:</caption>
			<thead>
					<tr align="center">
						<th>Código</th>
						<th>Descrição</th>	
						<th style="" class="text-right">Ações</th>										
					</tr>
			</thead>

			<div id="app-7">
				<ol>
				   	<todo-item
						  v-for="item in groceryList"
						  v-bind:todo="item"
						  v-bind:key="item.id">
					</todo-item>
				</ol>
			</div>

			<script>
				Vue.component('todo-item', {
  				// O componente todo-item agora aceita uma
  				// "prop", que é como um atributo personalizado.
  				// Esta propriedade foi chamada de "todo".
  				props: ['todo'],
  				template: '<li>{{ todo.text }}</li>'
			})

			/*var app7 = new Vue({
				el: '#app-7',
				data: {
					groceryList: [
					{ id: 0, text: 'Vegetais' },
					{ id: 1, text: 'Queijo' },
					{ id: 2, text: 'Qualquer outra coisa que humanos podem comer' }
					]
				}
			})*/
			</script>
			
			<tbody>	
				<?php foreach($enquete as $key => $row){ ?>					
					<tr>
						<td>
							<span style="font-size:12px;" class="label label-default" data-toggle="" data-target=""><?= str_pad($row->getId(), 6, 0, STR_PAD_LEFT) ?></span>       
						</td> 							
						
						<td><?= $row->getDescricao() ?></td>
						
					    <!-- <td><?= anchor("cadastro/edit/$cadastro->id", "Editar") ?>
							<a href="#" class='confirma_exclusao' data-id="<?= $row->id ?>" data-nome="<?= $row->nome ?>" />Excluir</a>.
						</td>-->					
					
					
						<td class="actions" align="right">
							<!--<a class="btn btn-success btn-xs" href="view.html">Visualizar</a>-->
							<!-- <a class="btn btn-warning btn-xs" href="<?= base_url()?>home/Editar">Edit_test</a>-->
							<!-- <a class="btn btn-info btn-xs"    href="<?php echo base_url() . 'relatorio/Relatorio/' . $row->getId(); ?>">Imprime</a>  --> 
							<a class="btn btn-info btn-xs"    href="<?php echo base_url().'report/Imprime/' . $row->getId(); ?>">Imprime</a>     
							<a class="btn btn-warning btn-xs" href="<?php echo base_url().'home/Editar/' . $row->getId(); ?>">Editar</a>    
							<a class="btn btn-danger btn-xs"  href="<?php echo base_url().'home/Excluir/'. $row->getId(); ?>">Excluir</a>    
						</td>	
					</tr>	
			<?php } ?>						
		</tbody>
	</div>
</table>				
</div>
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