<?
require_once 'global.php';
/**
* Classe gestora do nosso bot. O nome é em homenagem a uma pessoa muito querida que está por vir ao mundo... :)
* @since 01/08/2011
*/
class AliceBot
{
	var $componenteDeExtracao;
	var $componenteDeComunicacao;
	var $mapaDePlugins;
	var $debug=0;
	
	
	public function __construct()
	{
		//Aqui se registra todos os plugins habilitados
		//Nome do Plugin , Tipo de "Interface", Parte da URL
		$this->_registryPlugin('Groupon', 'ComprasColetivas', 'groupon.com.br');	
		$this->_registryPlugin('Clickon', 'ComprasColetivas', 'clickon.com.br');
		$this->_registryPlugin('Sacks', 'VarejoBeleza', 'sacks.com.br');
		$this->componenteDeComunicacao = new Comunicacao;
	}

	/**
	*  Controla a lógica de visualização do protótipo
	**/
	public function aliceBotController()
	{
		if (strlen($_POST['url'])>5)
		{
			//retorna os dados necessários para carregar o plugin adequado a URL informada
			$dadosDoPlugin = $this->_searchPluginPorUrl($_POST['url']);
			
			if ($this->debug)
			{
				print "<pre>".print_r($dadosDoPlugin,1)."</pre>";
			}
			
			if (is_object($dadosDoPlugin))
			{
				//Cria o componente com sua Interface				
				$this->componenteDeExtracao = new $dadosDoPlugin->tipo;
				
				//Carrega o plugin necessário para interpretar a url
				$this->componenteDeExtracao->__construct($dadosDoPlugin->nome);
				
				//Download do HTML da URL
				$this->componenteDeComunicacao->getHTML( $_POST['url'] );
				
				//Executa o algoritmo do componente e, com auxílio do plugin, extrai as informações relevantes
				$dadosDoConcorrente = $this->_extrairDadosDoConcorrente();
				
				if (count($dadosDoConcorrente))
				{
					return $dadosDoConcorrente;
				}			
				else
				{
					return "Não foi possível verificar a página.";
				}
			}
			else
			{
				//Se não encontrou plugin registrado para a url...
				return "Não foi possível localizar o plugin adequado para visualizar informações deste site.<br><br>Crie um facilmente, leia o readme...";
			}
		}
		
		return 1;
	}	
	
	/**
	 * Percorre todas as funções da Interface no plugin.
	 * 
	 * Por exemplo: se estivermos usando o plugin do Groupon e a interface ComprasColetivas...
	 * Supondo que ComprasColetivas tenha duas funções, o método abaixo irá procurar se no plugin do Groupon
	 * há essas duas funções, caso positivo irá chamá-las. 
	 */
	private function _extrairDadosDoConcorrente()
	{
		if (count($this->componenteDeExtracao->metodos))
		{
			$resultado = array();
			
			//Chamada dinamicamente todos os métodos da Interface corrente
			foreach($this->componenteDeExtracao->metodos as $key=>$nomeDaFuncao)
			{
				//Verifica se o método da interface é "chamável" no plugin
				if (is_callable(array($this->componenteDeExtracao->plugin, $nomeDaFuncao)))
				{
					//Chama o método no plugin, passa o compon. de comunicacao como parametro
					//e salva o resultado no vetor $resultado
					$resultado[$key] = call_user_func(array($this->componenteDeExtracao->plugin, $nomeDaFuncao), $this->componenteDeComunicacao);
				}
				else 
				{
					$resultado[$key] = 'n/a';	
				}
					
			}
			
			return $resultado;
		}
		else
		{
			return false;	
		}
		
	}
	
	/**
	 * Registra/Adiciona/Suporta um site ao BOT.
	 * É necessário que os arquivos de "Interface" ($tipo) e plugin ($nome) estejam prontos no formato correto
	 */
	
	private function _registryPlugin($nome, $tipo, $parteDaUrl)
	{
		$this->mapaDePlugins[] = (object)array('nome'=>$nome, 'tipo'=>$tipo, 'parteDaUrl'=>$parteDaUrl);
	}
	
	/**
	 * Dada uma URL localiza nos plugins registrados, o plugin e a "interface" adequada
	 */
	
	private function _searchPluginPorUrl($url)
	{
		if (count($this->mapaDePlugins))
		{
			foreach($this->mapaDePlugins as $key=>$val)
			{
				$pos = strpos($url, $val->parteDaUrl);
				
				//Verifica se não encontrou nada...
				if ($pos === false)
				{
					continue;
				}
				else
				{
					return ($this->mapaDePlugins[$key]);
				}
			}
			//Se nao achou nd...
			return false;
		}
		else
		{
			return false;
		}
	}
}

