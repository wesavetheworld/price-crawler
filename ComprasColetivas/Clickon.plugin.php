<?php 
class Clickon
{
	function getIdDoSite($obj)
	{
		return $obj->applyRegex('index.coupondisplay:buycoupon/(D.[0-9]{1,9})"\s*',1,1);
	}
	
	function getNomeEstabelecimento($obj)
	{
		return $obj->applyRegex('<dl class="info-map addressCorner"><dt>(.{0,150})</dt>',1);
	}
	
	function getInflatedPrice($obj)
	{
		return $obj->applyRegex('<a title=".{5,255}"\s*rel="alternate"\s*href=".{0,255}">R\$([0-9,\,]{3,6})</a></p></li>', 1,1);
	}
	
	function getCurrentPrice($obj)
	{
		return $obj->applyRegex('<div id="offer-buy"><div class="outer"><div class="boxCorner" id="box-buy"><h3><a title=".{0,255}" href=".{0,255}">R\$([0-9,\,]{3,6})</a></h3>',1);
	}
	
	function getTotalVendido($obj)
	{
		$quantidade = $obj->applyRegex('<div class="boxCorner" id="box-qt"><h5>([0-9.,]{1,8})\s*.{0,50}</h5><div',1);
		return intval($quantidade);
		
	}
	
	function getUrlOfertaNacional($obj)
	{
		return $obj->applyRegex('<a href="(.{1,200}[^"])">Oferta Nacional</a></li>',1);
	}
	
	function getUrlMaisOfertas($obj)
	{
		$maisOfertas = $obj->applyRegex('(<div class="dealCorner" id="side-deal1">|<div class="sub-sidedeal" id="side-deal2">)<style>\s*.{0,1000}\s*</style>.{0,80}<a rel="alternate" title=".{0,250}" href="(.{1,200}[^"])">\s*',2);
		if (is_array($maisOfertas))
		{
			return implode("<br>", $maisOfertas);
		}
		else 
		{
			return $maisOfertas;
		}
		
		
	}
	
	function getUrlDasCidades($obj)
	{
		return "Sem suporte no momento. Exibição da lista via AJAX, precisaria incorporar ao um componente (tal como o HTMLUnit) para manipular requisoes Ajax, nada que impeça um BOT comercial de fazê-lo.";
	}
	
	
}