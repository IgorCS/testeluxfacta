<head>
        <title>Autentificação</title>
        <link href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/estilos.css" rel="stylesheet">

        <link href="<?php echo base_url(); ?>includes/bootstrap/css/bootstrap/bootstrap.css" rel="stylesheet">
     <link href="<?php echo base_url(); ?>includes/bootstrap/css/estilo.css" rel="stylesheet">
    </head>
    <body>  
       
                
                <?php echo '<div class="alert alert-error">'.validation_errors().'</div>'; ?>
                
                <?php echo form_open('verifylogin'); ?>
                <div class="container">
                <div class="col-md-3">
                <h1 class="text-center">Login</h1>
                    <div class="form-group">
                        <input class="form-control input-lg" placeholder="Email" type="email" name="email" id="email" required="true">
                    </div>
                    <div class="form-group">
                        <input class="form-control input-lg" placeholder="Senha" type="password" name="senha" id="senha" required="true">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-lg btn-block" type="submit">Logar</button>
                   </div> 
            </div>
        </div>
    </body>
</html>