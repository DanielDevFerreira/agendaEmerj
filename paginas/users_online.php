<html>

<!--RECARREGA A PÁGINA A CADA 3 MINUTOS PARA ATUALIZAR O CONTADOR DE USUÁRIOS ONLINE-->
<meta http-equiv="refresh" content="180">
<img width="5%;" src="../imagens/circular-shape-silhouette.png  ">

</html>
<?php

//faça a inclusão da página de conexão criada logo acima
include ("../conexao.php"); 
 
   //determina um tempo para a variável $tempo
   $tempo = time();
 
   // pega o IP do usuário
   $ip = $_SERVER['REMOTE_ADDR'];
   //faz uma consulta para verificar se o ip já existe no banco de dados
   $verifica = mysqli_query($conn, "SELECT * FROM online WHERE ip ='$ip'");

   //retorna a quantidade de linhas da consulta ou seja, pode retornar 0 ou 1 linha
   $linhas  = mysqli_num_rows($verifica); 

   //se não existir o ip no banco ele grava um com um tempo determinado
   if($linhas == 0)
   { 
      // gravando o IP e o tempo no DB
      $acrescenta = mysqli_query($conn, "INSERT INTO online (ip, tempo) VALUES ('$ip','$tempo')");
   }
   else
   { 
      // se o IP já existe ele o pega e atualiza o tempo no DB no IP selecionado
      //pega o IP retornado da consulta
    
       while ($row = mysqli_fetch_assoc($verifica)){
        
       }

      //faz um update para o registro do IP existente
      $atualiza = mysqli_query($conn, "UPDATE online SET tempo ='$tempo' WHERE ip='$ip'"); 
   }

   //deleta a linha que não foi atualizada no tempo de 40 segundos
   mysqli_query($conn, "DELETE FROM online WHERE tempo <'$tempo'".-"120");

   //faz uma consulta para mostrar quantos estão on-line 
   $online = mysqli_query($conn, "SELECT * FROM online"); 
   //retorna o número de linhas que será a quantidade de usuários on-line nesse momento
   $agora = mysqli_num_rows($online);
   if($agora==1)
   {
      //para ficar mais amigável se tiver somente 1 pessoa on-line
      echo "Usuários Online: ".$agora." ";
   }
   else
   {
      //exibe todos os usuários que estão on-line
      echo  "Usuários Online: ".$agora." ";
   }
?>