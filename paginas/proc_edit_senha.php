<?php
session_start();

//Incluir conexao com BD
include_once("../conexao.php");


$recebeEmail = $_SESSION['usuarioEmail'];
$senhaAntiga = $_SESSION['senha'];
$recebeSenha1 = filter_input(INPUT_POST, 'senha1', FILTER_SANITIZE_SPECIAL_CHARS);
$recebeSenha2 = filter_input(INPUT_POST, 'senha2', FILTER_SANITIZE_SPECIAL_CHARS);
if ($recebeSenha1 != $recebeSenha2){
    echo "<script> alert('Senhas não conferem!');
    history.back()
            
        </script>";
return false;
}
else if (md5($recebeSenha1) == $senhaAntiga){
    echo "<script> alert('Senha nova não pode ser igual a antiga!');
    history.back()
            
        </script>";
return false;
}
else {
    $recebeSenhaNova = md5($recebeSenha1);
}


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
$result_usuario = "UPDATE usuarios SET senha = '$recebeSenhaNova', modified = '$dateTime' WHERE email = '$recebeEmail'";
$resultado_usuario = mysqli_query($conn, $result_usuario);
echo "<script> alert('Senha alterada com sucesso!');
    history.back()
            
        </script>";
return false;
} else {

    echo "<script> alert('Erro na alteração da senha!');
            
            history.back()
            
            
    
    </script>";

}