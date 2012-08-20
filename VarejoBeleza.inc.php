<?php
require_once 'global.php';
/**
* Define uma Interface dinâmica. 
* No método __construct há o registro de todas funções necessárias para todos os plugins
* deste tipo de negócio eletrônico
* @author Léo Borges <leonardo@kelda.com.br>
*/
class VarejoBeleza extends AmbienteDeConcorrencia
{
	//Load o plugin aqui?!? Não, há de ser no ET
	function __construct($nomeDoPlugin=false)
	{
		if ($nomeDoPlugin)
		{
			//Define ComprasColetivas como Operação
			$this->setTipoEmOperacao((string)get_class($this));
			
			//Adiciona as funcoes comuns a todos os sites de Varejo do Ramo de Beleza
			
			//Atenção: todos os plugins da pasta/tipo 'VarejoBeleza', terão de 
			//ter todas estas funções que vc definir para funcionar
			$this->registryFuncao('Nome_Do_Produto', 'getNomeDoProduto');
			$this->registryFuncao('SubItens', 'getProdutos');
			
			$this->loadPlugin($nomeDoPlugin);
		}
	}
}