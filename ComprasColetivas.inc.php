<?php
require_once 'global.php';
/**
* Define uma Interface dinâmica. Compras Coletivas estende AmbienteDeConcorrencia
* No método __construct há o registro de todas funções necessárias para todos os plugins
* deste tipo de negócio eletrônico
* @author Léo Borges <ideiasdaweb.blogspot.com>
*/
class ComprasColetivas extends AmbienteDeConcorrencia
{
	//Load o plugin aqui?!? Não, há de ser no ET
	function __construct($nomeDoPlugin=false)
	{
		if ($nomeDoPlugin)
		{
			//Define ComprasColetivas como Operação
			$this->setTipoEmOperacao((string)get_class($this));
			
			//Adiciona as funcoes comuns a todos os sites de ComprasColetivas
			
			//Atenção: todos os plugins da pasta/tipo 'ComprasColetivas', terão de 
			//ter todas estas funções que vc definir para funcionar
			$this->registryFuncao('ID_da_Oferta_No_Site', 'getIdDoSite');
			$this->registryFuncao('Nome_Do_Estabelecimento', 'getNomeEstabelecimento');
			$this->registryFuncao('Preco_Inflado', 'getInflatedPrice');
			$this->registryFuncao('Preco_Praticado', 'getCurrentPrice');
			$this->registryFuncao('Total_Vendido', 'getTotalVendido');
			$this->registryFuncao('URL_De_Outras_Ofertas_da_Cidade_Atual', 'getUrlMaisOfertas');
			$this->registryFuncao('URL_Da_Oferta_Nacional', 'getUrlOfertaNacional');
			$this->registryFuncao('URL_De_Todas_As_Cidades', 'getUrlDasCidades');			
			
			$this->loadPlugin($nomeDoPlugin);
		}
	}
}