<?php
class ISC_SIDERECENTPRODUCTS_PANEL extends PRODUCTS_PANEL
{
	public function SetPanelSettings()
	{
	   
		$output = "";
		$categorySql = $GLOBALS['ISC_CLASS_CATEGORY']->GetCategoryAssociationSQL(false);
		

		$query = $this->getProductQuery($categorySql, 'p.prodsortorder ASC', $GLOBALS['ISC_CLASS_CATEGORY']->GetProductsPerPage() ,$GLOBALS['ISC_CLASS_CATEGORY']->GetStart() ," and publish_end < now()");		
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

		
		
		
		if($GLOBALS['ISC_CLASS_DB']->CountResult($result) > 0) {
			if(!GetConfig('ShowProductRating')) {
				$GLOBALS['HideProductRating'] = "display: none";
			}

			$GLOBALS['AlternateClass'] = '';
			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$this->setProductGlobals($row);
				$output .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("RecentProducts");
			}

			// Showing the syndication option?
			if(GetConfig('RSSNewProducts') != 0 && GetConfig('RSSCategories') != 0 && GetConfig('RSSSyndicationIcons') != 0) {
				$GLOBALS['ISC_LANG']['CategoryNewProductsFeed'] = sprintf(GetLang('CategoryNewProductsFeed'), $GLOBALS['CatName']);
				$GLOBALS['SNIPPETS']['SideCategoryNewProductsFeed'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SideCategoryNewProductsFeed");
			}
		}
		else {
			$GLOBALS['HideSideCategoryNewProductsPanel'] = "none";
			$this->DontDisplay = true;
		}

		$GLOBALS['SNIPPETS']['SideNewProducts'] = $output;
	}
}