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
<!--<h1>Tela de Login</h1>-->
    <div id="form_login">      
            <div class="container">
                <div class="col-md-3">
                <h1 class="text-center">Faça seu Login</h1>
                 <form class="form-signin" role="form" method="post" action="<?= base_url('index.php/login/logar') ?>">
                    <div class="form-group">
                        <input class="form-control input-lg" placeholder="Username" type="username" name="username" id="username" required="true">
                    </div>
                    <div class="form-group">
                        <input class="form-control input-lg" placeholder="Password" type="password" name="password" id="password" required="true">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-lg btn-block" type="submit">Logar</button>
                   </div> 
                </div>
               </form>
            </div>
    </div> 
 </body>
</html>