<?php
	session_start();	
	//Incluindo a conexão com banco de dados
	include_once("conexao.php");

	if (!isset($_SESSION['usuarioNome']) or !isset($_SESSION['usuarioId']) or !isset ($_SESSION['usuarioNiveisAcessoId']) or !isset($_SESSION['usuarioEmail'])){
    
		header("Location: index.php");
		  
	  }
	//se não houver sessão iniciada, redireciona para o index

	//pega a hora atual e atualiza o log no banco
	date_default_timezone_set('America/Sao_Paulo');
	$dateTime = date('Y-m-d H:i:s');

    $urlAcesso = ("index.php");


	$recebeEmail = $_SESSION['usuarioEmail'];

	$recebeSenha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

	$recebeSenhaNova = filter_input(INPUT_POST, 'senha2', FILTER_SANITIZE_SPECIAL_CHARS);
	
	
	if((isset($_POST['senha'])) && (isset($_POST['senha2'])) && (isset($_POST['senha3'])))
	{
		$senha = mysqli_real_escape_string($conn, $_POST['senha']);
		$senha = md5($senha);
		$senhaNova = mysqli_real_escape_string($conn, $_POST['senha2']);
		$senhaNova = md5($senhaNova);
		$senhaConfirma = mysqli_real_escape_string($conn, $_POST['senha3']);
		$senhaConfirma = md5($senhaConfirma);

		
		if($senhaNova != $senhaConfirma){
			echo "<script> alert('Confirmação da senha não confere!');
							history.back()
						
					</script>";
					return false;
		}
		else if ($senha === $senhaNova){
			echo "<script> alert('A senha nova não pode ser igual a senha antiga!');
							history.back()
						
					</script>";
					return false;
		}
		
		$result_senha =  "SELECT senha FROM usuarios WHERE senha = '$senha' AND email = '$recebeEmail'";
		$resultado_senha = mysqli_query($conn, $result_senha);
		
		if(mysqli_num_rows($resultado_senha) != 0){
			//Buscar na tabela usuario o usuário que corresponde com os dados digitado no formulário
			$result_usuario = "UPDATE usuarios SET senha = '$senhaNova', modified = '$dateTime', controle = 1  WHERE email = '$recebeEmail'";
			$resultado_usuario = mysqli_query($conn, $result_usuario);
			//$resultado = mysqli_fetch_assoc($resultado_usuario);
			echo "<script> alert('Alterado com sucesso! Favor efetuar o login');
						window.location.href = 'index.php';
					</script>"; 
            

		}
		else{
			echo "<script> alert('Ocorreu um erro, favor tente novamente!');
							history.back()</script>";
		}
		
		  
		
	}
		
?>