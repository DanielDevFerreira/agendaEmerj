<?php

session_start();

if (!isset($_SESSION['usuarioNome']) or !isset($_SESSION['usuarioId']) or !isset ($_SESSION['usuarioNiveisAcessoId']) or !isset($_SESSION['usuarioEmail'])){

    unset(

        $_SESSION['usuarioId'],
        $_SESSION['usuarioNome'],
        $_SESSION['usuarioNiveisAcessoId'],
        $_SESSION['usuarioEmail']
    );

    //redirecionar o usuario para a página de login

    header("Location: ../index.php");

}

date_default_timezone_set('America/Sao_Paulo');
//Incluir conexao com BD
include_once("../conexao.php");

// FUNÇÕES QUE CONVERTEM FORMATOS DAS DATAS E CRIAM O PERÍODO DE TOLERÂNCIA DE 30 MINUTOS ENTRE EVENTOS
function converteDataMenor($date_str)
{
	$date = \DateTime::createFromFormat('d/m/Y H:i', $date_str);
	$date->modify('-29 minutes');
	return $date->format('Y-m-d H:i:s');	
}
function converteDataMaior($date_str)
{
	$date = \DateTime::createFromFormat('d/m/Y H:i', $date_str);
	$date->modify('+29 minutes');
	return $date->format('Y-m-d H:i:s');	
}
function converteData($date_str)
{
	$date = \DateTime::createFromFormat('d/m/Y H:i', $date_str);
	return $date->format('Y-m-d H:i:s');	
}

$nivelLogado = $_SESSION['usuarioNiveisAcessoId'];//nível 1 = administrador, nível 2 = funcionário, nível 3 = secge, nível 4 consulta>
$dateTime = date('Y-m-d H:i:s');//FORMATO AMERICANO PARA COMPARAÇÃO DE DATAS

$title = filter_input(INPUT_POST, 'evento', FILTER_SANITIZE_STRING);
$responsavel = filter_input(INPUT_POST, 'responsavel', FILTER_SANITIZE_STRING);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);

// -- NECESSÁRIO CONVERTER PARA FORMATO AMERICANO PARA COMPARAÇÃO ENTRE DATAS NO PHP --//
$start = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING);
$end = filter_input(INPUT_POST, 'end', FILTER_SANITIZE_STRING);
$startConvert = converteData($start);
$endConvert = converteData($end);
// criação das variáveis para a query
$startMenor = converteDataMenor($start);//pega o início do evento menos 29 minutos
$startMaior = converteDataMaior($start);//pega o início do evento mais 29 minutos
$endMenor = converteDataMenor($end);//pega o fim do evento menos 29 minutos
$endMaior = converteDataMaior($end);//pega o fim do evento mais 29 minutos
// ----------------------------------------------------------------------------------- //

$aud = filter_input(INPUT_POST, 'aud', FILTER_SANITIZE_STRING);
$setor = filter_input(INPUT_POST, 'setor', FILTER_SANITIZE_STRING);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

//PEGA O NOME DO AUDITÓRIO SEM A SIGLA
$audConsulta = substr($aud, 4);

//VERIFICA SE O INTERVALO DOS HORÁRIOS DIGITADOS PELO USUÁRIO ESTÃO NO START E NO END DO BANCO, SE ESTIVER NÃO PERMITE CADASTRAR/EDITAR
$verificaInicio = mysqli_query($conn, "SELECT * FROM events WHERE '$startMenor' BETWEEN start AND end AND aud = '$audConsulta' AND status != 2 
OR '$startMaior' BETWEEN start AND end AND aud = '$audConsulta' AND status != 2");

$verificaFim = mysqli_query($conn, "SELECT * FROM events WHERE '$endMenor' BETWEEN start AND end AND aud = '$audConsulta' AND status != 2 
OR '$endMaior' BETWEEN start AND end AND aud = '$audConsulta' AND status != 2");

$linhaInicio = mysqli_num_rows($verificaInicio);
$linhaFim = mysqli_num_rows($verificaFim);


if (($linhaInicio == 0) && ($linhaFim == 0)){

	//CHECA SE DATA/HORA INICIAL É IGUAL OU MENOR A DATA/HORA FINAL, CASO SIM NÃO PERMITE CADASTRAR/EDITAR
	if($startConvert === $endConvert){
		echo  "<script> window.alert ('Data inicial e data final não podem ser iguais!'); 
				 window.location.href='principal.php'
			  </script>";
		return false;	  
	}
	else if($startConvert > $endConvert){
		echo  "<script> window.alert ('Data final não pode ser menor que a data inicial!'); 
				 window.location.href='principal.php'
			  </script>";
		return false;
	}
	else if(($startConvert < $dateTime) || ($endConvert < $dateTime)){
		echo  "<script> window.alert ('Data selecionada anterior a hoje!');
				 window.location.href='principal.php'
			  </script>";
		return false;
	}
	else{
		
	//Converter a data e hora do formato brasileiro para o formato do Banco de Dados
	$data = explode(" ", $start);
	list($date, $hora) = $data;
	$data_sem_barra = array_reverse(explode("/", $date));
	$data_sem_barra = implode("-", $data_sem_barra);
	$start_sem_barra = $data_sem_barra . " " . $hora;

	//$aud_cor = explode(".", $aud);   cor para auditorio
	$status_cor = explode(".", $status);
	$aud_sigla = explode(".",$aud);

	
	$data = explode(" ", $end);
	list($date, $hora) = $data;
	$data_sem_barra = array_reverse(explode("/", $date));
	$data_sem_barra = implode("-", $data_sem_barra);
	$end_sem_barra = $data_sem_barra . " " . $hora;

	mysqli_select_db($conn, 'agenda');
	
	$result_events = 'INSERT INTO events (responsavel, telefone, email, title, color, start, end, aud, setor, status, sigla, cadastradoPor, dataCadastro, nivel_cadastro) 
	VALUES ("'.$responsavel.'","'.$telefone.'","'.$email.'","'.$title.'", "'.$status_cor[1].'", "'.$start_sem_barra.'", "'.$end_sem_barra.'", "'.$aud_sigla[1].'","'.$setor.'","'.$status_cor[0].'","'.$aud_sigla[0].'","'.$_SESSION['usuarioNome'].'","'.$dateTime.'", "'.$_SESSION['usuarioNiveisAcessoId'].'")';
	$resultado_events = mysqli_query($conn, $result_events);

	    $linhaAfetadas = mysqli_affected_rows($conn);

        if($linhaAfetadas !=0){

            echo  "<script> window.alert ('Evento cadastrado com sucesso!'); 
				 window.location.href='principal.php'
			  </script>";



        }else{
            echo  "<script> window.alert ('Erro no cadastro do evento!'); 
				 window.location.href='principal.php'
			  </script>";
        }
	}
	

	
}else  //SE O INTERVALO DOS HORÁRIOS FOR ENCONTRADO NO BANCO PARA AQUELE AUDITÓRIO
{
	echo  "<script> window.alert ('Auditório já reservado no período informado!'); 
				 window.location.href='principal.php'
			  </script>";
}
?>