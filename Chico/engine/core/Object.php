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
 * @subpackage core
 * File: Object.php
 **/


/** Objeto padrao
 * @author Silas R. N. Junior
 */
abstract class Object {

	/** Retorna o objeto de reflexao da Classe
	 * @return ReflectionClass
	 */
	public function getClass() {
		return new ReflectionClass($this);
	}

	/** Retorna o objeto de reflexao da Classe com suporte a Annotations
	 * @return ReflectionClassAnnotated
	 */
	public function getAnnotatedClass() {
		return new ReflectionClassAnnotated($this);
	}

	/** Compara o objeto com o objeto fornecido
	 * @param Object $object   Objeto para comparacao
	 * @return boolean
	 */
	public function equals(Object $object) {
		if (spl_object_hash($this) == spl_object_hash($object)) {
			return true;
		} else {
			return false;
		}
	}
}

?>