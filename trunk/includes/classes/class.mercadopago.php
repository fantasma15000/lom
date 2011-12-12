<?php

	class ISC_MERCADOPAGO
	{

		public function __construct()
		{		
		}

			 
	public function EmailCuponToCustomer($cuponToken,$control,$first,$last,$email,$validez,$maildescription,$brandname,$brandinfo, $shipdata = "")
	{
	   $GLOBALS['CuponToken'] = $cuponToken;
	   $GLOBALS['Control'] = $control;
	   $GLOBALS['First'] = $first;
	   $GLOBALS['Last'] = $last;
	   $GLOBALS['Email'] = $email;

	   $GLOBALS['Validez'] = substr($validez,0,10);
	   $GLOBALS['Maildescription'] = $maildescription;
	   $GLOBALS['Validez'] = substr($validez, 0, 10);
	   $GLOBALS['Maildescription'] = nl2br($maildescription);
	   $GLOBALS['Brandname'] = $brandname;
	   $GLOBALS['Brandinfo'] = $brandinfo;
	   $GLOBALS['Control'] = $control;

	   $GLOBALS['ShText_1'] = $shipdata;
	   if($shipdata == ""){$GLOBALS['ShowMethod'] = "none";}
	   else{$GLOBALS['ShowMethod'] = "block";}
	   
	   $emailTemplate = FetchEmailTemplateParser();
	   
	   
	   $emailTemplate->SetTemplate("cupon_email");
		$message = $emailTemplate->ParseTemplate(true);

		// Create a new email API object to send the email
		$store_name = GetConfig('StoreName');
		$obj_email = GetEmailClass();
		$obj_email->From(GetConfig('OrderEmail'), $store_name);
		$obj_email->Set("Subject", sprintf(GetLang('YourCuponFrom'), $store_name));
		$obj_email->AddBody("html", $message);
		$obj_email->AddRecipient($email, "", "h");
		$email_result = $obj_email->Send();
		
		// If the email was sent ok, show a confirmation message
		if ($email_result['success']) {
			return true;
		}
		else {
			// Email error
			return false;
		}
		
	}

	
	
		public function procesaOk($orderId)
		{
		 		
				$query1 = "SELECT module FROM isc_order_shipping WHERE order_id = ".$orderId;
				//echo $query1;
				$result1 = $GLOBALS['ISC_CLASS_DB']->Query($query1);
				while ($row1 = $GLOBALS['ISC_CLASS_DB']->Fetch($result1)) {
					$hasShipping = $row1['module'];
				}
				if($hasShipping == 12 || $hasShipping == 13){
					$updateOrders = array(
						"ordstatus" => 9,
					);
				} else {
					$updateOrders = array(
						"ordstatus" => 10,
					);
				}
				
				$when="orderid=" . $orderId;
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("orders", $updateOrders, "orderid='".$GLOBALS['ISC_CLASS_DB']->Quote($orderId)."'"); 
				
				$updateCompra = array(
					"comprastatusId" => 10,
				);
				$when="orderid=" . $orderId;
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("compra", $updateCompra, "orderid='".$GLOBALS['ISC_CLASS_DB']->Quote($orderId)."'");
				
				
				
				$query = "SELECT com.compraToken as token, com.compraSeguridad as control  ,pr.productid as productid, ordstatus, orderprodid,orderorderid,ordprodqty,i.`custconfirstname`, i.`custconlastname`, i.`custconemail`,validez,ProductMailDescription,brandname , brandinfo,sh.method as shmethod
FROM isc_order_products p INNER JOIN isc_orders o on p.orderorderid=o.orderid
				INNER JOIN isc_products pr on pr.productid=p.ordprodid
				INNER JOIN isc_brands b on b.brandid=pr.prodbrandid
				INNER JOIN isc_customers  i on i.customerid=o.ordcustid
				LEFT JOIN isc_order_shipping as sh on sh.order_id = o.orderid
        INNER JOIN isc_compra com on com.orderId=o.orderid  where (com.orderid)='" . $orderId. "'";
				
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {

						$shipdata = $row['shmethod'];
				
					$this->EmailCuponToCustomer($row['token'],$row['control'],$row['custconfirstname'],$row['custconlastname'],$row['custconemail'],$row['validez'],$row['ProductMailDescription'],$row['brandname'],$row['brandinfo'], $shipdata );
				}
				
				
		}
		
		
		public function getOrders()
		{
			$query="SELECT orderid from isc_orders where  deleted=0 and ordstatus=7  order by rand() limit 35";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			return $result;
		}
		
		
		public function HandleOnline()
		{
				/*	if (count($_POST)!=0){
				$link=fopen("text.txt","w");
				fwrite($link,var_export($_POST,true));
				$orderid=$_POST['seller_op_id'];
				fwrite($link,"ORDERID1:". $orderid);
				$orderid=str_replace('%28%23','', $orderid);
				$orderid=str_replace('%29','', $orderid);
				fwrite($link,"ORDERID2:".$orderid);				
				fclose($link);
			}*/


			
		    $link=fopen("text.txt","a");		   
			if (isset($_POST['acc_id'])  && isset($_POST['status']) )
			  {
				  if ($_POST['acc_id']=="25828434" ) 
				  {
					  if ($_POST['status']=="A") 
								{
									$orderid=$_POST['seller_op_id'];
									$orderid=str_replace('%28%23','', $orderid);
									$orderid=str_replace('%29','', $orderid);
									fwrite($link,"order:".$orderid."\n");			
									$this->procesaOk($orderid);
								}
					}
				}
				
			fclose($link);
		}
		
		public function HandleOffline()
		{
		
			$result = $this->getOrders();
			//  $link=fopen("offline.txt","a");	
			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {	
				$url ="https://www.mercadopago.com/mla/sonda" ;  	
				$postData = array("acc_id" => "25828434",
								"seller_op_id" => "(#".trim($row['orderid']).")",
								"sonda_key" => "v1fsMbMdIUQoiAPViJwkrESjiRQ%3D");   

				$elements = array();
				foreach ($postData as $name=>$value) {
					$elements[] = "{$name}=".urlencode($value);
				}
				$postData = implode ("&", $elements);	
				 
				$handler = curl_init();

				curl_setopt($handler, CURLOPT_URL, $url);
				curl_setopt($handler, CURLOPT_POST,true);
				curl_setopt($handler, CURLOPT_POSTFIELDS, $postData);
				curl_setopt($handler, CURLOPT_RETURNTRANSFER,true);
				//curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
				$response = curl_exec ($handler);
				curl_close($handler);
				$xml=simplexml_load_string($response);		 
				if ($xml->message=="OK"){				    
				    if ($xml->operation->status=="A") 
					{
					 //echo $row['orderid'];
			//			fwrite($link,"order:".$row['orderid']."\n");	
					    $this->procesaOk($row['orderid']);
					}
				}
				else{
					// HACER LO QUE QUIERAS (el mensaje de error esta en $xml->message)
				}
				
			}	
		//	fclose($link);
			

			
		}
		
		
		public function getOrders2()
		{
			if (isset($_GET['extra']) )
			{
			  $extra=$_GET['extra'];
			}
			$query="SELECT orderid from isc_orders where  deleted=0 and ordstatus=7 " . $extra." order by rand() limit 15";
			
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			return $result;
		}
		
		public function HandleTest()
		{
		/*echo "test procesaok";
		     if (isset($_GET['manualOK']) )
			 {
			 echo $_GET['manualOK'];
			    $this->procesaOk($_GET['manualOK']);
				die();
			 }*/
			$result = $this->getOrders2();

			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {	
				$url ="https://www.mercadopago.com/mla/sonda" ;  	
				$postData = array("acc_id" => "25828434",
								"seller_op_id" => "(#".trim($row['orderid']).")",
								"sonda_key" => "v1fsMbMdIUQoiAPViJwkrESjiRQ%3D");   

				$elements = array();
				foreach ($postData as $name=>$value) {
					$elements[] = "{$name}=".urlencode($value);
				}
				$postData = implode ("&", $elements);	
				 
				$handler = curl_init();

				curl_setopt($handler, CURLOPT_URL, $url);
				curl_setopt($handler, CURLOPT_POST,true);
				curl_setopt($handler, CURLOPT_POSTFIELDS, $postData);
				curl_setopt($handler, CURLOPT_RETURNTRANSFER,true);
				//curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
				$response = curl_exec ($handler);
				curl_close($handler);
				$xml=simplexml_load_string($response);		 
				echo "<p>" .$row['orderid'] . ":". $xml->operation->status ."</p>" ;
				
				
			}
			

			
		}
		
	}
