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
 * File: OrderByAnnotation.php
 **/


/** Anotacao de Ordenacao: Define a ordem em que a lista deve ser ordenada
 * @author Silas R. N. Junior
 */
class OrderByAnnotation extends Annotation {

	/** Nome da coluna de ordenacao
	 * @var string
	 */
	private $name;

	/** Sentido da ordenacao
	 * @var string
	 */
	private $order = "asc";

	/** Retorna o nome da coluna de ordenacao
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/** Define o nome da coluna de ordenacao
	 * @param string $newName   Nome da coluna
	 * @return void
	 */
	public function setName($newName) {
		$this->name = $newName;
	}

	/** Retorna o sentido da ordenacao
	 * @return string
	 */
	public function getOrder() {
		return $this->order;
	}

	/** Define o sentido da ordenacao
	 * @param string $newOrder   asc [Padrao] - Ordena a lista em ordem ascendente
	 *    desc - Ordena a lista em ordem descendente
	 * @return void
	 */
	public function setOrder($newOrder) {
		$this->order = $newOrder;
	}
}

?>