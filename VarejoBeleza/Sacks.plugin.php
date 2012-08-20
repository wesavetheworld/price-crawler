<?php 
class Sacks
{
	function getNomeDoProduto($obj)
	{
		return $obj->applyRegex('<div class="tituloDescricaoProduto">\s*<h1>\s*(.{0,255})\s*</h1>\s*<a href',1);
	}
	
	function getProdutos($obj)
	{
		 $resultado = array();

		 $subItens = (array)$obj->applyRegex('<td class="refSKU">\s*(.{0,200})\s*</td>\s*<td class="precoSKU">', 1);
		 
		 
		 if (count($subItens))
		 {
		 	foreach ($subItens as $key=>$val)
		 	{
		 		$regexVal = preg_quote($val);
		 		
		 		//Procura o preço do subitem
				$regexProcuraPreco = '<td class="refSKU">\s*'.$regexVal.'\s*</td>\s*<td class="precoSKU">\s*.{0,500}\s*<span class="produtoPrecoVendaDe">.{0,500}\s*<span class="produtoPrecoVendaPor">.{0,6}R\$\s*([0-9,\,]{3,6})\s*.{0,150}</span>\s*<span';
				
				//Procura o botao comprar do subitem
				$regexProcuraDispo = '<td class="refSKU">\s*'.$regexVal.'\s*</td>\s*<td class="precoSKU">\s*.{0,250}\s*.{0,250}\s*<span class="produtoPrecoVendaPor">.{0,500}</span>\s*<span.{0,500}\s*.{0,500}\s*</td>\s*(<td class="btnSKU">\s*<a class="produtoBtnComprar")';
				
				$procuraPreco = $obj->applyRegex($regexProcuraPreco,1);
				$procuraDispo = $obj->applyRegex($regexProcuraDispo,1);
				
				
				$resultado[$key]['NomeSubItem'] = $val;
				
		 		if ($procuraPreco > 0)
		 		{
		 			$resultado[$key]['PrecoSubItem'] = $procuraPreco;

		 		}
		 		else
		 		{
		 			$resultado[$key]['PrecoSubItem'] = 'N/A';
		 		}
		 		
		 		if (strlen($procuraDispo)>5)
				{
					$resultado[$key]['Disponibilidade'] = "EM ESTOQUE";
				}
				else 
				{
					$resultado[$key]['Disponibilidade'] = "SEM ESTOQUE";
				}		 		
		 		
		 	}
		 	
		 	//Constroi uma tabela HTMl para listar os subitens extraidos...
		 	$html = "<table width='400' border='1'><tr style='background-color:#eeeeee'><td>Item</td><td>Preço</td><td>Disponivel</td></tr>";
		 	
		 	foreach($resultado as $key=>$val)
		 	{
		 		$html.= "<tr><td>".utf8_encode($val['NomeSubItem'])."</td><td>R\$".$val['PrecoSubItem']."</td><td>".$val['Disponibilidade']."</td></tr>";
		 	}
		 	
		 	$html .= "</table>";
		 	
			return $html;
		 }
		 else
		 	return 'Nao foi possível detectar os produtos desta página, tente outra';
		 
	}
	
}