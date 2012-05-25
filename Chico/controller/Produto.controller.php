<?php
require_once ("../model/Produto.DAO.php");
class ProdutoControle{


	function ListaProdutoPorCategoria($cat){

		$model = new ProdutoDAO();
		$model->setCategoria_idCategoria($cat);
		$result = $model->ConsultaProdutoPorCategoria();

		if( mysql_num_rows( $result ) > 0 ){
			$resposta = "<div>";

			while( $r = mysql_fetch_array( $result ) ){
				$resposta = $resposta .
				"    <div id='produto'>
				<div id='imgProd'><img href='../view/imagens/img_produtos/".$r[0].".jpg'>
				</div>
				<div id='dadosProduto'>
				<div id='tituloProduto'>".$r[1]."</div>
				<div id='descProduto'>".$r[2]."</div>
				<div id='valorProduto'>R$ ".$r[3]."</div>
				<div id='addBandeja'>
				<form target='_self' action='categoria.php?id=".$cat."&act=add' method='post'>
				Quantidade: <input type='text' name='prodQnt' value='1' maxlength='2' size='2'/>
				<input type='hidden' name='prodCod' value='".$r[0]."' />
				<input type='image' src='../view/imagens/adicionarbandeja.gif' name='submit' alt='Adicione à sua bandeja!' />
				</form>
				</div>
				</div>
				</div>";
			}
			$resposta = $resposta . "</div>";

		}else{

			$resposta = "Não foi encontrado nenhum dado.";
		}
		return $resposta;
	}

	function ProdutoTotal($quant, $valor){
		$result = $valor * $quant;
		return $result;
	}

	function isProduto($prod){

		$model = new ProdutoDAO();
		$model->setIdProduto($prod);
		$result = $model->ConsultaProdutoPorProduto($prod);

		if( mysql_num_rows( $result ) > 0 ){
			$resposta = TRUE;
		}else{

			$resposta = FALSE;
		}
		return $resposta;
	}


	function ListaProdutoCarrinho(){

		$model = new ProdutoDAO();
		$valorTotal = 0;
		if (isset($_COOKIE['chicocookie'])) {
			foreach ($_COOKIE['chicocookie'] as $nome => $valor) {
				$model->setIdProduto($nome);
				$result = $model->ConsultaProdutoPorProduto($model->getIdProduto());

				$r = mysql_fetch_array( $result );
				$somavalor = ($r[3] * $valor);
				$resposta =
				"<div id='itemCarrinho'>
				<div id='itemCarrinhoFoto'>".$r[0]."</div>
				<div id='itemCarrinhoDesc'>".$r[1]."</div>
				<div id='itemCarrinhoQuant'><input size='3' name='itemCarrinhoQuant' type='text' value='".$valor."'></div>
				<div id='itemCarrinhoVUnit'>R$ ".$r[3]."</div>
				<div id='itemCarrinhoVTotal'>R$ ". $somavalor ."</div>
				</div>";			
				echo $resposta;
				$valorTotal = $valorTotal + $somavalor;
				}

		}
		echo "</div>
		<div id='carrinhoTotal'>Total: R$ " . $valorTotal . "</div>
		<div id='atualizaCarrinho'>Atualizar Carrinho</div>
		<div id='continuarComprando'>Continuar comprando</div>
		<div id='finalizaPedido'>Fechar Pedido</div>
		</div>";
	}

}

$ProdutoControle = new ProdutoControle();