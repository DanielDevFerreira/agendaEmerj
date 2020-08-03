<?php
session_start();
    header('Content-Type: text/html; charset=UTF-8');

//Incluir conexao com BD
include_once("../conexao.php");
date_default_timezone_set('America/Sao_Paulo');

$recebeSeuNome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$confereSeuNome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_MAGIC_QUOTES);
$recebeEmail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$recebeSenha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);
$niveis_acesso_id = filter_input(INPUT_POST, 'niveis_acesso_id', FILTER_SANITIZE_STRING);
$recebeSenha = md5($recebeSenha);
$controle = 0;//variável que quando 0 força o usuário a redefinir a senha
$created = date('Y-m-d H:i:s');



// Verifica se ja contem um email na base de dados, para não haver emails duplicados de usuários

$consultaBanco = "SELECT * FROM usuarios WHERE email = '$recebeEmail'";
    $resultado = mysqli_query($conn, $consultaBanco);
    if(!$resultado) {
        die("Falha na consulta ao banco");
    }
    $verificaBanco = mysqli_num_rows($resultado);

    if($verificaBanco == 1){

echo "<script> alert('O endereço de e-mail $recebeEmail já consta em nossa base de dados!');
            
            history.back()
            
            
    
    </script>";

return false;
} else {

//Agora vamos inserir os dados no banco

    $inserir    = "INSERT INTO usuarios ";
        $inserir    .= "( nome, email, senha, controle, niveis_acesso_id, created) ";
        $inserir    .= "VALUES ";
        $inserir    .= "('$confereSeuNome', '$recebeEmail', '$recebeSenha', '$controle', '$niveis_acesso_id', '$created')";

        $operacao_inserir = mysqli_query($conn,$inserir);
        if(!$operacao_inserir) {
            die("Erro no banco");
        }
    

    // mensagens de alert apos ação de cadastrar usuário
	if(mysqli_insert_id($conn)){
		echo "<script> alert('Usuário $recebeEmail cadastrado com sucesso!');
        history.back()
        </script>";
        return false;
	}else{
		echo "<script> alert('Erro ao cadastrar usuário!');
        history.back()
        </script>";
        return false;
	
    }
    }