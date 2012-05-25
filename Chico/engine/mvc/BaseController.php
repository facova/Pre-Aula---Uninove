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
 * File: BaseController.php
 **/

import('engine.mvc.BaseMVC');

/** Fornece servicos basicos para os controladores da aplicacao
 * @author Silas R. N. Junior
 */
abstract class BaseController extends BaseMVC {

	/** Instancia da view
	 * @var BaseView
	 */
	private $view;

	/** Instancia do modelo
	 * @var BaseModel
	 */
	private $model;

	/** Retorna a instancia da View
	 * @return BaseView
	 */
	public function getView() {
		return $this->view;
	}

	/** Define a instancia da View
	 * @param BaseView $newView   Instancia da view
	 * @return void
	 */
	public function setView(BaseView $newView) {
		//$newView->setController($this);
		$this->view = $newView;
	}

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
		$newModel->setController($this);
	}

	/** Operacao Padrao
	 * @return void
	 */
	public function _default_() {
	}
}

?>