<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chico Hamburguer - S�o Paulo Passa por aqui! - Home</title>

<link href="css/screen.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet" type="text/css"  />

	<meta http-equiv="content-type" content="text/html;charset=ISO" />    

<!-- Carregando as bibliotecas Java -->

	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/easySlider1.7.js"></script>
	<script type="text/javascript">
    $(document).ready(function(){	
        $("#slider").easySlider({
            auto: true, 
            continuous: true,
            numeric: true
        });
    });	
	</script>
    

</head>

<body>
<?php include('header.php'); ?>

<!-- Aqui vai o conteúdo de cada página -->

<div id="conteudo" class="containerCentral">
		<div id="slider">
			<ul>				
				<li><a href="#"><img src="banners/01.jpg" alt="Css Template Preview" /></a></li>
				<li><a href="#"><img src="banners/02.jpg" alt="Css Template Preview" /></a></li>
				<li><a href="#"><img src="banners/03.jpg" alt="Css Template Preview" /></a></li>
				<li><a href="#"><img src="banners/04.jpg" alt="Css Template Preview" /></a></li>
				<li><a href="#"><img src="banners/05.jpg" alt="Css Template Preview" /></a></li>			
			</ul>
		</div>

</div>

<!-- Aqui vai o rodapé página -->
<?php include('footer.php'); ?>
</body>
</html>