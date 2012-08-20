<?php
require_once 'global.php';
/**
 * Esta classe permite, através de suas extensões, a criação de "Interfaces" dinâmicas para manipulação de plugins
 * Por exemplo: veja o codigo de ComprasColetivas e VarejoBeleza
 */

class AmbienteDeConcorrencia
{
	/**
	 * variavel onde fica armazenado os métodos da "interface"
	 */
	var $metodos;
	
	/**
	 * Pode-se definir qual o tipo de ecommerce em execução
	 * 
	 * Este atributo é útil para organizar os plugins em diretórios. P.ex.: se esta variavel estiver
	 * setada como 'ComprasColetivas', ele procurará os plugins no diretorio ComprasColetivas,
	 * caso contrário procurará no diretorio onde foi chamada.
	 * 
	 * Ele é setado nas classes que extendem esta.
	 * 
	 * @usedby $this->loadPlugin()
	 * @var string  
	 */
	var $tipoEmOperacao=false;
	
	/**
	* Armazena o plugin
	* @var object
	*/
	var $plugin;
	
	public function registryFuncao($nomeDoCampo, $funcaoResponsavel)
	{
		$this->metodos[$nomeDoCampo] = $funcaoResponsavel;
	}
	
	public function setTipoEmOperacao($tipoEmOperacao)
	{
		$this->tipoEmOperacao = $tipoEmOperacao;
	}
	
	public function loadPlugin($nomeDoPlugin)
	{
		//Extrai somente os caracteres alfanuméricos da string
		$nomeDoPlugin = preg_replace("/[^a-zA-Z0-9]+/", "", $nomeDoPlugin);
		
		$base = dirname(__FILE__)."/";
		
		if ($this->tipoEmOperacao)
		{
			$base .= $this->tipoEmOperacao."/";			
		}
		
		$toload = $base.$nomeDoPlugin.".plugin.php";
		
		//Carrega o plugin
		if (file_exists($toload))
		{
			require_once ($toload);	
			$this->plugin = new $nomeDoPlugin;
			$this->_verificarConsistenciaDoPlugin();
			
			return true;
		}
		else
		{
			die("Não foi possível localizar o arquivo do plugin.");
			return false;
		}		
	}
	
	/**
	 * Certifica que todas as funcoes da interface dinâmica foram replicadas no plugin
	 * @return boolean
	 */
	private function _verificarConsistenciaDoPlugin()
	{
		if (($this->plugin)&&count($this->metodos))
		{
			//Metodos da interface dinâmica (no __construct das classes que estendem esta)
			$metodosDaInterface = array_values($this->metodos);
			
			//Métodos encontrados no plugin carregado
			$metodosDoPlugin = get_class_methods($this->plugin);
			
			//Certifica que todas as funcoes da interface foram replicadas no plugin
			$comparacao = array_diff($funcoesDaInterface, $funcoesDoPlugin);
			
			
			if (count($comparacao))
			{
				die("Não foi possível carregar o plugin por inconsistencias. Falta os seguintes métodos: <br>".implode("<br>-".$comparacao));
				return false;
			}
			else 
			{
				return true;
			}
			
		}
		else
		{
			die("Não foi possível carregar o plugin, pois o sistema não foi preparado.");
			return false;
		}
	}
	
	
	
	
}