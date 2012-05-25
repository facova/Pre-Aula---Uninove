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
 * @subpackage exceptions
 * File: ExceptionOutput.php
 **/


/** Saida de dados de Excecoes
 * @author Silas R. N. Junior
 */
class ExceptionOutput {

	/** Gera codigo html contendo a mensagem de erro
	 * @param string $tipo   Tipo da excecao
	 * @param Exception $e   Objeto de excecao
	 * @return string
	 */
	public static function generate(Exception $e,$tipo = null) {
		if (!$tipo) {
			$tipo = get_class($e);
		}
		?><?php
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
		//echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\";
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
	xmlns:e="http://seelaz.com.br/EngineML">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Pagina de erro</title>
<style type="text/css">
body,html {
	height: 100%;
	margin: 0;
	padding: 0;
	font-family: tahoma;
}

#errorBox {
	width: 760px;
	margin: 10px auto 0 auto;
}

#errorName {
	width: 760px;
	background: #E95A5A;
	color: #FFFFFF;
	font-weight: bold;
	padding: 3px;
}

#errorBody {
	width: 758px;
	border: #E95A5A 1px solid;
	padding: 3px;
	font-size: 12px;
}
</style>
</head>
<body>
<div id="errorBox">
<div id="errorName"><?php echo($tipo) ?></div>
<div id="errorBody"><?php echo($e->getMessage()) ?><br />
Call Trace:<br />
		<?php echo($e->getTraceAsString()); ?></div>
</div>
</body>
</html>
		<?php
	}
}

?>