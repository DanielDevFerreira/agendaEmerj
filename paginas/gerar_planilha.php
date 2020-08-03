
 <?php
	session_start();
	// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
    date_default_timezone_set('America/Sao_Paulo');
	$inicio = date("Y/m/d", strtotime($_GET['data_inicio']));
	$fim = date("Y/m/d", strtotime('+1 day',strtotime( $_GET['data_fim'])));
   
	$tipo = $_GET['tipo'];
	include_once('../conexao.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<title>Agenda dos Auditórios</title>
	<head>
	<body>
        <?php
		if(empty($_GET['data_inicio']) && empty($_GET['data_fim'])){
			echo '<Script>window.alert ("Por Favor, informe o campo data para gerar o relatório")
            window.location.href="principal.php"; 
            </script>';
			return false;
		}
		else{
		if(isset($_REQUEST['data_inicio']) && isset($_REQUEST['data_fim'])){

		//CHECA SE DATA/HORA INICIAL É MENOR QUE A DATA/HORA FINAL, CASO SIM NÃO PERMITE GERAR
			if($inicio > $fim){
				echo  "<script> window.alert ('Data inicial não pode ser maior que a data final!'); 
						window.location.href='principal.php'
					</script>";
				return false;
			}

			if ($tipo == 'aca')
			{
				$resultado_eventos = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'ACA' AND status != 5 ORDER BY start";
			}
			else if ($tipo == 'nra')
			{
				$resultado_eventos = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'NRA' AND status != 5 ORDER BY start";
			}
			else if ($tipo == 'aps')
			{
				$resultado_eventos = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'APS' AND status != 5 ORDER BY start";
			}
			else if ($tipo == 'apv')
			{
				$resultado_eventos = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'APV' AND status != 5 ORDER BY start";
			}
	
			else if ($tipo == 'tp')
			{
				$resultado_eventos = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'TP' AND status != 5 ORDER BY start";
			}
	
			else if ($tipo == 'jnc')
			{
				$resultado_eventos = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'JNC' AND status != 5 ORDER BY start";
			}
	
			else if ($tipo == 'tran')
			{
				$resultado_eventos = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND status = 3 AND status != 5 ORDER BY start";
			}
			else if ($tipo == 'canc')
			{
				$resultado_eventos = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND status = 2 AND status != 5 ORDER BY start";
			}
			else 
			{
				$resultado_eventos = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND status != 5 ORDER BY start";
				$nomeaud = "de todos os Auditórios";
			}
			
			$resultado_eventos = mysqli_query($conn , $resultado_eventos);
	
	
			//SE NÃO HOUVER RESULTADOS NO FILTRO DE BUSCA SELECIONADO, EMITE ALERT E RETORNA PARA A PRINCIPAL
	
			if(mysqli_num_rows($resultado_eventos) == 0){
				echo '<Script>window.alert ("Não há resultados para o filtro selecionado!")
				window.location.href="principal.php"; 
				</script>';
				break;
			}	
		
		// Criamos uma tabela HTML com o formato da planilha
		$html = '';
		$html .= '<table border="1">';
		$html .= '<tr>';
		$html .= '<td colspan="15"> <img width="6.6%" heigth="6.6%" src="http://www.emerj.com.br/agenda/imagens/logo-relatorio.png"> <center><strong>EMERJ</strong></center>';
		$html .= '<center><strong>Agenda dos Auditórios</strong></center></td>';
		$html .= '</tr>';
		

		
		$html .= '<tr>';
        $html .= '<td><b>Início</b></td>';   
        $html .= '<td><b>Fim</b></td>'; 
		$html .= '<td><b>Auditório</b></td>';
		$html .= '<td><b>Título</b></td>';       
        $html .= '<td><b>Situação</b></td>';
		$html .= '<td><b>Setor</b></td>';
		$html .= '<td><b>Responsável</b></td>';	
		$html .= '<td><b>Telefone</b></td>';
		$html .= '<td><b>E-mail</b></td>';
		$html .= '<td><b>Cadastrado por</b></td>';		
		$html .= '<td><b>Data cadastro</b></td>';
		$html .= '<td><b>Modificado por</b></td>';
		$html .= '<td><b>Solicitou cancelamento</b></td>';
		$html .= '<td><b>Observação</b></td>';
		$html .= '<td><b>Última modificação</b></td>';
		$html .= '</tr>';

		//SE HOUVER RESULTADOS, PROSSEGUE NORMAL
		
		while($row_eventos = mysqli_fetch_assoc($resultado_eventos)){
			
			if ($row_eventos ["status"] == 0)  { 
								
				$status  = "Pré-reserva" ;
			
			}else if ($row_eventos ["status"] == 1){
				
				$status = "Confirmado";

			}else if ($row_eventos ["status"] == 3){
				
				$status = "Transmissão de sinal";	
		
			}else   {

				$status = "Cancelado";

			}

			
			if ($row_eventos['solicitante'] == ''){
				$exibeSolicitante = 'Não solicitado';
				$exibeObs = '-';
			}
			else {
				$exibeSolicitante = $row_eventos['solicitante'];
				$exibeObs = $row_eventos['obs'];
			}

			$html .= '<tr>';
            $html .= '<td>'.$row_eventos["start"].'</td>';
            $html .= '<td>'.$row_eventos["end"].'</td>';
            $html .= '<td>'.$row_eventos["aud"].'</td>';
			$html .= '<td>'.$row_eventos["title"].'</td>';
           	$html .= '<td>'.$status.'</td>';
			$html .= '<td>'.$row_eventos["setor"].'</td>';
			$html .= '<td>'.$row_eventos["responsavel"].'</td>';			
			$html .= '<td>'.$row_eventos["telefone"].'</td>';
			$html .= '<td>'.$row_eventos["email"].'</td>';
			$cadastradoPor = $row_eventos["cadastradoPor"]==null?"-":$row_eventos["cadastradoPor"];
			$html .= '<td>'.$cadastradoPor.'</td>';
			$dtCad = $row_eventos["dataCadastro"]=='0000-00-00 00:00:00'?"-":date("d/m/Y H:i", strtotime($row_eventos["dataCadastro"]));
			$html .= '<td>'.$dtCad.'</td>';			
			$html .= '<td>'.$row_eventos["modificadoPor"].'</td>';
			$html .= '<td>'.$exibeSolicitante.'</td>';
			$html .= '<td>'.$exibeObs.'</td>';
			$data = $row_eventos["modificadoEm"]==null?"-":date("d/m/Y H:i", strtotime($row_eventos["modificadoEm"]));
		    $html .= '<td>'.$data.'</td>';
			$html .= '</tr>';
			;
			
		}
		// Configurações header para forçar o download
		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
		header ("Content-type: application/x-msexcel");
		header ('Content-Disposition: attachment; filename=agenda_dos_auditorios"' . date('d_m_Y_H_i') . '.xls');
		header ("Content-Description: PHP Generated Data" );
		// Envia o conteúdo do arquivo
		echo $html;
        }
        }
		exit; ?>
	</body>
</html>