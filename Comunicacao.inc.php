<?php
/**
 * @comments Tentei deixar esta classe o mais simples e menos simplista possível.
 * @author Léo Borges <ideiasdaweb.blogspot.com>
 * 
 *  Métodos para operação
 *  -> getHTML ($url) -> retorna o HTML (ou outra resposta) de uma URL
 *  -> applyRegex($regex, $target) -> Aplica uma expressão regular no HTML
 *  -> setProxy -> Pode-se definir um proxy para operação do getHTML (útil caso o IP do teu servidor seja bloqueado pelo alvo)
 */
class Comunicacao
{
	/**
	 * Onde ficará armazenado o HTML ou a resposta, após o método getHTML
	 * @var string
	 */
	public $result;
	
	/**
	 * Onde ficará armazenado o cURL
	 * @var Object 
	 */
	private $_curl;

	/**
	 * Caso deseje utilizar um proxy caso o ecommerce alvo bloqueie o IP do BOT
	 * @var string
	 */
	private $_proxy = false;
	
	/**
	 * Exibir HTML no momento de recuperar uma página (útil para debug)
	 * @var boolean
	 */
	private $_exibirHTML = false;
	
	/**
	 * Caso deseje que o cabeçalho da requisição seja retornado junto com a resposta (html)
	 * @var boolean
	 */
	private $_exibirCabecalho = false;
	
	/**
	 * Caso deseje que o conteúdo seja retornado na resposta
	 * @var boolean
	 */
	private $_exibirConteudo = true;

	/**
	 * Caso deseje que o componente siga para um redirecionamento imposto pelo alvo
	 * @var boolean
	 */
	private $_seguirRedirecionamento = true; 
	
	/**
	 * Onde ficarão armazenados os possíveis cookies oriundos da navegação
	 * @var string
	 */
	private $_arquivoDoCookie; 


	public function __construct()
	{		
		$this->_arquivoDoCookie = tempnam("/tmp", "ck_");
	}

	/**
	 * Retorna o HTML de uma URL qualquer
	 * @param string $url URL completa
	 * @param string $navegador Caso deseje simular um navegador
	 */
    public function getHTML($url,$navegador=false) 
    {
		$url = ereg_replace(" ", "%20",$url);;
    	$this->_init();
		if($this->_curl)
		{
			$this->_carregarConfigsDoCurl();
			$this->_curlSetConfig(CURLOPT_POST, 0);
			$this->_curlSetConfig(CURLOPT_CUSTOMREQUEST,'GET');
			
			if($navegador)
			{
				$this->_curlSetConfig(CURLOPT_USERAGENT, $navegador);
			}
			
			//Caso haja um proxy definido...
			if ($this->_proxy)
			{ 
				$this->_curlSetConfig(CURLOPT_PROXY, $this->_proxy);
			}
			
			$this->_curlSetConfig(CURLOPT_URL, $url);
			
			$result = curl_exec($this->_curl);
			
			if ($this->_exibirHTML)
			{
				print $result;
			}
			
			$this->result = $result;
			
			$this->_destroy();
			
			return ($this->result);
		}
		return 0;
	}

	/**
	 * Aplica uma expressão regular no HTML gerado
	 * @param string $regex Código da Expressão Regular
	 * @param array $target Caso haja mais de um match qual capturar? O valor pode ser integer ou um array com integer
	 * @param integer $ocorrenciaEspecifica (opcional)Dentro de um match, pode se ter um grupo de ocorrencias. 
	 * Por exemplo um cjto de URLS. Que ocorrencia vc quer? Começa a partir de 1 (primeira ocorrencia)
	 */
    public function applyRegex($regex, $target=1, $ocorrenciaEspecifica=false)
	{
		if ($this->result)
		{
			//Prepara a regex para o PHP...
			$regex = "/".ereg_replace("/", "\/", $regex)."/";
			
			//Reseta o vetor que armazenará o resultado
			$resultado = array();
			
			if (preg_match_all($regex, $this->result, $resultado))
			{
				//Verifica se o target é um array
				if (is_array($target))
				{
					$txtResultado = '';					
					//verifica os targets disponiveis, se um deles tiver resultado...salva.
					foreach($target as $tKey => $tVal)
					{
						if (strlen(trim($resultado[$tVal][0]))>0)
						{
							$txtResultado .= implode(' ', $resultado[$tVal]);
						}
					}	
					
					return $txtResultado;
				}
				else
				{
					if (is_array($resultado[$target]))
					{
						if ($ocorrenciaEspecifica>0)
						{
							return (string)$resultado[$target][($ocorrenciaEspecifica-1)];
						}
						else 
						{
							if (count($resultado[$target])>1)
							{
								return $resultado[$target];	
							}
							else 
							{
								return implode('',$resultado[$target]);
							}
								
						}
						
						
					}
					else
					{
						return (string)$resultado[$target];	
					}
				}					
			} 			
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
			
	}
	
	/**
	 * Define um proxy
	 * @param string $strProxy
	 */
	public function setProxy($strProxy)
	{
		$this->_proxy = $strProxy;
	}
	
	/**
	 * Carrega o cURL na classe
	 */
	private function _init()
	{
		return $this->_curl = curl_init();
	}	
	
	/**
	 * Método para inserir ou modificar alguma configuração
	 * @param string $opt Nome da configuração
	 * @param string $value Valor da configuração
	 */
	private function _curlSetConfig($opt, $value)
	{
		return curl_setopt($this->_curl, $opt, $value);
	}
  
	/**
	 * Configura o cURL com definições default do projeto em questão (pode ser modificado a bel prazer)
	 */
	private function _carregarConfigsDoCurl()
	{
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[] = "Accept-Language: pt-br,pt;q=0.5";
		
		$this->_curlSetConfig(CURLOPT_HTTPHEADER, $header); 	  
	    $this->_curlSetConfig(CURLOPT_RETURNTRANSFER, 1);
	    $this->_curlSetConfig(CURLOPT_FOLLOWLOCATION, $this->_seguirRedirecionamento);
	    $this->_curlSetConfig(CURLOPT_VERBOSE, false); 
	    $this->_curlSetConfig(CURLOPT_SSL_VERIFYPEER, false);
	    $this->_curlSetConfig(CURLOPT_SSL_VERIFYHOST, false);
	    $this->_curlSetConfig(CURLOPT_HEADER, $this->_exibirCabecalho);
	    $this->_curlSetConfig(CURLOPT_NOBODY, !$this->_exibirConteudo);
	    $this->_curlSetConfig(CURLOPT_COOKIEJAR, $this->_arquivoDoCookie);
	    $this->_curlSetConfig(CURLOPT_COOKIEFILE, $this->_arquivoDoCookie);
	    
	    //As duas linhas abaixo são para fins educacionais, elas dificultariam a detecção do bot espião perante as
	    //ferramentas analytics dos concorrentes 
	    $this->_curlSetConfig(CURLOPT_REFERER, "http://google.com");
	    $this->_curlSetConfig(CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)");
	    $this->_curlSetConfig(CURLOPT_POST, 1);
	    $this->_curlSetConfig(CURLOPT_CUSTOMREQUEST,'POST');
	 	return true;
	}

	/**
	 * Encerra a sessão do cURL
	 */
	private function _destroy()
	{
		return curl_close($this->_curl);
	}	

}
?>