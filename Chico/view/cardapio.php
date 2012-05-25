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
    
    print_r($CategoriaControle -> ListaCategorias());
    ?>
</div>

<!-- Aqui vai o rodapé página -->
<?php include('footer.php'); ?>
</body>
</html>