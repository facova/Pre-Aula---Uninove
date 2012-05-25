<?php

require_once("../model/Cliente.DAO.php");


class ClienteControle{


	function validacadastro() {

		// Pegando o POST
		$p = $_POST;

		// Guardando dados em um array
		$dados = array($p['login'],$p['senha'],$p['nome'],$p['sobrenome'],$p['cpf'],$p['dt_nasc'],$p['sexo'],$p['email'],$p['cep'],$p['endereco'],$p['numero'],$p['complemento'],$p['bairro'],$p['cidade'],$p['estado'],$p['telefone_1'],$p['telefone_2'],$p['referencia']);

		// Validando Campos
		switch($dados) {
			case (empty($p['nome'])):
				$erro = "Preencha o nome";
				return $erro;
				break;
			case (!filter_var($p['email'], FILTER_VALIDATE_EMAIL)):
				$erro = "Digite um email válido";
				return $erro;
				break;
			case (empty($erro)):
				// Se não houver erro, cria um objeto e passa os valores
				$cliente = new ClienteDAO();

				$cliente->setNome($p['nome']);
				$cliente->setSobrenome($p['sobrenome']);
				$cliente->setCpf($p['cpf']);
				$cliente->setDt_nasc($p['dt_nasc']);
				$cliente->setSexo($p['sexo']);
				$cliente->setEmail($p['email']);
				$cliente->setLogin($p['login']);
				$cliente->setSenha($p['senha']);
				$cliente->setCep($p['cep']);
				$cliente->setEndereco($p['endereco']);
				$cliente->setNumero($p['numero']);
				$cliente->setComplemento($p['complemento']);
				$cliente->setBairro($p['bairro']);
				$cliente->setCidade($p['cidade']);
				$cliente->setEstado($p['estado']);
				$cliente->setTelefone_1($p['telefone_1']);
				$cliente->setTelefone_2($p['telefone_2']);
				$cliente->setReferencia($p['referencia']);

				if($cliente->IncluirCliente($cliente)){
					require_once("../controller/Session.controller.php");
					$SessionControle->GravaSessao($cliente->getNome(),1);

					return TRUE;

				}else {
					return FALSE;
				}
				//Passa o objeto para a função da DAO
					
					
				break;
		}



	}

	function validaLogin(){
		// Pegando o POST
		$p = $_POST;

		// Guardando dados em um array
		$dados = array($p['login'],$p['senha']);

		// Validando Campos
		switch($dados) {
			case (empty($p['login'])):
				$erro = "<p id='erro'>O Login precisa ser informado</div>";
				return $erro;
				break;
			case (empty($p['senha'])):
				$erro = "<p id='erro'>Uma senha precisa ser informada</div>";
				return $erro;
				break;
			case (empty($erro)):
				// Se não houver erro, cria um objeto e passa os valores
				$cliente = new ClienteDAO();

				$cliente->setLogin($p['login']);
				$cliente->setSenha($p['senha']);

				$result = $cliente->ConsultaClienteLoginSenha($cliente);
				echo $result;
				if($result){
					$r = mysql_fetch_array( $result );
					require_once("../controller/Session.controller.php");
					$SessionControle->GravaSessao($r[2],1);
					echo $_SESSION['usuario'];
					echo "verdade";
					return TRUE;

				}else {
					$erro = "<p id='erro'>Usuário ou senha inválido!</div>";
					return $erro;
				}
				//Passa o objeto para a função da DAO


				break;

		}
	}
}
$ClienteControle = new ClienteControle();
?>