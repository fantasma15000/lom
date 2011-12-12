<?php

	/**
	* This is the OCASASUCURSAL shipping module for Interspire Shopping Cart. To enable
	* OCASASUCURSAL in Interspire Shopping Cart login to the control panel and click the
	* Settings -> Shipping Settings tab in the menu.
	*/
	class SHIPPING_OCASASUCURSAL extends ISC_SHIPPING
	{

		/**
		* Variables for the OCASASUCURSAL shipping module
		*/

		/*
			The delivery type for OCASASUCURSAL shipments
		*/
		private $_deliverytype = "";

		/*
			The destination country ISO code for OCASASUCURSAL shipments
		*/
		private $_destcountry = "";

		/*
			The destination country zip for OCASASUCURSAL shipments
		*/
		private $_destzip = "";

		/*
			The shipping rate OCASASUCURSAL shipments
		*/
		private $_shippingrate = "";

		/*
			The packaging type for OCASASUCURSAL shipments
		*/
		private $_packagingtype = "";

		/*
			The destination type (residential or commercial) for OCASASUCURSAL shipments
		*/
		private $_destination = "";

		/*
			Shipping class constructor
		*/
		public function __construct()
		{

			// Setup the required variables for the OCASASUCURSAL shipping module
			parent::__construct();
			$this->_name = GetLang('OCASASUCURSALName');
			$this->_image = "ocasa_logo.gif";
			$this->_description = GetLang('OCASASUCURSALDesc');
			$this->_help = GetLang('OCASASUCURSALHelp');
			$this->_height = 310;

			$this->_deliverytypes = array(
				"1DM" => GetLang('OCASASUCURSALDeliveryType1'),
				"1DA" => GetLang('OCASASUCURSALDeliveryType2'),
			);

		}

		/**
		* Custom variables for the shipping module. Custom variables are stored in the following format:
		* array(variable_id, variable_name, variable_type, help_text, default_value, required, [variable_options], [multi_select], [multi_select_height])
		* variable_type types are: text,number,password,radio,dropdown
		* variable_options is used when the variable type is radio or dropdown and is a name/value array.
		*/
		public function SetCustomVars()
		{

		

		}

		/**
		* Test the shipping method by displaying a simple HTML form
		*/
		public function TestQuoteForm()
		{

		}

		/**
		* Get the shipping quote and display it in a form
		*/
		public function TestQuoteResult()
		{

		}

		private function GetQuote()
		{

			// The following array will be returned to the calling function.
			// It will contain at least one ISC_SHIPPING_QUOTE object if
			// the shipping quote was successful.

			$ups_quote = array();

			// Connect to OCASASUCURSAL.com to retrieve a live shipping quote
			$result = "";
			$valid_quote = false;
			$action = "3";
			$ups_url = "http://www.ocasa.com/using/services/rave/qcostcgi.cgi?accept_OCASASUCURSAL_license_agreement=yes&";

			// for some reason the options are stored url encoded (like with + instead of space)
			$shippingRate = urldecode($this->_shippingrate);

			// OCASASUCURSAL will only recognise the ZIP part of ZIP+4 for US addresses - drop it
			$zip = $this->_destzip;
			if ($this->_destcountry == 'US') {
				// replace either XXXXXYYYY or XXXXX-YYYY with just XXXX
				// shouldn't affect quotes since OCASASUCURSAL will throw an error for anything other than 5 digits
				$zip = preg_replace('#^(\d{5})-?\d{4}$#', '\1', $zip);
			}

			$post_vars = array(
				"10_action" => $action,
				"13_product" => $this->_deliverytype,
				"14_origCountry" => $this->_origin_country['country_iso'],
				"15_origPostal" => $this->_origin_zip,
				"19_destPostal" => $zip,
				"22_destCountry" => $this->_destcountry,
				"23_weight" => $this->_weight,
				"47_rate_chart" => $shippingRate,
				"48_container" => $this->_packagingtype,
				"49_residential" => $this->_destination,
			);

			// build a query here for use with either curl or fopen (though, this should probably be
			// using PostToRemoteFileAndGetResponse)

			$post_vars = http_build_query($post_vars);

			if(function_exists("curl_exec")) {
				// Use CURL if it's available
				$ch = @curl_init($ups_url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vars);
				curl_setopt($ch, CURLOPT_TIMEOUT, 60);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				// Setup the proxy settings if there are any
				if (GetConfig('HTTPProxyServer')) {
					curl_setopt($ch, CURLOPT_PROXY, GetConfig('HTTPProxyServer'));
					if (GetConfig('HTTPProxyPort')) {
						curl_setopt($ch, CURLOPT_PROXYPORT, GetConfig('HTTPProxyPort'));
					}
				}

				if (GetConfig('HTTPSSLVerifyPeer') == 0) {
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				}

				$result = curl_exec($ch);

				if($result != "") {
					$valid_quote = true;
				}
			}
			else {
				// Use fopen instead
				if($fp = @fopen($ups_url . $post_vars, "rb")) {
					$result = "";

					while(!feof($fp))
						$result .= fgets($fp, 4096);

					@fclose($fp);
					$valid_quote = true;
				}
			}

			$this->SetCustomVars();

			if($valid_quote) {
				$result = explode("%", $result);

				if(count($result) > 5) {
					$Error = false;
					$quote_desc = "";

					// Set the description of the method
					foreach($this->_variables['deliverytypes']['options'] as $k => $v) {
						if($v == $result[1]) {
							$quote_desc = $k;
						}
					}

					// Create a quote object
					$quote = new ISC_SHIPPING_QUOTE($this->GetId(), $this->GetDisplayName(), $result[8], $quote_desc);
					return $quote;
				}
				else {
					$this->SetError($result[1]);
					return false;
				}
			}
			else {
				// Couldn't get to OCASASUCURSAL.com
				$this->SetError(GetLang('OCASASUCURSALOpenError'));
				return false;
			}

			return $ups_quote;
		}

		public function GetServiceQuotes()
		{
			$this->ResetErrors();
			$QuoteList = array();
			// Set the OCASASUCURSAL-specific variables
			$this->_destcountry = $this->_destination_country['country_iso'];
			$this->_destzip = $this->_destination_zip;
			$this->_shippingrate = $this->GetValue("shippingrate");
			$this->_packagingtype = $this->GetValue("packagingtype");
			$this->_destination_rescom = $this->GetValue("destination");

			if($this->_destination_rescom == "COM") {
				$this->_destination = "0";
			} else {
				$this->_destination = "1";
			}

			// Convert the weight to pounds
			$this->_weight = ConvertWeight($this->_weight, 'pounds');

			// Return quotes for all available OCASASUCURSAL service types
			$services = $this->GetValue("deliverytypes");

			if(!is_array($services) && $services != "") {
				$services = array($services);
			}

			foreach($services as $service) {
				// Set the service type
				$this->_deliverytype = $service;

				// Next actually retrieve the quote
				$err = "";
				$result = $this->GetQuote($err);

				// Was it a valid quote?
				if(is_object($result)) {
					array_push($QuoteList, $result);
				// Invalid quote, log the error
				} else {
					foreach($this->GetErrors() as $error) {
						$GLOBALS['ISC_CLASS_LOG']->LogSystemError(array('shipping', $this->GetName()), $this->_deliverytypes[$service].": " .GetLang('ShippingQuoteError'), $error);
					}
				}
			}
			return $QuoteList;
		}

		/**
		 * Get a human readable list of of the delivery methods available for the shipping module
		 *
		 * @return array
		 **/
		public function GetAvailableDeliveryMethods()
		{
			// Return quotes for all available OCASASUCURSAL service types
			$methods = $this->GetValue("deliverytypes");

			if (!is_array($methods) && $methods != "") {
				$methods = array($methods);
			} elseif (!is_array($methods)) {
				$methods = array();
			}

			$displayName = $this->GetDisplayName();

			foreach ($methods as $key => $method) {
				$methods[$key] = $displayName.' ('.$this->_deliverytypes[$method].')';
			}

			return $methods;
		}

		/**
		* Generate a link to track items for OCASASUCURSAL.
		*
		* @return string The tracking URL for OCASASUCURSAL shipments.
		*/
		public function GetTrackingLink($trackingNumber = "")
		{
			//return "http://www.ocasa.com/WebTracking/track?loc=en_US&WT.svl=PNRO_L1";
			return "http://wwwapps.ocasa.com/WebTracking/processRequest?&tracknum=" . urlencode($trackingNumber);
		}
	}
