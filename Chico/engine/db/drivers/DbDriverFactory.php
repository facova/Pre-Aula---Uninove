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
 * @package db
 * @subpackage drivers
 * File: DbDriverFactory.php
 **/


/** Factory de drivers para acesso ao DBMS
 * @author Silas R. N. Junior
 */
class DbDriverFactory {

	/** Retorna o driver correspondente ao codigo informado
	 * @param string $driverCode   Codigo do driver
	 * @return DbDriver
	 */
	public static function getDriver($driverCode) {
		switch ($driverCode) {
			case 'pgsql':
				import('engine.db.drivers.PgSQLDriver');
				return new PgSQLDriver();
				break;
			case 'mysql':
				import('engine.db.drivers.MySQLDriver');
				return new MySQLDriver();
				break;
			default:
				throw new DbDriverException('Driver nao encontrado. [suportados: mysql, pgsql]');
		}
	}



	public function sgbd($name) {
		return self::getDriver($name);
	}
}

?>