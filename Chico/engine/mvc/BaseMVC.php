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
 * File: BaseMVC.php
 **/

import('engine.mvc.BaseModel');
import('engine.mvc.BaseController');
import('engine.mvc.BaseView');

/** Classe de servicos basicos dos componentes do mvc
 * @author Silas R. N. Junior
 */
abstract class BaseMVC {

	/** Gerenciador de logs
	 * @var Logger
	 */
	public $logger;

	/** Retorna o contexto atual
	 * @return Context
	 */
	public function getContext() {
		/** <TODO> Implement. */
	}

	/** Adiciona uma entrada ao log
	 * @param string $message
	 * @param int $severity
	 * @return void
	 */
	public function log($message, $severity) {
		Logger::getInstance()->add($message);
	}
}

?>