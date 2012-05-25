<?php
/**
 * Engine PHP Application Framework
 * http://seelaz.com.br
 * Copyright (C) 2006-2011 Silas "Seelaz" Junior <seelaz@gmail.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @license http://www.fsf.org/licensing/licenses/gpl.html
 *
 * @filesource
 * @package engine
 * @subpackage mvc
 * File: FrontController.php
 **/


/** Cotrolador de operacoes da aplicacao para interface WEB
 * @author Silas R. N. Junior
 */
class FrontController {

	/** Instancia do Logger
	 * @var Logger
	 */
	private $logger;

	/** Arquivo de configuracao geral
	 * @var SimpleXMLElement
	 */
	private $cfgLocal;

	/** Arquivo de configuracao da aplicacao
	 * @var SimpleXMLElement
	 */
	private $cfgApp;

	/** Security Agent
	 * @var IAuth
	 */
	private $sec;

	/** Sessao
	 * @var Session
	 */
	private $session;

	/** Inicializa ou recria o ambiente da aplicacao e do usuario
	 * @return boolean
	 */
	public function initialize() {
		//Carregar configuracao geral do Framework
		if (file_exists("config/conf.xml"))
		$this->cfgLocal = @simplexml_load_file("config/conf.xml");
		elseif (file_exists("lib/config/conf.xml"))
		$this->cfgLocal = @simplexml_load_file("lib/config/conf.xml");
		else
		throw new Exception("Arquivo de configuração geral do framework não foi encontrado.");
		 
		//Carregar configuracao especifica da aplicacao
		if (Request::hasVar('app')) {
			if (file_exists("config/context-".Request::getVar('app').".xml")) {
				$this->cfgApp = @simplexml_load_file("config/context-".Request::getVar('app').".xml");
			}
			elseif (file_exists("lib/config/context-".Request::getVar('app').".xml")) {
				$this->cfgApp = @simplexml_load_file("lib/config/context-".Request::getVar('app').".xml");
			}
			else {
				throw new Exception("Arquivo de configuração não encontrado para o nome de aplicação ".Request::hasVar('app')." solicitado.");
			}
		} else {
			if (file_exists("config/context-".$this->cfgLocal->application.".xml")) {
				$this->cfgApp = @simplexml_load_file("config/context-".$this->cfgLocal->application.".xml");
			}
			else if (file_exists("lib/config/context-".$this->cfgLocal->application.".xml")) {
				$this->cfgApp = @simplexml_load_file("lib/config/context-".$this->cfgLocal->application.".xml");
			} else {
				throw new Exception("Arquivo de configuração de aplicação default não encontrado.");
			}
		}

		/**
		 * TIMEZONE
		 */
		if (isset($this->cfgApp->timezone))
		date_default_timezone_set($this->cfgApp->timezone);
	  
		/**
		 * APP_NAME & APP_PATH
		 */
		if (isset($this->cfgApp->name)) {
			define('APP_NAME',$this->cfgApp->name);
			define('APP_PATH',$this->cfgApp->path.APP_NAME);
				
			if (!isset($this->cfgApp->path))
			$path = "/";
			else
			$path = $this->cfgApp->path;
				
			set_include_path('.'.$path.APP_NAME
			.PATH_SEPARATOR.get_include_path()
			);
		} else {
			throw new Exception ("Nome da aplicacao nao encontrado no arquivo de configuracao");
		}

		/**
		 * CACHE
		 */
		if (isset($this->cfgApp->nocache)) {
			define('ENGINE_NO_CACHE',true);
		}

		/**
		 * DEBUG
		 */
		if (isset($this->cfgApp->debug->log) && $this->cfgApp->debug->log == "true") {
			define('ENGINE_DEBUG_LOG',true);

			if (isset($this->cfgApp->debug->logpath)) {
				$this->logger = new Logger($this->cfgApp->debug->logpath);
				$logPath = $this->cfgApp->debug->logpath;
			} else {
				$this->logger = new Logger();
				$logPath = ".";
			}
			$this->logger->add("Log Inicializado [path:{$logPath}].");
			$logPath = null;
		}

		if (isset($this->cfgApp->debug->verbosity))
		define('ENGINE_DEBUG_VERBOSE',$this->cfgApp->debug->verbosity);

		/**
		 * DAO
		 */
		if (isset($this->cfgApp->db)) {
			import('engine.db.DAO');
			import('engine.db.DAOFactory');
			import('engine.db.drivers.DbDriverFactory');
			$driver = DbDriverFactory::getDriver($this->cfgApp->db->dbms);
			$driver->configure($this->cfgApp->db->host,$this->cfgApp->db->dbname,$this->cfgApp->db->user,$this->cfgApp->db->pass);
			$factory = new DAOFactory($driver,APP_NAME);
			$driver = null;
		}

	}

	/** Carrega o arquivo de configuracao no disco
	 * @return void
	 */
	private function loadConfiguration() {

	}

	/** Atualiza a view com os dados da Request
	 * @param BaseView $object   View a ser atualizada
	 * @return void
	 */
	private function updateView(ViewWeb $object) {
	}

	/** Valida os campos na view
	 * @param array $array   View da aplicacao
	 * @return void
	 */
	private function validateViewData($array) {
		/** <TODO> validar de fato */
		return true;
	}

	/** Atualiza o Model com base nos dados da view
	 * @param BaseView $view   Controlador contendo Modelo a ser atualizado
	 * @return void
	 */
	private function updateModel(BaseView $view) {
		$viewData = $view->getData();
		if ($this->validateViewData($viewData)) {
			$model = $view->getModel();
			foreach ($viewData as $path => $value) {
				if (!empty($value))
				EntityUtils::setByPath($model,$path,$value);
			}
		}
	}

	/** Cria o Gerenciador de log padrao
	 * @return Logger
	 */
	private function getLogManager() {
		return $this->logger;
	}

	/** Retorna o Gerente de seguranca da aplicacao
	 * @return Auth
	 */
	private function getSecurityAgent() {
		import('engine.mvc.AuthFactory');
		if (!isset($this->sec)) {
			if($this->session->hasVar('sca_'.APP_NAME)) {
				$this->sec = $this->session->getObject('sca_'.APP_NAME);
			} else {
				if(isset($this->cfgApp->security)) {
					$agent = (string) $this->cfgApp->security;
				} else {
					$agent = "dummy";
				}
				$this->sec = AuthFactory::getAuthenticator($agent,(string) APP_NAME);
			}
		}
		return $this->sec;
	}

	/** Processa a requisicao.
	 * Recria os estados dos objetos envolvidos na operacao.
	 * @return boolean
	 */
	public function processRequest() {
		try {

			//Parametros iniciais
			if (Request::hasVar('view')) {
				$view = Request::getVar('view');
			} else {
				$view = 'Default';
			}

			$v = substr($view,0,1);
			$iew = substr($view,1);
			$v = strtoupper($v);
			//$controllerName = $c.$ontroller.'Controller';
			//$modelName = $c.$ontroller.'Model';
			//$viewName = $c.$ontroller.'View';
			$viewName = $v.$iew;
				
			//import(APP_NAME.'.controller.'.$controllerName);
			//import(APP_NAME.'.model.'.$modelName);
				
			if (Request::hasVar('action')) {
				$action = Request::getVar('action');
			} else if (Request::hasVar('actionButton')) {
				$action = Request::getVar('actionButton');
			} else {
				$action = '_default_';
			}
				
			//Recriar Ambiente
			//$m = new $modelName();
			$v = new ViewWeb($viewName);
			//$c = new $controllerName();
				
			$this->session = new Session();

			if (($this->session->hasVar('LAST_REQUEST')) && ($this->session->getVar('LAST_REQUEST') != $view)) {
				$this->session->setVar($this->session->getVar('LAST_REQUEST'),null);
			}
			$this->session->setVar('LAST_REQUEST',$view);
				
			if ($this->session->hasVar($view)) {
				$arrSess = $this->session->getVar($view);
				$v = unserialize($arrSess['data']);
			} //else {
			//	$c->defineModelView($m,$v);
			//}
			$m = $v->getModel();
				
			if($this->session->hasVar('ctxt_'.APP_NAME)) {
				$context = $this->session->getObject('ctxt_'.APP_NAME);
			} else {
				$context = new Context((string)APP_NAME);
			}
			$context->setVar('securityAgent',$this->getSecurityAgent());
			$m->setContext($context);
				
			//Seguranca
			if ($m->authenticate()) {
				if ($this->getSecurityAgent()->isAuthenticated()) {
					$uri = array();
					$uri[] = APP_NAME;
					$uri[] = $view;
					$action == "_default_" ? null : $uri[] = $action;
						
					if ($this->getSecurityAgent()->access("/".implode("/",$uri))) {
						$access = true;
					} else {
						//Pagina de erro
						$response = "/".APP_NAME."/erros";
						$access = false;
					}
				} else {
					//Pagina de login
					$response = "/".APP_NAME."/logon";
					$access = false;
				}
			} else {
				$access = true;
			}
				
			if ($access) {
				//echo "\$access == true<br/>";
				$rc = new ReflectionClass($v);
					
				//Atualizar os valores do modelo com os da requisicao
				$this->updateModel($v);

				//Executar a acao
				if ($v->getController()) {
					if (Request::hasVar('parameter')) {
						$response = $v->getController()->{$action}(Request::getVar('parameter'));
						//echo "parameter: ".Request::getVar('parameter')."<br/>";
					} else {
						$response = $v->getController()->{$action}();
						//echo "no parameters!<br/>";
					}
				}

				if (!isset($response)) {
					$v->render();
					//echo "!isset(\$response)<br/>";
				}
				//} else {
				//throw new Exception("O controlador <b>".$viewName."</b> nao da suporte ao evento <b>".$action."</br>");
				//}
				//echo "<html><body>:P</body></html>";
				//Persiste Ambiente
				//$rm = new ReflectionClass($c->getModel());
				//if (!isset($this->cfgApp->nocache)) {
				//$m->setSecurityAgent(null);
				$m->setContext(null);
				$this->session->setVar($view,array(
						'data' => serialize($v)
				));
				//} else {
				//	$this->session->setVar($view,null);
				//}
				$this->session->setObject('sca_'.APP_NAME,$this->getSecurityAgent());
				$context->setVar('securityAgent',null);
				$this->session->setObject('ctxt_'.APP_NAME,$context);
				$this->session = null;
			}
				
			//CLEANUP
			$this->logger->add(sprintf("Utilizacao de Memoria: %.0f/%.0fK Pico: %.0f/%.0fK Limite: %s",memory_get_usage()/1024, memory_get_usage(true)/1024,memory_get_peak_usage()/1024,memory_get_peak_usage(true)/1024,ini_get('memory_limit')));
			$this->logger->add("Realizando operacoes de limpeza");
			$this->cfgApp = null;
			//$this->logger->add("Fechando Log");
			//$this->logger->close();

			if (isset($response) && !is_bool($response)) {
				header("location:".$response);
			}
			return;
		} catch (DbException $e) {
			$tipo = "Erro de SQL";
			ExceptionOutput::generate($e, $tipo);
			exit();
		} catch (Exception $e) {
			$tipo = "Erro de Execu&ccedil;&atilde;o";
			ExceptionOutput::generate($e, $tipo);
			exit();
		}

	}
}

?>