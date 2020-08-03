
 <?php
	session_start();
	// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
    date_default_timezone_set('America/Sao_Paulo');
   
	$tipo = $_GET['tipo'];
	include_once('../conexao.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<title>Usuários - Agenda dos Auditórios</title>
	<head>
	<body>
        <?php

		// Criamos uma tabela HTML com o formato da planilha
		$html = '';
		$html .= '<table border="1">';
		$html .= '<tr>';
		$html .= '<td colspan="6"> <img width="6.6%" heigth="6.6%" src="http://www.emerj.com.br/agenda/imagens/logo-relatorio.png"> <center><strong>EMERJ</strong></center>';
		$html .= '<center><strong>Usuários - Agenda dos Auditórios</strong></center></td>';
		$html .= '</tr>';
		

		
		$html .= '<tr>';
        $html .= '<td><b>Nome</b></td>';   
        $html .= '<td><b>E-mail</b></td>'; 
		$html .= '<td><b>Nível</b></td>';
		$html .= '<td><b>Data criação</b></td>';       
		$html .= '<td><b>Última modificação</b></td>';
		$html .= '<td><b>Último acesso</b></td>';
		$html .= '</tr>';
		
		$resultado_eventos = "SELECT * FROM usuarios ORDER BY nome";
		
		$resultado_eventos = mysqli_query($conn , $resultado_eventos);


		//SE NÃO HOUVER RESULTADOS NO FILTRO DE BUSCA SELECIONADO, EMITE ALERT E RETORNA PARA A PRINCIPAL

		if(mysqli_num_rows($resultado_eventos) == 0){
			echo '<Script>window.alert ("Não há resultados para o filtro selecionado!")
			window.location.href="principal.php"; 
			</script>';
			break;
		}

		//SE HOUVER RESULTADOS, PROSSEGUE NORMAL
		
		while($row_eventos = mysqli_fetch_assoc($resultado_eventos)){
			
			if ($row_eventos ["niveis_acesso_id"] == 1)  { 
								
				$status  = "Administrador" ;
			
			}else if ($row_eventos ["niveis_acesso_id"] == 2){
				
				$status = "Colaborador";
		
			}else if ($row_eventos ["niveis_acesso_id"] == 3){
				
				$status = "Secge";
		
			}
			else{

				$status = "Consulta";

			}

			$html .= '<tr>';
            $html .= '<td>'.$row_eventos["nome"].'</td>';
            $html .= '<td>'.$row_eventos["email"].'</td>';
			$html .= '<td>'.$status.'</td>';
			$data1 = date("d/m/Y H:i", strtotime($row_eventos["created"]));
			$data2 = $row_eventos["modified"]==null?"-":date("d/m/Y H:i", strtotime($row_eventos["modified"]));
			$data3 = $row_eventos["ultimoAcesso"]==null?"-":date("d/m/Y H:i", strtotime($row_eventos["ultimoAcesso"]));
			$html .= '<td>'.$data1.'</td>';
			$html .= '<td>'.$data2.'</td>';
			$html .= '<td>'.$data3.'</td>';
			$html .= '</tr>';
			
		}
		// Configurações header para forçar o download
		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
		header ("Content-type: application/x-msexcel");
		header ('Content-Disposition: attachment; filename=relatorio_usuarios"' . date('d_m_Y_H_i') . '.xls');
		header ("Content-Description: PHP Generated Data" );
		// Envia o conteúdo do arquivo
		echo $html;
        
		exit; ?>
	</body>
</html>