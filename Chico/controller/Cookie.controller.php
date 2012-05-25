<?php
require_once ("../controller/Produto.Controller.php");
class CookieControle{
	
	
	function GravaCookieProduto($prod, $quant){
		
		$model = new ProdutoControle();
		if ($model->isProduto($prod)){
			
			if (!isset($_COOKIE['chicocookie'])){
				setcookie("chicocookie", time()+3600);
			}
			
			if (!array_key_exists("$prod",$_COOKIE['chicocookie'])){
				setcookie("chicocookie[$prod]", "$quant");
				return TRUE;
			}else{
				$arr1 = $_COOKIE['chicocookie'];
				$qnt = $arr1[$prod] + $quant;
				setcookie("chicocookie[$prod]", "$qnt");
				return TRUE;
			}
			
		}else{
			
			return FALSE;
		}
		
	}
	
	
}

$CookieControle = new CookieControle();