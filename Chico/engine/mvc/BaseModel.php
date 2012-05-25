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
 * File: BaseModel.php
 **/


/** Fornece servicos basicos para classes de modelo.
 * @author Silas R. N. Junior
 */
abstract class BaseModel extends BaseMVC {

	/** Instancia do controlador
	 * @var BaseController
	 */
	private $controller;

	/** Instancia da view
	 * @var BaseView
	 */
	private $view;

	/** Gerente de seguranca da aplicacao
	 * @var Auth
	 */
	private $securityAgent;

	/** Contexto da Aplicacao
	 * @var Context
	 */
	private $context;

	/** Mensagens para a view
	 * @var array
	 */
	private $messages = array();

	/** Retorna true se as credenciais do usuario permitem o acesso ao recurso
	 * @return boolean
	 */
	public abstract function authenticate();

	/** Retorna o DAO da aplicacao
	 * @return DAO
	 */
	public function getDAO() {
		return DAOFactory::getDAO();
	}

	/** Retorna a instancia do Controlador
	 * @return BaseController
	 */
	public function getController() {
		return $this->controller;
	}

	/** Define a instancia do Controlador
	 * @param BaseController $newController   Instancia do Controlador
	 * @return void
	 */
	public function setController(BaseController $newController) {
		$this->controller = $newController;
		//$this->controller->setModel($this);
		//$this->setView($this->controller->getView());
		//$this->getView()->setModel($this);
	}

	/** Retorna a instancia da View
	 * @return BaseView
	 */
	public function getView() {
		return $this->view;
	}

	/** Define a instancia da View
	 * @param BaseView $newView   Instancia da View
	 * @return void
	 */
	public function setView(BaseView $newView) {
		$this->view = $newView;
		//$this->view->setModel($this);
	}

	/** Seta o objeto de contexto na sessao
	 * @param Context $object
	 * @return void
	 */
	public function setContext(Context $object = null) {
		$this->context = $object;
	}

	/** Retorna o objeto de contexto
	 * @return Context
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @return IAuth
	 */
	public function getSecurityAgent() {
		return $this->securityAgent;
	}

	/** Define o Gerente de seguranca
	 * @param Auth $newSecurityAgent   Gerente de Seguran�a
	 * @return void
	 */
	public function setSecurityAgent(IAuth $newSecurityAgent = null) {
		$this->securityAgent = $newSecurityAgent;
	}

	/**
	 * @return array
	 */
	public function getMessages() {
		$messages = $this->messages;//implode("<br />",$this->messages);
		$this->messages = array();
		return $messages;
	}

	/** Define o Gerente de seguranca
	 * @param string  $newMessage   Mensagem
	 * @return void
	 */
	public function addMessage($newMessage) {
		$this->messages[] = $newMessage;
	}
}

?>