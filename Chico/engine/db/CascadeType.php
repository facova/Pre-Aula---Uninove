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
 * File: CascadeType.php
 **/


/** Tipo de estrategia de propagacao de Persistencia
 * @author Silas R. N. Junior
 */
class CascadeType {

	/** CascadeType.SAVE - Propaga quando criando ou atualizando
	 * @var string
	 */
	const SAVE = "SAVE";

	/** CascadeType.CREATE - Propaga quando criando
	 * @var string
	 */
	const CREATE = "CREATE";

	/** CascadeType.UPDATE - Propaga quando atualizando
	 * @var string
	 */
	const UPDATE = "UPDATE";

	/** CascadeType.DELETE - Propaga quando excluindo
	 * @var string
	 */
	const DELETE = "DELETE";

	/** CascadeType.ALL - Propaga em qualquer circunstancia
	 * @var string
	 */
	const ALL = "ALL";

	/** CascadeType.NONE - Nao Propaga
	 * @var string
	 */
	const NONE = "NONE";
}

?>