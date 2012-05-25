<?php
require_once ("../model/Categoria.DAO.php");

class CategoriaControle{
	
	
	function ListaCategorias(){
		$model = new CategoriaDAO();
	
		$result = $model->ConsultaCategoria();
	
		if( mysql_num_rows( $result ) > 0 ){
			$resposta = "<div id='blocoCateg'>";
				
			while( $r = mysql_fetch_array( $result ) ){
				$resposta = $resposta . 
				"<div id='categ'>
		            <div id='imgCateg'>
		            	<a href='categoria.php?id=".$r[0]."'>
		            		<img href='../view/imagens/img_cat/".$r[1].".jpg'>
		            	</a>
		            </div>
		            <div id='nomeCateg'>
						<a id='linkCateg' href='categoria.php?id=".$r[0]."'>".$r[1]."</a>
					</div>
				</div>";
			}							
			$resposta = $resposta . "</div>";
			
		}else{
			
			$resposta = "Não foi encontrado nenhum dado.";
		}
		return $resposta;
	}
	
	function ListaCategoriaUnica($cat){
		$model = new CategoriaDAO();
		$model->idCategoria = $cat;
		$result = $model->ConsultaCategoriaUnica();
		
		if( mysql_num_rows( $result ) > 0 ){
			while( $r = mysql_fetch_array( $result ) ){
				$resposta = $r[0];
			}
				
		}else{
				
			$resposta = "Não foi encontrado nenhum dado.";
		}
		return $resposta;
	}
	
	
}

$CategoriaControle = new CategoriaControle();