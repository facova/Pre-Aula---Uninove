		<?php
			   if (count($_POST) > 0) {
					require_once ("../controller/Cookie.controller.php");
					$cookieVerificado = $CookieControle->GravaCookieProduto($_POST["prodCod"],$_POST["prodQnt"]);
			   }
        ?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chico Hamburguer - S�o Paulo Passa por aqui! - Card�pio</title>

<link href="css/screen.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet" type="text/css"  />

<meta http-equiv="content-type" content="text/html;charset=ISO" />    


</head>

<body>
<?php include('header.php'); ?>

<!-- Aqui vai o conteúdo de cada página -->


<div id="conteudo" class="containerCentral">
	Confira!!!
    
    

		<?php
			   require_once ("../controller/Categoria.controller.php");
			   require_once ("../controller/Produto.controller.php");
			   if (count($_POST) > 0) {
				   if($cookieVerificado){
						echo "<div id='blocoAviso'> Produto inclu�do com sucesso. <a href='#'>Acesse sue carrinho</a></div>";
					   }
					   else{
						   echo "<div id='blocoAviso'> Hilston, temos um problema! Tente novamente.</div>";
					   }
				   };
				   
        ?>
        
    <div id='blocoProd'>
    <div id='nomeCatProd'>
		<?php
        	echo ($CategoriaControle -> ListaCategoriaUnica($_GET['id']));
		?>
    </div>
 		<?php
        	echo ($ProdutoControle -> ListaProdutoPorCategoria($_GET['id']));
		?>   

    </div>
</div>

<!-- Aqui vai o rodapé página -->
<?php include('footer.php'); ?>
</body>
</html>