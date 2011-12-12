<?php

	/**
	* ISC_ADDON
	* Handles the execution of all addon modules through the control panel
	*
	* @author Mitchell Harper
	* @copyright Interspire Pty. Ltd.
	* @date	19th Jan 2008
	*/

	class ISC_ADMIN_EXPORTOCASA extends ISC_ADMIN_BASE
	{

	
		/**
		* Constructor
		* Work out which addon we're running so we can show it in the breadcrum trail amongst other things
		*
		* @return Void
		*/
		
				
		public function setDataForSucursal($get){
					
			$result = "";
			
			$get = str_replace("Entrega en ", "", $get);
			$data = explode("(", $get);
			
			$result[0] = trim($data[0]);
			
			$datad = explode(")", $data[1]);
			$result[1] = trim($datad[0]);
			
			$result[2] = "";
			$result[3] = "";
				
				$sucname = str_replace("Sucursal ", "", $data[0]);
				$query = "SELECT e.statename FROM isc_sucursales as s INNER JOIN isc_country_states as e ON e.stateid = s.state_id WHERE sucursalname = '".$sucname."'";			
				$result1 = $GLOBALS['ISC_CLASS_DB']->FetchOne($query);
				
			$result[4] = trim($result1);
			$result[5] = "Argentina";
			
			$getdni = explode("DNI: ", $get);
			$result[6] = "DNI: ".trim($getdni[1]);
			

			return $result;
			
		}

		public function setDataForDomicilio($get){
			
			$result = "";

			$result[0] = "Entrega en Domicilio";
			
			$dir = str_replace("En Domicilio:", "", $get);
			$dir1 = explode(" (", $dir);
			$result[1] = trim($dir1[0]);
			
			$cod1 = explode("CP: ", $get);
			$cod2 = explode(")", $cod1[1]);
			$prevCod = trim($cod2[0]);
			
			$vowels = array("q", "w", "e", "r", "t", "y", "u", "i", "o", "p", "a", "s", "d", "f", "g", "h", "i", "j", "k", "l", "ñ", "z", "x", "c", "v", "b", "n", "m",
							"Q", "W", "E", "R", "T", "Y", "U", "I", "O", "P", "A", "S", "D", "F", "G", "H", "I", "J", "K", "L", "Ñ", "Z", "X", "C", "V", "B", "N", "M");
			$result[2] = str_replace($vowels, "", $prevCod);
			
			$loc1 = str_replace("(", "", $cod2[1]);
			$loc2 = explode(",", $loc1);
			$result[3] = trim($loc2[0]);
			
			$prov1 = explode(" | ", $loc2[1]);
			$result[4] = trim($prov1[0]);
			$result[5] = trim($prov1[1]);
			
			$result[6] = "";
			
			
			return $result;
			
		}

		public function xlsBOF(){
			echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
			return;
		}

		public function xlsEOF(){
			echo pack("ss", 0x0A, 0x00);
			return;
		}

		public function xlsWriteLabel($Row, $Col, $Value = '' ){
			$Value2UTF8=utf8_decode($Value);
			$L = strlen($Value2UTF8);
			echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
			echo $Value2UTF8;
			return;
		}

		public function xlsWriteCell($Row, $Col, $Value = '' ){
			$this->xlsWriteLabel($Row, $Col, $Value);
		}

		public function __construct()
		{
			parent::__construct();

		}

				
		public function Doexport($type)
		{
		
		if($type == "sucursal"){
			$typeexport = 12;
		} else {
			$typeexport = 13;
		}

			 $queryCount = "SELECT count(*)
					FROM isc_orders o
					inner join isc_order_shipping os on o.orderid=os.order_id
					inner join isc_order_addresses oa on os.order_address_id=oa.id
					inner join isc_order_products p on p.orderorderid=o.orderid
					inner join isc_customers c on c.customerid=o.ordcustid
					inner join isc_products as pp on pp.productid = p.ordprodid
					inner join isc_compra as cc on cc.orderid = o.orderid
					where ordstatus=9 and module = ".$typeexport . " group by o.orderid";		
			$NumResults = $GLOBALS['ISC_CLASS_DB']->Fetch($queryCount);
						
			if(count($NumResults) > 0){
			

			 $query = "SELECT o.orderid as orderid, method, module, ordprodname as producttitle,ordprodqty as bultos,c.custconfirstname as firstname,
							c.custconlastname as lastname, pp.prodweight as peso, cc.compratoken as token, pp.prodocasaname as ocasaname, sc.shipphone as shipphone
					FROM isc_orders o
					inner join isc_order_shipping os on o.orderid=os.order_id
					inner join isc_order_addresses oa on os.order_address_id=oa.id
					inner join isc_order_products p on p.orderorderid=o.orderid
					inner join isc_customers c on c.customerid=o.ordcustid
					inner join isc_products as pp on pp.productid = p.ordprodid
					inner join isc_compra as cc on cc.orderid = o.orderid
					inner join isc_shipping_addresses as sc on sc.shipcustomerid = c.customerid
					where ordstatus=9 and module = ".$typeexport . " group by o.orderid";					
			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
	
			
				header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
				header ("Cache-Control: no-cache, must-revalidate");
				header ("Pragma: no-cache");
				header ("Content-type: application/x-msexcel");
				header ("Content-Disposition: attachment; filename=ordenes_export_".$type."_".date("d_m_Y").".xls" );
				header ("Content-Description: PHP/INTERBASE Generated Data" );
					
					$this->xlsBOF(); 
					
						$this->xlsWriteCell(0,0,"TRACKING NUMBER");
						$this->xlsWriteCell(0,1,"ALTERNATIVO");
						$this->xlsWriteCell(0,2,"DESTINATARIO");
						$this->xlsWriteCell(0,3,"NOMBRES");
						$this->xlsWriteCell(0,4,"DOMICILIO");
						$this->xlsWriteCell(0,5,"CODPOSTAL");
						$this->xlsWriteCell(0,6,"LOCALIDAD");
						$this->xlsWriteCell(0,7,"PROVINCIA");						
						$this->xlsWriteCell(0,8,"TELEFONO");
						$this->xlsWriteCell(0,9,"BULTOS");
						$this->xlsWriteCell(0,10,"PESO");
						$this->xlsWriteCell(0,11,"OBSERVACIONES");
						$this->xlsWriteCell(0,12,"PAIS");
						$this->xlsWriteCell(0,13,"CONTRAREEMBOLSO");
						$this->xlsWriteCell(0,14,"SEGURO");
						$this->xlsWriteCell(0,15,"DIAS PLAZO");
						$this->xlsWriteCell(0,16,"SERVICIOS");
						$this->xlsWriteCell(0,17,"CANTREMITOS");
						$this->xlsWriteCell(0,18,"CUENTA");
						$this->xlsWriteCell(0,19,"FACTURA");
						$this->xlsWriteCell(0,20,"REMITO");
						$this->xlsWriteCell(0,21,"FECHA");
						//$this->xlsWriteCell(0,21,"CODIGO DE SEG. CUPON");
					
						
						$r = 1;
						while ($rows = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {						
							
							$orderID = $rows['orderid'];
							
							if($type == "sucursal"){$data = $this->setDataForSucursal($rows['method']);}
							else{$data = $this->setDataForDomicilio($rows['method']);}
							$destinatario = $data[0];
							$domicilio = $data[1];
							$codpostal = $data[2];
							$localidad = $data[3];
							$provincia = $data[4];
							$pais = $data[5];
							$dni = $data[6];
							$shipphone = $rows['shipphone'];							
							$bultos = $rows['bultos'];
							$peso = $rows['peso'];
							if($type == "sucursal"){$observaciones = $dni . " ; ". utf8_decode($rows['ocasaname']);}
							else{$observaciones = utf8_decode($rows['ocasaname']);}
							$nombres = $rows['firstname'] . " " . $rows['lastname'];
							//$codigoSeguridadCupon = $rows['token'];
						
							$this->xlsWriteCell($r,0,$orderID);
							$this->xlsWriteCell($r,1,"");
							$this->xlsWriteCell($r,2,$destinatario);
							$this->xlsWriteCell($r,3,$nombres);
							$this->xlsWriteCell($r,4,$domicilio);
							$this->xlsWriteCell($r,5,$codpostal);
							$this->xlsWriteCell($r,6,$localidad);
							$this->xlsWriteCell($r,7,$provincia);
							$this->xlsWriteCell($r,8,$shipphone);							
							$this->xlsWriteCell($r,9,$bultos);
							$this->xlsWriteCell($r,10,$peso);
							$this->xlsWriteCell($r,11,$observaciones);
							$this->xlsWriteCell($r,12,$pais);
							$this->xlsWriteCell($r,13,"");
							$this->xlsWriteCell($r,14,"");
							$this->xlsWriteCell($r,15,"");
							$this->xlsWriteCell($r,16,"");
							$this->xlsWriteCell($r,17,"");
							$this->xlsWriteCell($r,18,"");
							$this->xlsWriteCell($r,19,"");
							$this->xlsWriteCell($r,20,"");
							$this->xlsWriteCell($r,21,"");
							//$this->xlsWriteCell($r,21,$codigoSeguridadCupon);
						$r++;
						
						
							/*actualizar status*/							
							$updatedCompra = array(
								"compraStatusid" => 2
							);
							$updatedOrders = array(
								"ordstatus" => 2
							);
							$GLOBALS['ISC_CLASS_DB']->UpdateQuery("compra", $updatedCompra, "orderid = ".$orderID);
							$GLOBALS['ISC_CLASS_DB']->UpdateQuery("orders", $updatedOrders, "orderid = ".$orderID);
						
						}

					$this->xlsEOF(); // close the stream				
				
	
								
			}else{?>			
					<script type="text/javascript">
					alert("No hay pedidos esperando envio.");
					window.location="/admin/index.php?ToDo=viewOrders";
					</script>			
				<?php 
			}				
		}
		
		public function HandleToDo($Do)
		{
		//	$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Addons') => "index.php?ToDo=viewDownloadAddons", $this->_addon->GetName() => $_SERVER["PHP_SELF"]);
			//$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
			$this->Doexport($_GET['type']);
			//$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
		}

		
	}