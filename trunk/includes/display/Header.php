<?php

	CLASS ISC_HEADER_PANEL extends PANEL
	{
		public function SetPanelSettings()
		{
		error_reporting (0);

			// Are we using a text or image-based logo?
			$loadLogo = true;
			if($GLOBALS['ISC_CLASS_TEMPLATE']->getIsMobileDevice()) {
				if(getConfig('mobileTemplateLogo')) {
					$GLOBALS['ISC_CLASS_TEMPLATE']->assign('StoreLogo', getConfig('mobileTemplateLogo'));
					$GLOBALS['ISC_CLASS_TEMPLATE']->assign('HeaderLogo', $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('LogoImage'));
					$loadLogo = false;
				}
			}

			if($loadLogo) {
				//$GLOBALS['HeaderLogo'] = FetchHeaderLogo();
				$homecatname = "Buenos-Aires";
				if(isset($GLOBALS['CatName'])){$homecatname = $GLOBALS['CatName'];}
				if(isset($_GET['showcattop'])){$homecatname = $_GET['showcattop'];}
				if(isset($_GET['category'])){$homecatname = $_GET['category'];}
				
					if(isset($_COOKIE['CATNAME']) && (!isset($GLOBALS['CatName']) && !isset($_GET['showcattop']) && !isset($_GET['category'])) ){
						$homecatname = $_COOKIE['CATNAME'];
					}
				
				if($homecatname == ""){$homecatname = $_COOKIE['CATNAME'];}
				$homecatname = str_replace(" ", "-", $homecatname);
				$GLOBALS['catforsales'] = $homecatname;
				$GLOBALS['HeaderLogo'] = "<a href='%%GLOBAL_ShopPathNormal%%/categories/".$homecatname."'><img border='0' alt='Vivo en Sale' id='LogoImage' src='%%GLOBAL_ShopPathNormal%%/product_images/logo.png'></a>"; 
			}
		}
	}