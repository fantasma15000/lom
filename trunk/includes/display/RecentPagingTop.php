<?php
class ISC_RECENTPAGINGTOP_PANEL extends PANEL
{
	public function SetPanelSettings()
	{
		if (!self::generatePagingPanel()) {
			$this->DontDisplay = true;
		}
	}

	public static function generatePagingPanel()
	{
	
		// Do we need to show paging, etc?
	if($GLOBALS['ISC_CLASS_CATEGORY']->GetNumProducts() <= $GLOBALS['ISC_CLASS_CATEGORY']->GetProductsPerPage() ) {
			return false;
		}

		// Workout the paging data
		$GLOBALS['SNIPPETS']['PagingData'] = "";

		$maxPagingLinks = 5;
		if($GLOBALS['ISC_CLASS_TEMPLATE']->getIsMobileDevice()) {
			$maxPagingLinks = 3;
		}

		$start = max($GLOBALS['ISC_CLASS_CATEGORY']->GetPage()-$maxPagingLinks,1);
		$end = min($GLOBALS['ISC_CLASS_CATEGORY']->GetPage()+$maxPagingLinks, $GLOBALS['ISC_CLASS_CATEGORY']->GetNumPages());



		for ($page = $start; $page <= $end; $page++) {
			if($page == $GLOBALS['ISC_CLASS_CATEGORY']->GetPage()) {
				$snippet = "RecentPagingItemCurrent";
			}
			else {
				$snippet = "RecentPagingItem";
			}

			$pageQueryStringAppend = $queryStringAppend;
			$pageQueryStringAppend['page'] = $page;
			$GLOBALS['PageLink'] = RecentLink($pageQueryStringAppend);			
			$GLOBALS['PageNumber'] = $page;
			$GLOBALS['SNIPPETS']['PagingData'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet($snippet);
		}

		// Parse the paging snippet
		if($GLOBALS['ISC_CLASS_CATEGORY']->GetPage() > 1) {
			// Do we need to output a "Previous" link?
			$pageQueryStringAppend = $queryStringAppend;
			$pageQueryStringAppend['page'] = $GLOBALS['ISC_CLASS_CATEGORY']->getPage() - 1;
			$GLOBALS['PrevLink'] = CatLink($GLOBALS['CatId'], $GLOBALS['ISC_CLASS_CATEGORY']->GetName(), false, $pageQueryStringAppend);
			$GLOBALS['SNIPPETS']['RecentPagingPrevious'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("RecentPagingPrevious");
		}

		if($GLOBALS['ISC_CLASS_CATEGORY']->GetPage() < $GLOBALS['ISC_CLASS_CATEGORY']->GetNumPages()) {
			// Do we need to output a "Next" link?
			$pageQueryStringAppend = $queryStringAppend;
			$pageQueryStringAppend['page'] = $GLOBALS['ISC_CLASS_CATEGORY']->getPage() + 1;
			$GLOBALS['NextLink'] = CatLink($GLOBALS['CatId'], $GLOBALS['ISC_CLASS_CATEGORY']->GetName(), false, $pageQueryStringAppend);
			$GLOBALS['SNIPPETS']['RecentPagingNext'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("RecentPagingNext");
		}

		$output = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("RecentPaging");
		$output = $GLOBALS['ISC_CLASS_TEMPLATE']->ParseSnippets($output, $GLOBALS['SNIPPETS']);
		$GLOBALS['SNIPPETS']['RecentPaging'] = $output;
		return true;
	}
}