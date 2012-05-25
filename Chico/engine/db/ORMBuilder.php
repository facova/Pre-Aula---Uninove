<?php
/**
 * Engine PHP Application Framework
 * http://seelaz.com.br
 *
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
 * File: ORMBuilder.php
 **/


/** Construtor para operacoes ORM
 * @author Silas R. N. Junior
 */
abstract class ORMBuilder {

	/** Contador de aliases
	 * @var int
	 */
	private static $aliasCounter = 0;

	/** Todas as classes enfileiradas
	 * @var array
	 */
	protected $allClasses = array();

	/** Mapeamento da entidade raiz
	 * @var ORM
	 */
	private $root;

	/** Define o mapeamento raiz
	 * @param ORM $newRoot   Mapeamento da entidade raiz
	 * @return void
	 */
	public function setRoot(ORM $newRoot) {
		$this->root = $newRoot;
	}

	/** Retorna o mapeamento raiz
	 * @return ORM
	 */
	public function getRoot() {
		return $this->root;
	}

	/** Constroi o proximo mapeamento
	 * @param string $className   Nome da classe
	 * @param array $path   Caminho desde o objeto raiz
	 * @param string $ownerClassProperty   Propriedade da classe conteiner que referencia esta classe
	 * @param ORM $ownerClassORM   Mapeamento da classe conteiner
	 * @param int $depth   Contador de Profundidade de recursao
	 * @return ORM
	 */
	public abstract function build($className, $path = null, $ownerClassProperty = null, ORM &$ownerClassORM = null, $depth = null);

	protected function isRecursion($path,$thisClass) {
		if ($path) {
			$aPath = array_count_values($path);
			return (isset($aPath[$thisClass]) && $aPath[$thisClass] > 1) ? true : false;
		} else {
			//assume root
			return false;
		}
	}
	
	public static function getNextAlias() {
		return self::$aliasCounter++;
	}
	
	public static function resetCounter() {
		self::$aliasCounter = 0;
	}
}

?>
