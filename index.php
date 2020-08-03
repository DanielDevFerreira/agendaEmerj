<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
      
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=11">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        
      
      <!-- inicio Apple Touch Icons-->
    <link rel="apple-touch-icon" sizes="152x152" href="/imagens/logo_m_v2_2_fw_P40_2.ico">
    <link rel="apple-touch-icon" sizes="144x144" href="imagens/logo_m_v2_2_fw_P40_2.ico">
    <link rel="apple-touch-icon" sizes="120x120" href="/imagens/logo_m_v2_2_fw_P40_2.ico">
    <link rel="apple-touch-icon" sizes="114x114" href="/imagens/logo_m_v2_2_fw_P40_2.ico">
    <link rel="apple-touch-icon" sizes="76x76" href="/imagens/logo_m_v2_2_fw_P40_2.ico">
    <link rel="apple-touch-icon" sizes="72x72" href="/imagens/logo_m_v2_2_fw_P40_2.ico">
      
      
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
   
      <link rel="icon" href="imagens/logo_m_v2_2_fw_P40_2.ico">
    
      <link href="css/login.css" rel="stylesheet">
      
      
      <center><img id="foto-emerj" src="imagens/logo_M_v2_2.fw.png" alt="Agenda" title="EMERJ"></center><br>
      
    <title>Agenda dos Auditórios</title>

      
      <!-- font -->
      <link href="https://fonts.googleapis.com/css?family=Passion+One&display=swap" rel="stylesheet">
      
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="js/ie-emulation-modes-warning.js"></script>
      

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

 
<div class="login-form">
    <div class="container">
        <form class="form-signin" method="POST" action="valida.php">
            <h2 style="font-family: 'Arial', cursive;" class="form-signin-heading">Agenda dos Auditórios</h2>
            <center><legend>Login:</legend></center>
            <!-- ALERTS DE LOGIN -->
            <p class="text-center text-danger">
            <?php if(isset($_SESSION['loginSucesso'])){
                echo $_SESSION['loginSucesso'];
                unset($_SESSION['loginSucesso']);
              }?>
              <?php if(isset($_SESSION['loginErro'])){
                echo $_SESSION['loginErro'];
                unset($_SESSION['loginErro']);
              }?>
            </p>
            <p class="text-center text-success">
              <?php 
              if(isset($_SESSION['logindeslogado'])){
                echo $_SESSION['logindeslogado'];
                unset($_SESSION['logindeslogado']);
              }
              ?>
            </p>
            <!-- -->
            <label for="inputEmail" class="sr-only">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
            <label for="inputPassword" class="sr-only">Senha</label> 
            <input type="password" name="senha" id="senha" class="form-control" placeholder="Senha" required>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Acessar</button><br>
            <p>
            <a href="mailto:emerjsite@tjrj.jus.br"><span>Dúvidas ou problemas:</span>
              <span class="glyphicon glyphicon-envelope"></span>
            </a>
                <br><br><center><p><i>Versão: 1.2</i></p></center>

        </form>
    </div>
    </div>
      
      <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>


<!-- formulario 2 -->


 


