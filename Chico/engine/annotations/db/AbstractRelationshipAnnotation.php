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
 * File: AbstractRelationshipAnnotation.php
 **/

import('engine.annotations.Annotation');

/** Define atributos e metodos comuns as annotations de relacionamentos
 * @author Silas R. N. Junior
 */
abstract class AbstractRelationshipAnnotation extends Annotation {

	/** Como o sistema propagara as operacoes de gravacao, alteracao e exclusao
	 * @var mixed
	 */
	private $cascade;

	/** Como o sistema propagara as operacoes de recuperacao de registros referentes a objetos compondo a entidade
	 * @var mixed
	 */
	private $fetch;

	/** Campo na entidade mapeada que mapeia a entidade referenciadora
	 * @var string
	 */
	private $mappedBy;

	/** Classe da entidade referenciada
	 * @var string
	 */
	private $targetEntity;

	/** Profundidade do relacionamento a inicializar
	 * @var int
	 */
	private $depth;

	/**
	 */
	public function AbstractRelationshipAnnotation() {
		$this->cascade = CascadeType::NONE;
		$this->fetch = FetchType::FETCH;
	}

	/** Retorna a estrategia de propagacao definida para as entidades
	 * @return mixed
	 */
	public function getCascade() {
		return $this->cascade;
	}

	/** Define a estrategia de propagacao definida para as entidades
	 * @param mixed $newCascade
	 * 	  CascadeType.SAVE - Propaga quando criando ou atualizando
	 *    CascadeType.CREATE - Propaga quando criando
	 *    CascadeType.UPDATE - Propaga quando atualizando
	 *    CascadeType.DELETE - Propaga quando excluindo
	 *    CascadeType.ALL - Propaga em qualquer circunstancia
	 *    CascadeType.NONE - Nao propaga
	 * @return void
	 */
	public function setCascade($newCascade) {
		$this->cascade = $newCascade;
	}

	/** Retorna a estrategia de recuperacao definida para as entidades
	 * @return mixed
	 */
	public function getFetch() {
		return $this->fetch;
	}

	/** Define a estrategia de recuperacao definida para as entidades
	 * @param mixed $newFetch   FetchType.LAZY - Nao recupera a entidade
	 *    FetchType.FETCH [Padrao] - Recupera a entidade
	 * @return void
	 */
	public function setFetch($newFetch) {
		$this->fetch = $newFetch;
	}

	/** Retorna o nome do campo na entidade mapeada que mapeia a entidade referenciadora
	 * @return string
	 */
	public function getMappedBy() {
		return $this->mappedBy;
	}

	/** Define o nome do campo na entidade mapeada que mapeia a entidade referenciadora
	 * @param string $newMappedBy   Nome da coluna
	 * @return void
	 */
	public function setMappedBy($newMappedBy) {
		$this->mappedBy = $newMappedBy;
	}

	/** Define a classe da entidade referenciada
	 * @param string $newTargetEntity   Nome da classe
	 * @return void
	 */
	public function setTargetEntity($newTargetEntity) {
		$this->targetEntity = $newTargetEntity;
	}

	/** Retorna a classe da entidade referenciada
	 * @return string
	 */
	public function getTargetEntity() {
		return $this->targetEntity;
	}

	/** Retorna a profundidade do relacionamento a inicializar
	 * @return int
	 */
	public function getDepth() {
		return $this->depth;
	}

	/** Define a profundidade do relacionamento a inicializar
	 * @param int $newDepth
	 * @return void
	 */
	public function setDepth($newDepth) {
		$this->depth = $newDepth;
	}

	/** Tipo da Associacao
	 * @return string
	 */
	public abstract function getType();
}

?>