<?php
// incluindo conexуo com o banco
include_once ("../model/database/conn.php");

// incluindo a classe Cliente
include_once ("../model/Cliente.class.php");
 
class ClienteDAO extends Cliente {
    
	// Variavel de conexao com o banco
	var $bd;
	
	function ClienteDAO()	{
		$bd = new ConexaoBD();
	}

                       
    // Metodo de Inclusуo
	public function IncluirCliente($cliente) {
		$sql = "insert into cliente (nome, sobrenome, cpf, dt_nasc, sexo, email, login, senha, cep, endereco, numero, complemento, bairro, cidade, estado, telefone_1, telefone_2, referencia) values
	   	 ('".$this->getNome()."' , '".$this->getSobrenome()."' , ".$this->getCpf()." ,
	   	 ".$this->getDt_nasc()." , '".$this->getSexo()."' , '".$this->getEmail()."' , '".$this->getLogin()."' , ".$this->getSenha()." , ".$this->getCep()." , '".$this->getEndereco()."' , '".$this->getNumero()."' , '".$this->getComplemento()."' , '".$this->getBairro()."' , '".$this->getCidade()."' , '".$this->getEstado()."' , ".$this->getTelefone_1()." , ".$this->getTelefone_2()." , '".$this->getReferencia()."')";
		$result = mysql_query( $sql );
		if( !$result )
		{
		    return false;
		}
		
		return true;
	}
	
	// Metodo de Alteraчуo
	function AlterarCliente()
	{
		$sql = "update cliente set nome = '".$this->getNome()."', sobrenome = '".$this->getSobrenome()."', cep = '".$this->getCep()."', dt_nasc = '".$this->getDt_nasc()."', sexo = '".$this->getSexo()."', email = '".$this->getEmail()."', login = '".$this->getLogin()."', senha = '".$this->getSenha()."', cep = '".$this->getCep()."', endereco = '".$this->getEndereco()."', numero = '".$this->getNumero()."', complemento = '".$this->getComplemento()."', bairro = '".$this->getBairro()."', cidade = '".$this->getCidade()."', estado = '".$this->getEstado()."', telefone_1 = '".$this->getTelefone_1()."', telefone_2 = '".$this->getTelefone_2()."', referencia = '".$this->getReferencia()."' where idCliente = ".$this->getIdCliente()."";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}	
	
	// Metodo de Exclusуo
	function DeletarCliente()
	{
		$sql = "delete from cliente where idCliente = ".$this->getIdCliente()."";
		$result = mysql_query( $sql );
	
		if( !$result )
		{
			return false;
		}
	
		return true;
	}
	
	// Metodo de Consulta
	function ConsultaCliente()
	{
		$sql = "select idCliente, nome, sobrenome, cpf, dt_nasc, sexo, email, login, senha, cep, endereco, numero, complemento, bairro, cidade, estado, telefone_1, telefone_2, referencia from cliente where 1=1 ";
	
		$result = mysql_query( $sql );
	
		return $result;
	}
	
	// Metodo de Consulta de Login & Senha pelo Login
	function ConsultaClienteLoginSenha($cliente){

		$sql = "select login, senha, nome from cliente where login = '".$this->getLogin()."' and senha = '" . $this->getSenha() ."' ";
	
		$result = mysql_query( $sql );
		
		if( !$result ){
			return FALSE;
		}else{		
			return $result;
		}
	}
	
	// Metodo de Consulta de Email
	function ConsultaClienteEmail()
	{
		$sql = "select email from cliente where login = ".$this->getEmail()." ";
	
		$result = mysql_query( $sql );
	
		return $result;
	}
}

$ClienteDAO = new ClienteDAO();
?>