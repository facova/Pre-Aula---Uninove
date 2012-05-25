<?php
require_once ("../model/database/constantes.php");
class ConexaoBD
{
  	public $con;
  	
  	//** Construtor que abre conexao
  	function __construct()
  	{
	    $this->con = mysql_connect( DB_HOST , DB_USER , DB_PASSWORD );
	    if( !$this->con )
	    {
		  	echo("Erro ao conectar no Bando de Dados.");
		  	exit;
		}
	    mysql_select_db( DB_NAME , $this->con );
	}
	
	//** Fecha conexao
	function fechaBd()
	{
		mysql_close( $this->con );
  	}
}
?>