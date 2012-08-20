<?php 
function __autoload($className)
{
	$__PROMPT = dirname(__FILE__)."/";
	
	switch($className){
		default:
			$toload = $__PROMPT."$className.inc.php";
	}
	
	if(file_exists($toload))
		require_once $toload;
	else{
		echo "Falha carregando classe <b>$className</b>: Arquivo nno encontrado: $toload<br />";
		return false;
	}
	return true;

}