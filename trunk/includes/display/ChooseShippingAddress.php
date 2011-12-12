<?php

	CLASS ISC_CHOOSESHIPPINGADDRESS_PANEL extends PANEL
	{
		/**
		 * Set the settings for this panel.
		 */
		public function createShippingBox($pid)	
	   {
		
		
 
	
	}

	
		public function SetPanelSettings()
		{
			//Me fijo el codigo de Producto y Cantidad
			$items = getCustomerQuote()->getItems();
			foreach($items as $item) {
			  $pid=$item->getProductId();
			  $quantity=$item->getQuantity();
			  
			}
			
			//No mostrar el Panel a menos que deba mostrarlo.
			$mostrar=0;
			/*$GLOBALS['ShippingInfoBoxDisplay'] = 'display:none';
			$GLOBALS['ShippingSucursalOptionsDisplay'] = 'display:none';*/
			
			
			//Me fijo si el usuario es de una provincia que tiene envio
			$query = "SELECT * FROM isc_shipping_addresses i where shipcustomerid=".getClass('ISC_CUSTOMER')->getCustomerId();		

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			
			if ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {		
			    //Si tiene cargado una provincia y no es capital ni buenos aires.. continuo
				if  ( $row ['shipstateid']>0 and $row ['shipstateid']!=1001 and $row ['shipstateid']!=1006)
				{	
					//me guardo la provincia para usarla despues en sucursales
				    $provinciaId=$row ['shipstateid'];
				
					
					//Me fijo la configuracion del producto
					$query = "SELECT shippingallowed,prodfixedshippingcost,prodfixedshippingcost2 FROM [|PREFIX|]products i where productid=".$pid;								
					$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

					if ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					
					    //cargo informacion que voy a necesitar del producto. 					
						$precioSucursal = number_format($row ['prodfixedshippingcost'] * $quantity, GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
						$precioDomicilio=number_format($row ['prodfixedshippingcost2'] * $quantity, GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
						$shippingallowed=$row ['shippingallowed'];											
						
						
						$textoOpciones="";
						$opcion="";
						
						if ($shippingallowed==1 || $shippingallowed==3) 
							{		
								$query = "select count(*) from isc_sucursales where state_id = ".$provinciaId;
								$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
								$isscuc = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);
								if($isscuc > 0){
								  $textoOpciones.="Precio Entrega en Sucursal: $". $precioSucursal . "<br/>" ;
								  $opcion.='<option value="1">Entrega en Sucursal</option>';	
									//trae telefono
									$query1 = "SELECT shipphone FROM isc_shipping_addresses WHERE shipcustomerid = ".getClass('ISC_CUSTOMER')->getCustomerId();
									$result1 = $GLOBALS['ISC_CLASS_DB']->Query($query1);
									$GLOBALS['Suctel'] = $GLOBALS['ISC_CLASS_DB']->FetchOne($result1);								  
								 }
							}

						if ($shippingallowed==2 || $shippingallowed==3) 
							{$textoOpciones.="Precio Entrega en Domicilio: $". $precioDomicilio . "<br/>" ;
							$opcion.='<option value="2">Entrega en Domicilio</option>';							
							}
						
						$GLOBALS['hiddenMethodPrice'] = "<input type='hidden' name='precioSucursalHidden' value='".$precioSucursal."' /><input type='hidden' name='precioDomicilioHidden' value='".$precioDomicilio."' />";
						
						$GLOBALS['TextoOpciones']=$textoOpciones;
						if ($opcion!="")
						{
						  $opcion='<select onchange="showshipping(this.value)" id="ShippingType" name="ShippingType">'.'<option value="0">Selecciona M&eacute;todo de Env&iacute;o</option>'. $opcion. "</select>";
						}
						$GLOBALS['SelectShippingOptions']=$opcion;
						
						
						if ($shippingallowed>0)
							{$mostrar=1;}

					}
				}
			}
				
			$GLOBALS['SucursalOptionsList'] = $this->GenerateSucursalSelect($provinciaId);
		
			if ($mostrar==1)
			{
			
				$GLOBALS['HideTabMultiple'] = 'display: none';
				$GLOBALS['ActiveTabSingle'] = 'Active';
				$GLOBALS['ShippingInfoBoxDisplay'] = 'display:none';
				$GLOBALS['ShippingSucursalOptionsDisplay'] = 'display:none';
				
				
				$GLOBALS['SNIPPETS']['ShippingAddressList'] = "";
				$GLOBALS['ShippingAddressRow'] = "";
				$count = 0;

				$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');


				$numItems = getCustomerQuote()->getNumPhysicalItems();

				// Get a list of all shipping addresses for this customer and out them as radio buttons
				$shipping_addresses = $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerShippingAddresses();

				if(empty($shipping_addresses) && isset($GLOBALS['CheckoutShippingIntroNoAddresses'])) {
					$GLOBALS['CheckoutShippingIntro'] = $GLOBALS['CheckoutShippingIntroNoAddresses'];
				}

				$GLOBALS['SplitAddressList'] = '';
				foreach($shipping_addresses as $address) {
					$GLOBALS['ShippingAddressId'] = (int) $address['shipid'];
					$GLOBALS['ShipFullName'] = isc_html_escape($address['shipfirstname'].' '.$address['shiplastname']);

					$GLOBALS['ShipCompany'] = '';
					if($address['shipcompany']) {
						$GLOBALS['ShipCompany'] = isc_html_escape($address['shipcompany']).'<br />';
					}

					$GLOBALS['ShipAddressLine1'] = isc_html_escape($address['shipaddress1']);

					if($address['shipaddress2'] != "") {
						$GLOBALS['ShipAddressLine2'] = isc_html_escape($address['shipaddress2']);
					} else {
						$GLOBALS['ShipAddressLine2'] = '';
					}

					$GLOBALS['ShipSuburb'] = isc_html_escape($address['shipcity']);
					$GLOBALS['ShipState'] = isc_html_escape($address['shipstate']);
					$GLOBALS['ShipZip'] = isc_html_escape($address['shipzip']);
					$GLOBALS['ShipCountry'] = isc_html_escape($address['shipcountry']);

					if($address['shipphone'] != "") {
						$GLOBALS['ShipPhone'] = isc_html_escape(sprintf("%s: %s", GetLang('Phone'), $address['shipphone']));
					}
					else {
						$GLOBALS['ShipPhone'] = "";
					}

					$splitAddressFields = array(
						$address['shipfirstname'].' '.$address['shiplastname'],
						$address['shipcompany'],
						$address['shipaddress1'],
						$address['shipaddress2'],
						$address['shipcity'],
						$address['shipstate'],
						$address['shipzip'],
						$address['shipcountry']
					);

					// Please see self::GenerateShippingSelect below.
					
					$splitAddressFields = array_filter($splitAddressFields, array($this, 'FilterAddressFields'));
					$splitAddress = isc_html_escape(implode(', ', $splitAddressFields));
					
					
					$GLOBALS['SplitAddressList'] .= '<option value="'.$address['shipid'].'" <sel'.$address['shipid'].'>>'.$splitAddress.'</option>';

					$GLOBALS['SNIPPETS']['ShippingAddressList'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("CheckoutShippingAddressItem");
					
				}

				$GLOBALS['SNIPPETS']['MultiShippingItem'] = '';
					if(!gzte11(ISC_MEDIUMPRINT) || !GetConfig('MultipleShippingAddresses') || !CustomerIsSignedIn() || $numItems == 1 || !isset($GLOBALS['ISC_CLASS_CHECKOUT'])) {
						$GLOBALS['HideShippingTabs'] = 'display: none';
						$GLOBALS['HideMultiShipping'] = 'display: none';
					}
					else {
						if((isset($_REQUEST['type']) && $_REQUEST['type'] == 'multiple') || getCustomerQuote()->getIsSplitShipping() && CustomerIsSignedIn()) {
							$GLOBALS['HideTabSingle'] = 'display: none';
							$GLOBALS['HideTabMultiple'] = '';
							$GLOBALS['ActiveTabSingle'] = '';
							$GLOBALS['ActiveTabMultiple'] = 'Active';
						}

					
					foreach($items as $item) {
						$GLOBALS['ProductName'] = isc_html_escape($item->getName());

						// Is this product a variation?
						$GLOBALS['ProductOptions'] = '';
						$options = $item->getVariationOptions();
						if(!empty($options)) {
							$GLOBALS['ProductOptions'] .= "<br /><small>(";
							$comma = '';
							foreach($options as $name => $value) {
								if(!trim($name) || !trim($value)) {
									continue;
								}
								$GLOBALS['ProductOptions'] .= $comma.isc_html_escape($name).": ".isc_html_escape($value);
								$comma = ', ';
							}
							$GLOBALS['ProductOptions'] .= ")</small>";
						}


						// Loop through the cart items and add them individually to the list
						$quantity = $item->getQuantity();
						for($i = 1; $i <= $quantity; ++$i) {
							$GLOBALS['AddressFieldId'] = $item->getId().'_'.$i;
							$sel = $item->getAddressId();
							$GLOBALS['ShippingAddressSelect'] = $this->GenerateShippingSelect($GLOBALS['SplitAddressList'], $sel);
							$GLOBALS['SNIPPETS']['MultiShippingItem'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('MultiShippingItem');
						}
					}
				}
			}
		}

		
		private function GenerateSucursalSelect($statid){
				/* arma combo sucursales*/
				$obj = '<select id="ShippingSucursal" name="ShippingSucursal">'.'<option value="0">Selecciona Sucursal</option>';
				
					$queryS = "SELECT sucursalid, sucursalname, sucursaladdress, state_id FROM isc_sucursales where state_id=".$statid;
					$resultS = $GLOBALS['ISC_CLASS_DB']->Query($queryS);

					while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($resultS)) {								
						
						$obj .= '<option value="'.$row['sucursalid'].'">'.$row['sucursalname'].' ('.$row['sucursaladdress'].')</option>';
					}
				$obj .= '</select>';		
				/* arma combo sucursales*/
				return $obj;
		}
		
		/**
		 * Build the shipping address selection box from the string of addresses, optionally
		 * selecting a specific address. The incoming list contains special <sel[id]> markers
		 * to indicate each row and where to put the selected="selected" option. Having these indicators
		 * in the string seems to be a lot faster (for the possible number of loops it could do with many
		 * items in the cart) than manually looping and building the list.
		 *
		 * @param string The list of addresses.
		 * @param int Optionally the ID of the selected address.
		 * @return string The generated address list.
		 */
		private function GenerateShippingSelect($list, $selected=0)
		{
			$list = str_replace('<sel'.$selected.'>', 'selected="selected"', $list);
			$list = preg_replace('#<sel[0-9]+>#', '', $list);
			return $list;
		}

		/**
		 * Filter a field and if it's empty, return false. Used in an array_filter in SetPanelSettings()
		 *
		 * @param string The field value.
		 * @return boolean False if the field is empty.
		 * @see SetPanelSettings
		 */
		private function FilterAddressFields($field)
		{
			if(!$field) {
				return false;
			}
			else {
				return true;
			}
		}
	}