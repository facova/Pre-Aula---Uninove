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
 * File: Logger.php
 **/


/** Gerenciador de logs
 * @author Silas R. N. Junior
 */
class Logger {

	/** Caminho para o arquivo de log
	 * @var string
	 */
	private $logHandle;

	/**
	 * @var Logger
	 */
	private static $instance;

	/**
	 */
	public function Logger($path = null) {
		if (isset($path)) {
			$this->logHandle = fopen($path, "a+", true);
		} else {
			if (file_exists("engine/logs/app.log")) {
				$this->logHandle = fopen("engine/logs/app.log", "a+", true);
			}
			elseif (file_exists("app.log")) {
				$this->logHandle = fopen("app.log", "a+", true);
			}
			else {
				$this->logHandle = fopen("app.log", "a+", true);
				//throw new Exception("O arquivo de log (app.log) não foi encontrado.");
			}
		}
		self::$instance = $this;
	}

	/** Retorna uma instancia do objeto de log
	 * @return Logger
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new Logger();
		}
		return self::$instance;
	}

	/** Adiciona uma entrada ao log
	 * @param string $message
	 * @return void
	 */
	public function add($message) {
		fwrite($this->logHandle,"[".date("Ymd H:i:s")."] ".$message."\r\n");
	}

	/** Define o caminho para o arquivo de log
	 * @param string $newLogHandle   Caminho para o arquivo de log [ex: /app/log]
	 * @return void
	 */
	public function setLogHandle($newLogHandle) {
		$this->logHandle = $newLogHandle;
	}

	/** Fecha o log
	 */
	public function close() {
		fclose($this->logHandle);
	}
}

?>