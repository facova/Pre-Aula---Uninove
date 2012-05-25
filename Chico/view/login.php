<?php
if (count($_POST) > 0) {
	require_once("../controller/Cliente.controller.php");
	$erro = $ClienteControle->validaLogin();
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chico Hamburguer - São Paulo Passa por aqui! - Carrinho</title>

<link href="css/screen.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet" type="text/css"  />

<meta http-equiv="content-type" content="text/html;charset=ISO" />    


</head>

<body>
<?php include('header.php'); ?>

<!-- Aqui vai o conteúdo de cada página -->

<div id='cadastro'>
	<div id='loginfield'>
		<form action="login.php" method="post" name="login" target="_self">
		<label for="login">login</label>
		<input type="text" name="login" id="login" />
		<label for="senha">senha</label>
		<input type="text" name="senha" id="senha" />
		<input type="submit" value="Entrar" />
		</form>
		<?php 
			echo $erro;
		?>
	</div>
	<div id='cadfield'>
		<form action="cadastro.php" method="post" name="cadastro" target="_self">
		<label for="cep">CEP</label>
		<input type="text" name="cep" id="cep" />
		<input type="hidden" name="login" id="login" value="1" />
		<input type="submit" value="Continuar" />
		</form>
	
	</div>


</div>

<!-- Aqui vai o rodapé da página -->
<?php include('footer.php'); ?>
</body>
</html>