<?php


	
	
	function GravaCookieProduto($prod, $quant){
		
			if (!isset($_COOKIE['chicocookie'])){
				setcookie("chicocookie", time()+3600);
			}
			
			if (!array_key_exists("$prod",$_COOKIE['chicocookie'])){
				setcookie("chicocookie[$prod]", "$quant");
			}else{
				$arr1 = $_COOKIE['chicocookie'];
				$qnt = $arr1[$prod] + $quant;
				setcookie("chicocookie[$prod]", "$qnt");
				return TRUE;
			}
			
	}

// Depois que a página recarregar, mostra eles
if (isset($_COOKIE['chicocookie'])) {
    foreach ($_COOKIE['chicocookie'] as $nome => $valor) {
        echo "$nome : $valor <br />\n";
	}}