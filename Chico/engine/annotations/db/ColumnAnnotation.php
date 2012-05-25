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
 * @package annotations
 * @subpackage db
 * File: ColumnAnnotation.php
 **/


/** Anotacao de Coluna: Armazena o nome da coluna do campo
 * @author Silas R. N. Junior
 */
class ColumnAnnotation extends Annotation {

	/** Nome da coluna
	 * @var string
	 */
	private $name;

	/** Define se os valores dessa coluna sao unicos
	 * @var boolean
	 */
	private $unique = false;

	/** Define se a coluna aceita nulos
	 * @var boolean
	 */
	private $nullable = true;

	/** Define se a coluna participara de operacoes insert
	 * @var boolean
	 */
	private $insertable = true;

	/** Define se a coluna participara de operacoes update
	 * @var boolean
	 */
	private $updatable = true;

	/** Anotacao de Coluna
	 * @param string $name   Nome da Coluna
	 */
	public function ColumnAnnotation($name = null) {
		$this->setName($name);
	}

	/** Retorna o nome da coluna
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/** Define o nome da coluna
	 * @param string $newName   Nome da Coluna
	 * @return void
	 */
	public function setName($newName) {
		$this->name = $newName;
	}

	/** Verifica se a coluna deve possuir valores unicos
	 * @return boolean
	 */
	public function isUnique() {
		return $this->unique;
	}

	/** Define se a coluna deve possuir valores unicos
	 * @param boolean $newUnique   false [Padrao] - Permite valores duplicados
	 *    true - Nao permite valores duplicados
	 * @return void
	 */
	public function setUnique($newUnique) {
		$this->unique = $newUnique;
	}

	/** Verifica se a coluna deve possuir valores nulos
	 * @return boolean
	 */
	public function isNullable() {
		return $this->nullable;
	}

	/** Define se a coluna deve possuir valores nulos
	 * @param boolean $newNullable   false - Nao permite valores nulos
	 *    true [Padrao] - Permite valores nulos
	 * @return void
	 */
	public function setNullable($newNullable) {
		$this->nullable = $newNullable;
	}

	/** Verifica se a coluna deve participar de operacoes Insert
	 * @return boolean
	 */
	public function isInsertable() {
		return $this->insertable;
	}

	/** Define se a coluna deve participar de operacoes Insert
	 * @param boolean $newInsertable   false - Nao permite insercao do campo
	 *    true [Padrao] - Permite insercao do campo
	 * @return void
	 */
	public function setInsertable($newInsertable) {
		$this->insertable = $newInsertable;
	}

	/** Verifica se a coluna deve participar de operacoes Update
	 * @return boolean
	 */
	public function isUpdatable() {
		return $this->updatable;
	}

	/** Define se a coluna deve participar de operacoes Update
	 * @param boolean $newUpdatable   false - Nao permite atualizacao do campo
	 *    true [Padrao] - Permite atualizacao do campo
	 * @return void
	 */
	public function setUpdatable($newUpdatable) {
		$this->updatable = $newUpdatable;
	}
}

?>