<?php
session_start();

//Incluir conexao com BD
include_once("../conexao.php");

$recebeEmail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$recebeSenha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);
$recebeSenha = md5($recebeSenha);

//pega a hora atual e atualiza o log no banco
date_default_timezone_set('America/Sao_Paulo');
$dateTime = date('Y-m-d H:i:s');



// Verifica se ja contem um email na base de dados, para não haver emails duplicados de usuários

$consultaBanco = "SELECT * FROM usuarios WHERE email = '$recebeEmail'";
    $resultado = mysqli_query($conn, $consultaBanco);
    if(!$resultado) {
        die("Falha na consulta ao banco");
    }
    $verificaBanco = mysqli_num_rows($resultado);

    if($verificaBanco == 1){

//Buscar na tabela usuario o usuário que corresponde com os dados digitado no formulário
$result_usuario = "UPDATE usuarios SET senha = '$recebeSenha', modified = '$dateTime', controle = 0 WHERE email = '$recebeEmail'";
$resultado_usuario = mysqli_query($conn, $result_usuario);
echo "<script> alert('Usuário $recebeEmail redefinido com sucesso!');
    history.back()
            
        </script>";
return false;
} else {

    echo "<script> alert('O e-mail $recebeEmail não está cadastrado!');
            
            history.back()
            
            
    
    </script>";

}