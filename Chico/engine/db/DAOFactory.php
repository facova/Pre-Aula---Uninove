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
 * @subpackage db
 * File: DAOFactory.php
 **/


/**
 * @author Silas R. N. Junior
 */
class DAOFactory {

	/** Data Sources
	 * @var array
	 */
	private static $dsns = array();

	/** Data Source Padrao
	 * @var array
	 */
	private static $defaultDSN;

	/**
	 * @param DbDriver $driver
	 * @deprecated
	 */
	public function DAOFactory(DbDriver $driver) {
		self::$defaultDSN = 'default';
		self::$dsns[self::$defaultDSN] = new DAO($driver);
		trigger_error("Essa forma de utilizacao esta depreciada. Utilize o metodo addDSN(nome,sgbd)", E_USER_NOTICE);
	}

	/** Retorna o DAO padrao
	 * @return DAO
	 */
	public static function getDAO() {
		if (!isset(self::$defaultDSN)) throw new DbException("Nao existem conexoes configuradas.");
		return self::$dsns[self::$defaultDSN];
	}

	/** Define o DSN padrao [retornado pelo metodo getDAO]
	 * @param string $name Nome do DSN definido
	 */
	public static function setDefaultDSN($name) {
		if (!isset(self::$dsns[$name])) throw new DbException("O DSN {$name} nao esta definido.");
		self::$defaultDSN = $name;
	}

	/** Adiciona um data source ao factory
	 * @param name Nome do DS
	 * @param sgbd codigo do sgbd/driver pre configurado.
	 * @return DbDriver
	 */
	public static function addDSN($name, $sgbd) {

		if (isset(self::$dsns[$name])) throw new DbException("O DSN {$name} somente podera ser definido uma vez.");
		if (!isset($sgbd)) throw new DbException("O SGBD e obrigatorio.");
			
		if ($sgbd instanceof DbDriver) {
			self::$dsns[$name] = new DAO($sgbd);
		} else {
			self::$dsns[$name] = new DAO(DbDriverFactory::getDriver($sgbd));
		}
		if (!isset(self::$defaultDSN)) self::$defaultDSN = $name;
		return self::$dsns[$name]->getDriver();
	}

	/** Retorna um DAO configurado com o data source fornecido
	 * @param name Nome do DS
	 * @return DAO
	 */
	public static function getDAObyDSN($name) {
		if (is_null($name) || strlen(trim($name)) == 0) throw new DbException("Forneca um DSN valido.");

		if (!isset(self::$dsns[$name])) {
			throw new DbException("O DSN {$name} nao esta definido.");
		}
		return self::$dsns[$name];
	}
}

?>