<?php
class ISC_SIDEPRODUCTNEWPRODUCTS_PANEL extends PRODUCTS_PANEL
{
	public function SetPanelSettings()
	{

		$output = "";
		$GLOBALS['ISC_CLASS_CATEGORY'] = GetClass('ISC_CATEGORY');
		$categorySql = $GLOBALS['ISC_CLASS_CATEGORY']->GetCategoryAssociationSQL(false);
		
		$categorySql= "  p.productid in (select productid from isc_categoryassociations where categoryid in ( select categoryid from isc_categories where catname='". $GLOBALS['CatName'] ."'))";

		$publishbydate = " and (publish_start < now() and publish_end > now() )";


		
		$query = $this->getProductQuery($categorySql, 'p.prodsortorder ASC', 30,null , $publishbydate ." and p.productid <> " . $GLOBALS['ProductId']);
		

		
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

		if($GLOBALS['ISC_CLASS_DB']->CountResult($result) > 0) {
			if(!GetConfig('ShowProductRating')) {
				$GLOBALS['HideProductRating'] = "display: none";
			}

			$GLOBALS['AlternateClass'] = '';
			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$this->setProductGlobals($row);
				$output .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SideCategoryNewProducts");
			}

			// Showing the syndication option?
			if(GetConfig('RSSNewProducts') != 0 && GetConfig('RSSCategories') != 0 && GetConfig('RSSSyndicationIcons') != 0) {
				$GLOBALS['ISC_LANG']['CategoryNewProductsFeed'] = sprintf(GetLang('CategoryNewProductsFeed'), $GLOBALS['CatName']);
				$GLOBALS['SNIPPETS']['SideProductNewProductsFeed'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SideProductNewProductsFeed");
			}
		}
		else {
			$GLOBALS['HideSideProductNewProductsPanel'] = "none";
			$this->DontDisplay = true;
		}

		$GLOBALS['SNIPPETS']['SideNewProducts'] = $output;
	}
}