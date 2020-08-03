
<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if( isset($_SESSION['usuarioNome'])){
    $user   = $_SESSION['usuarioNome'];
}

include_once('../conexao.php');
include("../pdf/mpdf.php");


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

    $user = $_SESSION['usuarioNome'];//sem o encode dá erro de charset
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

function getTabela($conn)
{

    try //Tratamento de erro
    {

        $select = "SELECT * FROM usuarios ORDER BY nome";

        $color  = false;
        $retorno = "";
        $retorno .= "<h2 style=\"text-align:center\"></h2>";
        $retorno .= "<table class=\"tabela\" width=\"100%\" cellpadding=\"8\" border=\"1\">
		<thead class=\"teste\">
		<tr>
		<td colspan=\"14\"><h2>RELATÓRIO DOS USUÁRIOS DA AGENDA DOS AUDITÓRIOS</h2></td>		
		</tr>
		</thead>
						
		<thead>
		<tr>
		<td width=\"1%\">N</td>
		<td width=\"19%\">NOME</td>	
		<td width=\"20%\">E-MAIL</td>			
        <td width=\"15%\">NÍVEL</td>		
		<td width=\"15%\">DATA CRIAÇÃO</td>
		<td width=\"15%\">ÚLTIMA MODIFICAÇÃO</td>
		<td width=\"15%\">ÚLTIMO ACESSO</td>

		
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
            return false;
        }

        //SE HOUVER RESULTADOS, PROSSEGUE NORMAL

        while($reg = mysqli_fetch_assoc($result)){


            //VARIÁVEIS PARA AJUSTE DE EXIBIÇÃO NO RELATÓRIO
            $dt_inicio = date("d/m/Y H:i",strtotime($reg['created']));
            if ($reg['modified'] == NULL){
                $dt_final  = "-";
            }
            else{
                $dt_final  = date("d/m/Y H:i",strtotime($reg['modified']));
            }

            if ($reg['ultimoAcesso'] == NULL){
                $log_acesso  = "-";
            }
            else{
                $log_acesso = date("d/m/Y H:i",strtotime($reg['ultimoAcesso']));
            }


            if ($reg['niveis_acesso_id'] == 1) {
                $exibeStatus = "Administrador";
            } else if ($reg['niveis_acesso_id'] == 2) {
                $exibeStatus = "Colaborador";
            } else if ($reg['niveis_acesso_id'] == 3) {
                $exibeStatus = "Secge";
            }else if ($reg['niveis_acesso_id'] == 4) {
                $exibeStatus = "Consulta";
            }


            $retorno .= ($color) ? "<tr>" : "<tr class=\"zebra\">";
            $retorno .= "<td align=\"center\">$contador</td>";
            $retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$reg['nome']}</td>";
            $retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$reg['email']}</td>";
            $retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$exibeStatus}</td>";
            $retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$dt_inicio}</td>";
            $retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$dt_final}</td>";
            $retorno .= "<td align=\"center\" class=\"converte_maiusculo\">{$log_acesso}</td>";
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


$mpdf=new mPDF('c','A4','','',5,5,25,15,5,5); //Margem para o PDF no formato A4 e orientação paisagem (A4-L)

$mpdf->SetDisplayMode('fullpage');

$css = file_get_contents('../css/relatorio.css'); //Aponto para o arquivo Css 

$mpdf->WriteHTML($css,1);	// Parametro para importar CSS -> importante!

$mpdf->SetHTMLHeader(getHeader($conexao, 'O',true)); //Faço exibição do cabeçalho 

$mpdf->SetHTMLFooter(getFooter($retorno,$user)); //Faço exibição do rodapé

$mpdf->WriteHTML(getTabela($conn)); //Faço exibição da tabela com os dados do Sistema

$mpdf->Output('relatorio-agenda-eventos"' . date('d_m_Y_H_i') . '.pdf', 'D');//Força o download e acrescenta a data e hora atual no nome do arquivo

exit;

?>

