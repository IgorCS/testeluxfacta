
<!--  -->


<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Imprimir Enquete</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- <?=$header?> -->
<style>
.demo-container {
  box-sizing: border-box;
  width: 800px;
  height: 450px;
  padding: 20px 15px 15px 15px;
  /*margin: 15px auto 30px auto;*/
  border: 1px solid #ddd;
  background: #fff;
  background: linear-gradient(#f6f6f6 0, #fff 50px);
  background: -o-linear-gradient(#f6f6f6 0, #fff 50px);
  background: -ms-linear-gradient(#f6f6f6 0, #fff 50px);
  background: -moz-linear-gradient(#f6f6f6 0, #fff 50px);
  background: -webkit-linear-gradient(#f6f6f6 0, #fff 50px);
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
  -o-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  -ms-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  -moz-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  -webkit-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}
.demo-placeholder {
  width: 100%;
  height: 100%;
  font-size: 14px;
  line-height: 1.2em;
}
.myCSSClass {
  margin-top: 4px;
  font-size: 9px;
  color: #000000;
  padding: 2px;
  z-index: 999;
}
 . {
 text-align: right;
 width: 35px;
 padding-right: 20px;
 padding-bottom: 10px;
}
.demo-container1 {  box-sizing: border-box;
  width: 800px;
  height: 450px;
  padding: 20px 15px 15px 15px;
  margin: 15px auto 30px auto;
  border: 1px solid #ddd;
  background: #fff;
  background: linear-gradient(#f6f6f6 0, #fff 50px);
  background: -o-linear-gradient(#f6f6f6 0, #fff 50px);
  background: -ms-linear-gradient(#f6f6f6 0, #fff 50px);
  background: -moz-linear-gradient(#f6f6f6 0, #fff 50px);
  background: -webkit-linear-gradient(#f6f6f6 0, #fff 50px);
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
  -o-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  -ms-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  -moz-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  -webkit-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}
.demo-container11 {box-sizing: border-box;
  width: 800px;
  height: 450px;
  padding: 20px 15px 15px 15px;
  margin: 15px auto 30px auto;
  border: 1px solid #ddd;
  background: #fff;
  background: linear-gradient(#f6f6f6 0, #fff 50px);
  background: -o-linear-gradient(#f6f6f6 0, #fff 50px);
  background: -ms-linear-gradient(#f6f6f6 0, #fff 50px);
  background: -moz-linear-gradient(#f6f6f6 0, #fff 50px);
  background: -webkit-linear-gradient(#f6f6f6 0, #fff 50px);
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
  -o-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  -ms-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  -moz-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  -webkit-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.alinharTexto{
  text-align: center;
  font-size: 13px;
  background-color: #f4f2f2;
}

.proporcao{
  font-weight: bold;
}

.tabela td{  
  border: 1px solid #ccc !important;
  padding: 3px !important;
}
</style>
<script type="text/javascript">

</script>
<?php $array=json_decode($subEnqueteDescricao ,true); ?>
</head>
  <body style="font-family: Arial, Helvetica, sans-serif; border-top: 1px; ">


    <table style="margin: 0 auto;font-size:14px; width:1200px; margin-top:1px;" width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="width: 210px;" valign="top">
         <!--  -->
        </td>
        <td align="center">
          <div style="font-size:18px;text-align: center;"> 
            <strong>Enquete Respostas</strong>
            <br/>            
          </div>
        </td>    
        <td align="right" valign="top" style="font-size: 11px;width: 220px;">
          Enquete <?=date("d/m/Y H:i:s")?><br> Usuário: <br>
      </tr>
    </table>

    <div style="margin: 0 auto;font-size:14px; width:1200px; margin-top:15px;">

      <table width="100%" border="1" class="table tabela" cellspacing="0" cellpadding="0">              
               
         <tr>
          	<td style="font-weight: bold !important;background-color: #f4f2f2 !important;" colspan="8">  Descrição da Enquete: 
          	<?php 
          		foreach ($array as $key => $campos){
          			if($key==0){
          				echo $campos['descricao'].'<br />';
          	    	}
          		}?>
			</td>
         </tr>
         <tr>                  
          <td style="background-color: #f4f2f2 !important;"><b>Nº</b></td>
          <td style="background-color: #f4f2f2 !important;"><b>Respostas</b></td>
          <!-- <td style="background-color: #f4f2f2 !important;"><b>Respostas</b></td> --> 
          <td style="background-color: #f4f2f2 !important;" align="center"><b>Nota</b></td> 
                    
      </tr>

      <?php
      $cont = 1;
      
      foreach ($array as $key => $campos){
        ?>
        <tr>
          <td><b><?=$cont;?></b></td> 
          
          <td><?=$campos['subEnqueteDescricao'];?></td>  
               
         <!--  <td  align="center"></td> -->
          <!--moeda2Input($valorTotal) : '0,00'; -->        
          
          <td align="center"><?=$campos['nota'];?></td>
        
      </tr>
      <?php
      $cont++;
    } ?>
      
     
    </table>     
      <strong><!-- <?=$geradoEm;?> --></strong>
      <br><br>
    </div>
           
  </body>
</html>