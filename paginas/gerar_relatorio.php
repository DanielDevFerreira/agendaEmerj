
 <?php
	session_start();
	// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
    date_default_timezone_set('America/Sao_Paulo');

    if( isset($_SESSION['usuarioNome'])){
        $user   = utf8_encode($_SESSION['usuarioNome']);
    }
	$inicio = date("Y/m/d", strtotime($_GET['data_inicio']));
	$fim = date("Y/m/d", strtotime('+1 day',strtotime( $_GET['data_fim'])));

	//PARA EXIBIR AS DATAS NO RELATÓRIO NO FORMATO d/m/Y
	$inicioConvert = date("d/m/Y", strtotime($_GET['data_inicio']));
	$fimConvert = date("d/m/Y", strtotime('+1 day',strtotime( $_GET['data_fim'])));
	////////////////////////////////////////////////////
		
	$tipo = $_GET['tipo'];
	include_once('../conexao.php');
	include("../pdf/mpdf.php");


	//CHECA SE AS DATAS SÃO VAZIAS, IGUAIS OU SE A DATA INICIAL É MAIOR QUE A FINAL, CASO SIM RETORNA FALSO
	
	if(empty($_GET['data_inicio']) && empty($_GET['data_fim'])){
			echo '<Script>window.alert ("Por Favor! , Informe o campo data para gerar o relatório ")
            window.location.href="principal.php"; 
            </script>';
			return false;
	}
	else if($inicio === $fim){
		echo  "<script> window.alert ('Data inicial e data final não podem ser iguais!'); 
				window.location.href='principal.php'
			</script>";
		return false;	  
	}
	else if($inicio > $fim){
		echo  "<script> window.alert ('Data final não pode ser menor que a data inicial!'); 
				window.location.href='principal.php'
			</script>";
		return false;
	}
	
// ------------------------------------------------------------- FUNÇÃO PARA GERAR CABEÇALHO ----------------------------------------------------------------
function getHeader($conn)
{  
	
   
    $retorno = "<html>";

	$retorno = "

	<table class=\"tbl_header\">  
		   <tr>  
			 <td align=\"left\" width=\"5%\"><img src=\"../imagens/logo-relatorio.png\" width=\"50\"></td>  
			 <td align=\"center\"><h1>EMERJ - ESCOLA DA MAGISTRATURA DO ESTADO DO RIO DE JANEIRO</h1>
			 <h1>DETEC - Departamento de Tecnologia de Informação e Comunicação</h1>
								 <h1> AGENDA DOS AUDITÓRIOS </h1>								 
			</td>
			<td align=\"right\" width=\"5%\"></td> 
		   </tr>  
		 </table>";
	
	$retorno .= "<body>";
   return $retorno;  
 } 

 // ------------------------------------------------------------- FUNÇÃO PARA GERAR RODAPÉ ----------------------------------------------------------------
function getFooter($user)
{  

   date_default_timezone_set('America/Sao_Paulo'); 
   $user = utf8_encode($_SESSION['usuarioNome']);//sem o encode dá erro de charset
   $data = date('d/m/Y');
   $hora = date('H:i');
   $retorno = "<table class=\"tbl_footer\" width=\"1200\">  
		   <tr>  
			 <td align=\"left\">Gerado por $user em $data às $hora</td>  
			 <td align=\"right\">Página: {PAGENO} de {nb}</td>  
		   </tr>  
		 </table>";
		 
   return $retorno;  
 }

// ----------------------------------------------------------- FUNÇÃO PARA GERAR TABELAS COM FOREACH -------------------------------------------------------
 
function getTabela($conn, $tipo, $inicio, $fim, $inicioConvert, $fimConvert)
{  

	try //Tratamento de erro
	{
	
    if ($tipo == 'aca')
	{
		$select = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'ACA' AND status != 5 ORDER BY start";
		$nomeaud = "DO AUDITÓRIO ANTONIO CARLOS: DE $inicioConvert A $fimConvert";
	}
	else if ($tipo == 'nra')
	{
		$select = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'NRA' AND status != 5 ORDER BY start";
		$nomeaud = "DO AUDITÓRIO NELSON RIBEIRO ALVES: DE $inicioConvert A $fimConvert";
	}
	else if ($tipo == 'aps')
	{
		$select = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'APS' AND status != 5 ORDER BY start";
		$nomeaud = "DO AUDITÓRIO PENALVA SANTOS: DE $inicioConvert A $fimConvert";
		
	}else if ($tipo == 'apv')
	{
		$select = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'APV' AND status != 5 ORDER BY start";
		$nomeaud = "DO AUDITÓRIO PAULO VENTURA: DE $inicioConvert A $fimConvert";
	}else if ($tipo == 'tp')
	{
		$select = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'TP' AND status != 5 ORDER BY start";
		$nomeaud = "TRIBUNAL PLENO: DE $inicioConvert A $fimConvert";
	}else if ($tipo == 'jnc')
	{
		$select = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND sigla = 'JNC' AND status != 5 ORDER BY start";
		$nomeaud = "DO JOSÉ NAVEGA CRETTON : DE $inicioConvert A $fimConvert";
	}


	else if ($tipo == 'canc')
	{
		$select = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND status = 2 ORDER BY start";
		$nomeaud = "DAS RESERVAS CANCELADAS: DE $inicioConvert A $fimConvert";
	}
	else if ($tipo == 'tran')
	{
		$select = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND status = 3 ORDER BY start";
		$nomeaud = "DAS TRANSMISSÕES DE SINAL: DE $inicioConvert A $fimConvert";
	}
	else 
	{
		$select = "SELECT * FROM events WHERE start BETWEEN '$inicio' AND '$fim' AND status != 5 ORDER BY start";
		$nomeaud = "DE TODOS OS AUDITÓRIOS: DE $inicioConvert A $fimConvert";
	}
	
		  
      $color  = false;  
      $retorno = "";	 
	  $retorno .= "<h2 style=\"text-align:center\"></h2>";  
      $retorno .= "<table class=\"tabela\" width=\"100%\" cellpadding=\"8\" border=\"1\">
		<thead class=\"teste\">
		<tr>
		<td colspan=\"16\"><h2>RELATÓRIO $nomeaud</h2></td>		
		</tr>
		</thead>
						
		<thead>
		<tr>
		<td width=\"1%\">N</td>
		<td width=\"5%\">INÍCIO</td>	
		<td width=\"5%\">FIM</td>			
        <td width=\"10%\">AUDITÓRIO</td>		
		<td width=\"15%\">TÍTULO</td>
		<td width=\"5%\">SITUAÇÃO</td>
		<td width=\"5%\">SETOR</td>
		<td width=\"5%\">RESPONSÁVEL</td>
        <td width=\"9%\">TELEFONE</td>
		<td width=\"5%\">E-MAIL</td>
		<td width=\"5%\">CADASTRADO POR</td>
		<td width=\"5%\">DATA CADASTRO</td>
        <td width=\"5%\">MODIFICADO POR</td>
        <td width=\"5%\">SOLICITOU CANCELAMENTO</td>
        <td width=\"5%\">OBSERVAÇÃO</td>
        <td width=\"5%\">ÚLTIMA MODIFICAÇÃO</td>

		
		</tr>
		</thead>
        <tbody>";
		
        $contador = 1; //Criei contador para fazer ordenação da tabela
				
		$result = mysqli_query($conn , $select);

		//SE NÃO HOUVER RESULTADOS NO FILTRO DE BUSCA SELECIONADO, EMITE ALERT E RETORNA PARA A PRINCIPAL

		if(mysqli_num_rows($result) == 0){
			echo '<Script>window.alert ("Não há resultados para o filtro selecionado!")
			window.location.href="principal.php"; 
			</script>';
			break;
		}

		//SE HOUVER RESULTADOS, PROSSEGUE NORMAL
		
		while($reg = mysqli_fetch_assoc($result)){


			//VARIÁVEIS PARA AJUSTE DE EXIBIÇÃO NO RELATÓRIO
			$dt_inicio = date("d/m/Y H:i",strtotime($reg['start']));
			$dt_fim    = date("d/m/Y H:i",strtotime($reg['end']));
			$dt_final  = date("d/m/Y H:i",strtotime($reg['modificadoEm']));
			if ($reg['dataCadastro'] == '0000-00-00 00:00:00'|| $reg['dataCadastro'] == NULL || $reg['cadastradoPor'] == NULL){
				$dt_cad = '-';
				$cadastradoPor = '-';
			}
			else{
				$dt_cad = date("d/m/Y H:i",strtotime($reg['dataCadastro']));
				$cadastradoPor = $reg['cadastradoPor'];
			}

			if ($reg['solicitante'] == ''){
				$exibeSolicitante = 'NÃO';
				$exibeObs = '-';
			}
			else {
				$exibeSolicitante = $reg['solicitante'];
				$exibeObs = $reg['obs'];
			}

			if ($reg['status'] == 0) {
				$exibeStatus = "Pré-reserva";
			} else if ($reg['status'] == 1) {
				$exibeStatus = "Confirmado-FRM";
			}else if ($reg['status'] == 2) {
				$exibeStatus = "Cancelado";
			} else if ($reg['status'] == 3) {
				$exibeStatus = "Transmissão de sinal";
			}

			if ($reg['setor'] == '') {
				$exibeSetor = "-";
			}
			else{
				$exibeSetor = $reg['setor'];
			}
			if ($reg['responsavel'] == ''){
				$exibeResponsavel = "-";
			}
			else{
				$exibeResponsavel = $reg['responsavel'];
			}
			if ($reg['telefone'] == ''){
				$exibeTelefone = "-";
			}
			else{
				$exibeTelefone = $reg['telefone'];
			}
			if ($reg['email'] == ''){
				$exibeEmail = "-";
			}
			else{
				$exibeEmail = $reg['email'];
			}
			if ($reg['modificadoPor'] == ''){
				$exibeModificador = "-";
			}
			else{
				$exibeModificador = $reg['modificadoPor'];
			}
			/////////////////////////////////////////////////

			$retorno .= ($color) ? "<tr>" : "<tr class=\"zebra\">";  
			$retorno .= "<td align=\"center\">$contador</td>";			
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$dt_inicio}</td>";
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$dt_fim}</td>";
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$reg['aud']}</td>";
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$reg['title']}</td>";
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$exibeStatus}</td>";
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$exibeSetor}</td>";
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$exibeResponsavel}</td>";
            $retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$exibeTelefone}</td>";
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$exibeEmail}</td>";
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$cadastradoPor}</td>";
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$dt_cad}</td>";
			$retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$exibeModificador}</td>";
            $retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$exibeSolicitante}</td>";
            $retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$exibeObs}</td>";
            $retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$dt_final}</td>";
			$retorno .= "</tr>";  
			$color = !$color;  
			$contador++;
         }
			
        $retorno .= "</table>";
		$retorno .= "<br>"; 
		$retorno .= "</body>"; 
		$retorno .= "</html>"; 
		return $retorno;  
		
	}catch(mysqli_sql_exception $e)
	{
		echo $e; //Caso tenha erro, exibo mensagem
	}
}
	
//==============================================================
//==============================================================

$mpdf=new mPDF('c','A4-L','','',5,5,25,15,5,5); //Margem para o PDF no formato A4 e orientação paisagem (A4-L)

$mpdf->SetDisplayMode('fullpage');

$css = file_get_contents('../css/relatorio.css'); //Aponto para o arquivo Css 

$mpdf->WriteHTML($css,1);	// Parametro para importar CSS -> importante!

$mpdf->SetHTMLHeader(getHeader($conexao, 'O',true)); //Faço exibição do cabeçalho 

$mpdf->SetHTMLFooter(getFooter($retorno,$user)); //Faço exibição do rodapé

$mpdf->WriteHTML(getTabela($conn, $tipo, $inicio, $fim, $inicioConvert, $fimConvert)); //Faço exibição da tabela com os dados do Sistema

$mpdf->Output('relatorio-agenda-eventos"' . date('d_m_Y_H_i') . '.pdf', 'D');//Força o download e acrescenta a data e hora atual no nome do arquivo

exit;

?>

