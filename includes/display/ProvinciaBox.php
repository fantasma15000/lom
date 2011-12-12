<?php

	class ISC_PROVINCIABOX_PANEL extends PRODUCTS_PANEL
{
	public function SetPanelSettings(){
	
		if ($GLOBALS['CatName']){
			ISC_SetCookie("CATNAME", $GLOBALS['CatName'], time() + 3600);
		}else{		   
			$GLOBALS['CatName'] = $_COOKIE['CATNAME'];
		}
		
		if(isset($_GET['showcattop']) && $_GET['showcattop'] != ""){
			$GLOBALS['CatName'] = $_GET['showcattop'];
		}
		$GLOBALS['CatName'] = str_replace("-"," ",$GLOBALS['CatName'] );
	}
	
}