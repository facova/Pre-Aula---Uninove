<?php
// incluindo conexуo com o banco
include_once ("../model/database/conn.php");

// incluindo a classe Status
include_once ("../model/Status.class.php");
 
class StatusDAO extends Status {
    
	// Variavel de conexao com o banco
	var $bd;
	
	function StatusDAO() {
		$bd = new ConectaBD();
	}

                       
    // Metodo de Inclusуo
	public function IncluirStatus() {
	   	$sql = "insert into status (descricao) values ( '".$this->getDescricao()."')";
		$result = mysql_query( $sql );
		
		if( !$result )
		{
		    return false;
		}
		
		return true;
	}
	
	// Metodo de Alteraчуo
	function alterarStatus()
	{
		$sql = "update status set descricao = '".$this->getDescricao()."' where idStatus = ".$this->getIdStatus()."";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}	
	
	// Metodo de Exclusуo
	function deletarStatus()
	{
		$sql = "delete from status where idStatus = ".$this->getIdStatus()."";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}
	
	// Metodo de Consulta
	function consultaStatus()
	{
		$sql = "select idStatus,nome from status where 1=1 ";
		$result = mysql_query( $sql );
	
		return $result;
	}
	
}

$StatusDAO = new StatusDAO();
?>