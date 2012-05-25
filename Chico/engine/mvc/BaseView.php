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
 * File: BaseView.php
 **/


/** Fornece servicos basicos para os objetos view da aplicacao
 * @author Silas R. N. Junior
 */
abstract class BaseView extends BaseMVC {

	/** Instancia do modelo
	 * @var BaseModel
	 */
	private $model;

	/** Instancia do controlador
	 * @var BaseController
	 */
	private $controller;

	/** Renderiza a saida de dados
	 * @return string
	 */
	public abstract function render();

	/** Retorna a instancia do Modelo
	 * @return BaseModel
	 */
	public function getModel() {
		return $this->model;
	}

	/** Define a instancia do Modelo
	 * @param BaseModel $newModel   Instancia do Modelo
	 * @return void
	 */
	public function setModel(BaseModel $newModel) {
		$this->model = $newModel;
		$newModel->setView($this);
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
		$newController->setView($this);
	}
}

?>