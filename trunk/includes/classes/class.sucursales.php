<?php

	class ISC_SUCURSALES
	{
		private $_sucursal = "";
		private $_sucursalesort = "";
		private $_sucursalesortfield = "";
		private $_sucursalname = '';

		private $_sucursalid = 0;
		private $_sucursalnumproducts = 0;
		private $_sucursalpage = 0;
		private $_sucursalestart = 0;
		private $_sucursalnumpages = 0;

		private $_sucursalpagetitle = '';
		private $_sucursalmetakeywords = '';
		private $_sucursalmetadesc = '';
		private $_sucursalesearchkeywords = '';
		private $_sucursalcanonicallink = '';

		private $_allsucursales = false;

		private $_sucursalproducts = array();

		public function __construct()
		{
			$this->_SetSucursalData();
		}

		public function SetSortField($Field)
		{
			// Set the field that the results will be sorted by in the query
			$this->_sucursalesortfield = $Field;
		}

		public function GetSortField()
		{
			return $this->_sucursalesortfield;
		}

		public function SetSort()
		{
			// Pre-select the current sort order (if any)
			if (isset($_GET['sort'])) {
				$sort = $_GET['sort'];
			} else {
				$sort = "featured";
			}
			$this->_sucursalesort = $sort;

			$priceColumn = 'p.prodcalculatedprice';
			// If we need to join the tax pricing table then the sort price column for
			// products changes.
			if($this->getTaxPricingJoin()) {
				$priceColumn = 'tp.calculated_price';
			}

			switch ($sort) {

				case "newest": {
					$GLOBALS['SortNewestSelected'] = 'selected="selected"';
					$this->SetSortField("p.productid desc");
					break;
				}
				case "bestselling": {
					$GLOBALS['SortBestSellingSelected'] = 'selected="selected"';
					$this->SetSortField("p.prodnumsold desc");
					break;
				}
				case "alphaasc": {
					$GLOBALS['SortAlphaAsc'] = 'selected="selected"';
					$this->SetSortField("p.prodname asc");
					break;
				}
				case "alphadesc": {
					$GLOBALS['SortAlphaDesc'] = 'selected="selected"';
					$this->SetSortField("p.prodname desc");
					break;
				}
				case "avgcustomerreview": {
					$GLOBALS['SortAvgReview'] = 'selected="selected"';
					$this->SetSortField("prodavgrating desc");
					break;
				}
				case "priceasc": {
					$GLOBALS['SortPriceAsc'] = 'selected="selected"';
					$this->SetSortField($priceColumn.' ASC');
					break;
				}
				case "pricedesc": {
					$GLOBALS['SortPriceDesc'] = 'selected="selected"';
					$this->SetSortField($priceColumn.' DESC');
					break;
				}
				case "featured":
				default:
				{
					$GLOBALS['SortFeaturedSelected'] = 'selected="selected"';
					$this->SetSortField("p.prodsortorder asc");
					break;
				}

			}
		}

		public function GetSort()
		{
			return $this->_sucursalesort;
		}

		public function _SetSucursalData()
		{

			// Retrieve the query string variables. Can't use the $_GET array
			// because of SEO friendly links in the URL
			SetPGQVariablesManually();

			// Grab the page sort details
			$GLOBALS['URL'] = implode("/", $GLOBALS['PathInfo']);
			$this->SetSort();

			if (isset($_REQUEST['sucursal'])) {
				$sucursal = $_REQUEST['sucursal'];
			}
			else {
				if (isset($GLOBALS['PathInfo'][1])) {
					$sucursal = preg_replace('#\.html\??.*$#i', "", $GLOBALS['PathInfo'][1]);
				} else {
					$sucursal = '';
				}
			}

			$sucursal = MakeURLNormal($sucursal);

			// Get the link to the "all sucursales" page
			$GLOBALS['AllSucursalesLink'] = SucursalLink();

			// Viewing a particular sucursal
			if($sucursal) {
				// Get the Id of the sucursal
				$query = sprintf("select * from [|PREFIX|]sucursales where sucursalname='%s'", $GLOBALS['ISC_CLASS_DB']->Quote($sucursal));
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

				// Invalid sucursal
				if(!$row) {
					$GLOBALS['ISC_CLASS_404'] = GetClass('ISC_404');
					$GLOBALS['ISC_CLASS_404']->HandlePage();
					exit;
				}

				// Store the sucursal name
				$this->SetSucursal($sucursal);

				$this->SetSucursalName($row['sucursalname']);

				// Store the sucursal Id
				$this->SetId($row['sucursalid']);

				$this->SetSucursalPageTitle($row['sucursalpagetitle']);
				// Store sucursal meta details
				$this->SetMetaKeywords($row['sucursalmetakeywords']);
				$this->SetMetaDesc($row['sucursalmetadesc']);
				$this->SetSearchKeywords($row['sucursalesearchkeywords']);
				$this->SetNumProducts();
				$this->SetPage();
				$this->SetStart();
				$this->SetNumPages();

				// Load the products for the sucursal
				$this->LoadProductsForSucursal();
			}
		}

		public function SetSucursal($Sucursal)
		{
			$this->_sucursal = $Sucursal;
		}

		public function SetSucursalName($SucursalName)
		{
			$this->_sucursalname = $SucursalName;
		}

		public function GetSucursalName()
		{
			return $this->_sucursalname;
		}

		public function GetSucursal()
		{
			return $this->_sucursal;
		}

		public function SetId($SucursalId)
		{
			$this->_sucursalid = $SucursalId;
		}

		public function GetId()
		{
			return $this->_sucursalid;
		}

		public function GetPageTitle()
		{
			return $this->_sucursalpagetitle;
		}

		public function SetSucursalPageTitle($pagetitle)
		{
			$this->_sucursalpagetitle = $pagetitle;
		}

		public function SetMetaKeywords($Keywords)
		{
			$this->_sucursalmetakeywords = $Keywords;
		}

		public function SetMetaDesc($Desc)
		{
			$this->_sucursalmetadesc = $Desc;
		}

		public function SetCanonicalLink($Link)
		{
			$this->_sucursalcanonicallink = $Link;
		}

		public function SetSearchKeywords($Keywords)
		{
			$this->_sucursalesearchkeywords = $Keywords;
		}

		public function SetPage()
		{
			if (isset($_GET['page'])) {
				$this->_sucursalpage = abs((int)$_GET['page']);
			} else {
				$this->_sucursalpage = 1;
			}
		}

		public function GetPage()
		{
			return $this->_sucursalpage;
		}

		// Workout the number of pages for products in this category
		public function SetNumPages()
		{
			$this->_sucursalnumpages = ceil($this->GetNumProducts() / GetConfig('CategoryProductsPerPage'));
		}

		public function GetNumPages()
		{
			return $this->_sucursalnumpages;
		}

		// Set the start record for the products query
		public function SetStart()
		{
			$start = 0;

			switch ($this->_sucursalpage) {
				case 1: {
					$start = 0;
					break;
				}
				// Page 2 or more
				default: {
					$start = ($this->GetPage() * GetConfig('CategoryProductsPerPage')) - GetConfig('CategoryProductsPerPage');
					break;
				}
			}

			$this->_sucursalestart = $start;
		}

		public function GetStart()
		{
			return $this->_sucursalestart;
		}

		public function SetNumProducts()
		{
			if ($this->GetId() > 0) {
				$query = "
					SELECT COUNT(productid) AS numproducts
					FROM [|PREFIX|]products p
					WHERE prodsucursalid='" . $GLOBALS['ISC_CLASS_DB']->Quote($this->GetId()) . "' AND prodvisible='1'
					" . GetProdCustomerGroupPermissionsSQL() . "
					";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
				$this->_sucursalnumproducts = $row['numproducts'];
			}
		}

		public function GetNumProducts()
		{
			return $this->_sucursalnumproducts;
		}

		/**
		 * Get the SQL used to join the product pricing table when tax
		 * is set to be shown as inclusive for catalog prices.
		 *
		 * @return string SQL containing join to product_tax_pricing.
		 */
		protected function getTaxPricingJoin()
		{
			// Prices entered without tax and shown without tax, so we don't need this join
			if(getConfig('taxDefaultTaxDisplayCatalog') == TAX_PRICES_DISPLAY_EXCLUSIVE &&
				getConfig('taxEnteredWithPrices') == TAX_PRICES_ENTERED_EXCLUSIVE) {
					return '';
			}

			// Not sorting or searching by prices. This join is not necessary
			if(empty($_GET['sort']) || ($_GET['sort'] != 'priceasc' && $_GET['sort'] != 'pricedesc')) {
				return '';
			}

			// Showing prices ex tax, so the tax zone ID = 0
			if(getConfig('taxDefaultTaxDisplayCatalog') == TAX_PRICES_DISPLAY_EXCLUSIVE) {
				$taxZone = 0;
			}
			// Showing prices inc tax, so we need to fetch the applicable tax zone
			else {
				$taxZone = getClass('ISC_TAX')->determineTaxZone();
			}

			return '
				JOIN [|PREFIX|]product_tax_pricing tp
				ON (
					tp.price_reference=p.prodcalculatedprice AND
					tp.tax_zone_id='.$taxZone.' AND
					tp.tax_class_id=p.tax_class_id
				)
			';
		}

		// Load the products to show for this sucursal, taking into account paging, filters, etc
		public function LoadProductsForSucursal()
		{
			$taxJoin = $this->getTaxPricingJoin();
			$query = "
				SELECT p.*, FLOOR(prodratingtotal/prodnumratings) AS prodavgrating, pi.*, ".GetProdCustomerGroupPriceSQL()."
				FROM [|PREFIX|]products p
				LEFT JOIN [|PREFIX|]product_images pi ON (p.productid=pi.imageprodid AND pi.imageisthumb=1)
				".$taxJoin."
				WHERE prodsucursalid='".(int)$this->GetId()."' AND prodvisible='1'
				".GetProdCustomerGroupPermissionsSQL()."
				ORDER BY ".$this->GetSortField().", prodname ASC
			";
			$query .= $GLOBALS['ISC_CLASS_DB']->AddLimit($this->GetStart(), GetConfig('CategoryProductsPerPage'));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$row['prodavgrating'] = (int)$row['prodavgrating'];
				$this->_sucursalproducts[] = $row;
			}
		}

		public function BuildTitle()
		{
			// use preset page title if it exsits
			if (trim($this->GetPageTitle()) != "") {
				$title = $this->GetPageTitle();
			// Build an SEO-friendly page title
			} elseif ($this->GetSucursal() != "") {
				$title = sprintf("%s %s - %s", $this->GetSucursal(), GetLang('Products'), GetConfig('StoreName'));
			} else {
				$title = sprintf("%s %s", GetConfig('StoreName'), GetLang('Sucursales'));
			}

			return $title;
		}

		public function GetProducts(&$Ref)
		{
			$Ref = $this->_sucursalproducts;
		}

		public function HandlePage()
		{
			$this->ShowSucursal();
		}

		public function ShowingAllSucursales()
		{
			return $this->_allsucursales;
		}

		public function ShowSucursal()
		{
			$GLOBALS['SucursalId'] = $this->GetId();
			$GLOBALS['SucursalName'] = $this->GetSucursal();
			if($this->GetPage() > 1) {
				$this->_sucursalcanonicallink = SucursalLink($this->GetSucursalName(), array('page' => $this->GetPage()));
			} else {
				$this->_sucursalcanonicallink = SucursalLink($this->GetSucursalName());
			}

			$GLOBALS['CompareLink'] = CompareLink();

			if ($this->GetSucursal() == "") {
				$GLOBALS['TrailSucursalName'] = GetLang('AllSucursales');
				$this->_allsucursales = true;
			} else {
				$GLOBALS['TrailSucursalName'] = isc_html_escape($this->GetSucursal());
			}

			if ($this->_sucursalmetakeywords != "") {
				$GLOBALS['ISC_CLASS_TEMPLATE']->SetMetaKeywords($this->_sucursalmetakeywords);
			}

			if ($this->_sucursalmetadesc != "") {
				$GLOBALS['ISC_CLASS_TEMPLATE']->SetMetaDescription($this->_sucursalmetadesc);
			}

			if ($this->_sucursalcanonicallink != "") {
				$GLOBALS['ISC_CLASS_TEMPLATE']->SetCanonicalLink($this->_sucursalcanonicallink);
			}

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle($this->BuildTitle());
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("sucursales");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		*	Get a list of all sucursales as <option> tags
		*/
		public function GetSucursalesAsOptions($SelectedSucursal=0)
		{
			$query = "select * from [|PREFIX|]sucursales order by sucursalname asc";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$output = "";

			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				if ($SelectedSucursal == $row['sucursalid']) {
					$sel = 'selected="selected"';
				} else {
					$sel = "";
				}

				$output .= sprintf("<option value='%d' %s>%s</option>", $row['sucursalid'], $sel, isc_html_escape($row['sucursalname']));
			}

			return $output;
		}

		/**
		 * Search for sucursales
		 *
		 * Method will search for all the sucursales and return an array for sucursal records
		 *
		 * @access public
		 * @param array $searchQuery The search query array. Currently will only understand the 'search_query' option
		 * @param int &$totalAmount The referenced variable to store in the total amount of the result
		 * @param int $start The optional start position of the result total. Default is 0
		 * @param int $start The optional limit position of the result total. Default is -1 (no limit)
		 * @return array The array result set on success, FALSE on error
		 */
		static public function searchForItems($searchQuery, &$totalAmount, $start=0, $limit=-1)
		{
			if (!is_array($searchQuery)) {
				return false;
			}

			$totalAmount = 0;

			if (!array_key_exists("search_query", $searchQuery) || $searchQuery["search_query"] == '') {
				return array();
			}

			$fullTextFields = array("bs.sucursalname", "bs.sucursalpagetitle", "bs.sucursalesearchkeywords");

			$sucursales = array();
			$query = "SELECT SQL_CALC_FOUND_ROWS b.*,
							(IF(b.sucursalname='" . $GLOBALS["ISC_CLASS_DB"]->Quote($searchQuery["search_query"]) . "', 10000, 0) +
							 IF(b.sucursalpagetitle='" . $GLOBALS["ISC_CLASS_DB"]->Quote($searchQuery["search_query"]) . "', 10000, 0) +
							 ((" . $GLOBALS["ISC_CLASS_DB"]->FullText(array("bs.sucursalname"), $searchQuery["search_query"], false) . ") * 10) +
							   " . $GLOBALS["ISC_CLASS_DB"]->FullText($fullTextFields, $searchQuery["search_query"], false) . ") AS score
						FROM [|PREFIX|]sucursales b
							INNER JOIN [|PREFIX|]sucursal_search bs ON b.sucursalid = bs.sucursalid
						WHERE ";

			$searchPart = array();

			if (GetConfig("SearchOptimisation") == "fulltext" || GetConfig("SearchOptimisation") == "both") {
				$searchPart[] = $GLOBALS["ISC_CLASS_DB"]->FullText($fullTextFields, $searchQuery["search_query"], true);
			}

			if (GetConfig("SearchOptimisation") == "like" || GetConfig("SearchOptimisation") == "both") {
				$searchPart[] = "b.sucursalname LIKE '%" . $GLOBALS["ISC_CLASS_DB"]->Quote($searchQuery["search_query"]) . "%'";
				$searchPart[] = "b.sucursalpagetitle LIKE '%" . $GLOBALS["ISC_CLASS_DB"]->Quote($searchQuery["search_query"]) . "%'";
				$searchPart[] = "b.sucursalesearchkeywords LIKE '%" . $GLOBALS["ISC_CLASS_DB"]->Quote($searchQuery["search_query"]) . "%'";
			}

			$query .= " " . implode(" OR ", $searchPart) . " ORDER BY score DESC";

			if (is_numeric($limit) && $limit > 0) {
				if (is_numeric($start) && $start > 0) {
					$query .= " LIMIT " . (int)$start . "," . (int)$limit;
				} else {
					$query .= " LIMIT " . (int)$limit;
				}
			}

			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
			$row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result);

			if (!$row) {
				return array();
			}

			$totalAmount = $GLOBALS["ISC_CLASS_DB"]->FetchOne("SELECT FOUND_ROWS()");
			$sucursales[] = $row;

			while ($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
				$sucursales[] = $row;
			}

			return $sucursales;
		}

		/**
		 * Build the searched item results HTML
		 *
		 * Method will build the searched item results HMTL. Method will work with the ISC_SEARCH class to get the results
		 * so make sure that the object is initialised and the DoSearch executed.
		 *
		 * @access public
		 * @return string The search item result HTML on success, empty string on error
		 */
		static public function buildSearchResultsHTML()
		{
			if (!isset($GLOBALS["ISC_CLASS_SEARCH"]) || !is_object($GLOBALS["ISC_CLASS_SEARCH"])) {
				return "";
			}

			$totalRecords = $GLOBALS["ISC_CLASS_SEARCH"]->GetNumResults("sucursal");

			if ($totalRecords == 0) {
				return "";
			}

			$results = $GLOBALS["ISC_CLASS_SEARCH"]->GetResults("sucursal");
			$resultHTML = array();

			if (!array_key_exists("results", $results) || !is_array($results["results"])) {
				return "";
			}

			foreach ($results["results"] as $sucursal) {
				if (!is_array($sucursal) || !array_key_exists("sucursalid", $sucursal)) {
					continue;
				}

				$resultHTML[] = "<a href=\"" . SucursalLink($sucursal["sucursalname"]) . "\">" . isc_html_escape($sucursal["sucursalname"]) . "</a>";
			}

			$resultHTML = implode(", ", $resultHTML);
			$resultHTML = trim($resultHTML);
			return $resultHTML;
		}
	}
