<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chico Hamburguer - S�o Paulo Passa por aqui! - Carrinho</title>

<link href="css/screen.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet" type="text/css"  />

<meta http-equiv="content-type" content="text/html;charset=ISO" />    


</head>

<body>
<?php include('header.php'); ?>

<!-- Aqui vai o conteúdo de cada página -->
<div id='blocoCarrinho'>
<div id='headCarrinho'>
<div id='headCarrinhoFoto'></div>
<div id='headCarrinhoDesc'>Produto</div>
<div id='headCarrinhoQuant'>Quantidade</div>
<div id='headCarrinhoVUnit'>Valor Unit�rio</div>
<div id='headCarrinhoVTotal'>Valor Total</div>
</div>

<?php
require_once ("../controller/Cookie.controller.php");
require_once ("../controller/Produto.controller.php");

echo ($ProdutoControle -> ListaProdutoCarrinho());

?>
<!-- Aqui vai o rodapé página -->
<?php include('footer.php'); ?>
</body>
</html>