<?php
	session_start();	
	//Incluindo a conexão com banco de dados
	include_once("conexao.php");
	date_default_timezone_set('America/Sao_Paulo');
	$dateTime = date('Y-m-d H:i:s');

					$recebeEmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);

					$recebeSenha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

						if($recebeEmail == NULL ) {
					echo "<script> alert('O Email precisa ser informado!');
							history.back()
							
					</script>";

					return false;

						
					}else if ($recebeSenha == NULL ) {
					echo "<script> alert('A senha precisa ser informada!');
							history.back()
						
					</script>";
					return false;
					}
						
	
	
	if((isset($_POST['email'])) && (isset($_POST['senha']))){
		$usuario = mysqli_real_escape_string($conn, $_POST['email']); //Escapar de caracteres especiais, como aspas, prevenindo SQL injection
		$senha = mysqli_real_escape_string($conn, $_POST['senha']);
		$senha = md5($senha);
		
			
		//Buscar na tabela usuario o usuário que corresponde com os dados digitado no formulário
		$result_usuario = "SELECT * FROM usuarios WHERE email = '$usuario' && senha = '$senha' LIMIT 1";
		$resultado_usuario = mysqli_query($conn, $result_usuario);
		$resultado = mysqli_fetch_assoc($resultado_usuario);
		
		//Encontrado um usuario na tabela usuário com os mesmos dados digitado no formulário
		if(isset($resultado)){
			$_SESSION['usuarioId'] = $resultado['id'];
			$_SESSION['usuarioNome'] = $resultado['nome'];
			$_SESSION['usuarioNiveisAcessoId'] = $resultado['niveis_acesso_id'];
			$_SESSION['usuarioEmail'] = $resultado['email'];
			$controle = $resultado['controle'];

			if ($controle == 1){
				//Atualiza o último acesso do usuário no banco
				$log_usuario = "UPDATE usuarios SET ultimoAcesso = '$dateTime' WHERE email = '$usuario'";
				$resultado_log_usuario = mysqli_query($conn, $log_usuario);
				header("Location: paginas/principal.php");
			}
			else{
				$_SESSION['loginErro'] = "<script> alert('Favor redefinir sua senha!');
				</script>";
				header("Location: alterar_form.php");
			}

		//Não foi encontrado um usuario na tabela usuário com os mesmos dados digitado no formulário
		//redireciona o usuario para a página de login
		}else{	
			//Váriavel global recebendo a mensagem de erro
			$_SESSION['loginErro'] = "Usuário ou senha inválidos!";
			header("Location: index.php");
		}
	//O campo usuário e senha não preenchido entra no else e redireciona o usuário para a página de login
	}else{
		$_SESSION['loginErro'] = "Usuário ou senha inválidos!";
		header("Location: index.php");
	}
?>