<?php
class Groupon
{
	function getIdDoSite($obj)
	{
		return $obj->applyRegex('<input type="hidden" id="dotdId" name="dotdId" value="\s*([0-9]{3,8})\s*"',1,1);
	}
	
	function getNomeEstabelecimento($obj)
	{
		return $obj->applyRegex('<div class="merchantContact">\s*<h3 class="subHeadline">(.{1,200}[^<])</h3>',1);
	}
	
	function getInflatedPrice($obj)
	{
		//Groupon informa o quanto economiza
		$precoPraticado = self::getCurrentPrice($obj);
		$precoEconomizado = $obj->applyRegex('<tr class="row2">\s*<td class="col1">.{1,15}</td>\s*<td>R\$\s*([0-9.,]{3,9})</td>', 1);
		
		//Preco inflado...
		return ($precoPraticado + $precoEconomizado);
	}
	
	function getCurrentPrice($obj)
	{
		return $obj->applyRegex('<span class="price">\s*.{0,200}<span class="noWrap">R\$\s*([0-9.,]{3,9})</span>',1);
	}
	
	function getTotalVendido($obj)
	{
		return (int)$obj->applyRegex('<div class="soldAmount"\s*>\s*<span id="jDealSoldAmount">\s*([0-9.,]{1,8})\s*</span>',1);		
	}
	
	function getUrlOfertaNacional($obj)
	{
		return $obj->applyRegex('<div class="extraDealDescription">\s*<h3>\s*<a.{1,100}" href="(/ofertas/oferta-nacional/.{1,80}[^"])">\s*<span class="original"',1);
	}
	
	function getUrlMaisOfertas($obj)
	{
		$maisOfertas = $obj->applyRegex('<div class="extraDealDescription">\s*<h3>\s*<a.{1,100}" href="(.{4,250}[^"])"><span class="saving"',1);
		if (is_array($maisOfertas))
		{
			return implode("<br>", $maisOfertas);
		}		
		else
			return $maisOfertas;
	}
	
	function getUrlDasCidades($obj)
	{
		$cidades = $obj->applyRegex('<li class="" onclick="window.location\s*=\s*\'(http://.{5,150}[^\'])\'\s"><span>',1);
		if (is_array($cidades))
		{
			return implode("<br>", $cidades);
		}		
		else
			return $cidades;		
	}
	
	
}