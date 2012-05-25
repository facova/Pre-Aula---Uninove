<?php
class SessionControle{
	
	
	function GravaSessao($usuario, $validado){
		session_start();
		$_SESSION['usuario'] = $usuario;
		$_SESSION['validado'] = $validado;
	}

	function FinalizaSessao(){
		session_destroy();
	}
	
	
}
	

	$SessionControle = new SessionControle();