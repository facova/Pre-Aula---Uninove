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
<?php
if (count($_POST) > 0) {
	require_once("../controller/Cliente.controller.php");
	$ClienteControle->validacadastro();
}
?>
<!--
<div id='conteudo' class='containerCentral'>
<form action="cadastro.php" method="post" name="cadastro" target="_self">
    <div id='cad_esquerdo'>
    <label for="login">login</label>
    <label for="senha">senha</label>
    <label for="nome">nome</label>
    <label for="sobrenome">sobrenome</label>
    <label for="cpf">cpf</label>
    <label for="dt_nasc">dt_nasc</label>
    <label for="sexo">sexo</label>
    <label for="email">email</label>
    <label for="cep">cep</label>
    <label for="endereco">endereco</label>
    <label for="numero">numero</label>
    <label for="complemento">complemento</label>
    <label for="bairro">bairro</label>
    <label for="cidade">cidade</label>
    <label for="estado">estado</label>
    <label for="telefone_1">telefone_1</label>
    <label for="telefone_2">telefone_2</label>
    <label for="referencia">referencia</label>
    </div>

    <div id='cad_direito'>
    <input type="text" name="login" id="login" />
    <input type="text" name="senha" id="senha" />
    <input type="text" name="nome" id="nome" />
    <input type="text" name="sobrenome" id="sobrenome" />
    <input type="text" name="cpf" id="cpf" />
    <input type="text" name="dt_nasc" id="dt_nasc" />
    <input type="text" name="sexo" id="sexo" />
    <input type="text" name="email" id="email" />
    <input type="text" name="cep" id="cep" />
    <input type="text" name="endereco" id="endereco" />
    <input type="text" name="numero" id="numero" />
    <input type="text" name="complemento" id="complemento" />
    <input type="text" name="bairro" id="bairro" />
    <input type="text" name="cidade" id="cidade" />
    <input type="text" name="estado" id="estado" />
    <input type="text" name="telefone_1" id="telefone_1" />
    <input type="text" name="telefone_2" id="telefone_2" />
    <input type="text" name="referencia" id="referencia" />
    <input type="hidden" name="cadastro" value="1" />
    </div>

    <div id='cad_botao'>
    <input type="submit" value="Submit" />
    </div>
</form>
</div>
-->
<div id="conteudo" class="containerCentral">
<div id='produto'>
    <div id='imgProd'><img href='../view/imagens/img_produtos/".$r[0].".jpg'></div>
    <div id='dadosProduto'>
    <div id='tituloProduto'>".$r[1]."</div>
    <div id='descProduto'>".$r[2]."</div>
    <div id='valorProduto'>R$ ".$r[3]."</div>
    <div id='addBandeja'>
    <form target='_self' action='categoria.php?id=".$cat."&act=add' method='post'>
    Quantidade: <input type='text' name='prodQnt' value='1' maxlength='2' size='2'/>
    <input type='hidden' name='prodCod' value='".$r[0]."' />
    <input type='image' src='../view/imagens/adicionarbandeja.gif' name='submit' alt='Adicione � sua bandeja!' />
    </form>
    </div>
    </div>
</div>
</div>
<!-- Aqui vai o rodapé página -->
<?php include('footer.php'); ?>
</body>
</html>