<?php 
error_reporting(0);
ini_set("display_errors", "0");
require_once 'global.php';

$bot = new AliceBot;
$resultado = $bot->aliceBotController();
?>
<form method="post" action="<?=$_SERVER['PHP_SELF'] ?>">
<input type="text" size="100" name="url" value="http://www.groupon.com.br/ofertas/sao-paulo---sul-e-oeste"> 
<input type="submit" value="Extrair Dados!" /></form><br>
<hr>
<? 

ob_start("converte");
	if(count($resultado)): 
	    print "<h2>Resultado:</h2>";
		foreach($resultado as $key=>$val)
		{
			print "<b>- {$key}</b><br>{$val}<br><br>";		
		}
	else:
		print "<b>{$resultado}</b>";
	endif;
?><h6>Última atualização nos plugins em 19/10/2011.</h6><?
ob_end_flush();

/**
 * Correção de charset...
 */
function converte($buffer)
{
	$buffer = iconv("utf-8","iso-8859-1",$buffer);
	return $buffer;
}