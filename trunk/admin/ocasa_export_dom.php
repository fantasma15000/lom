<?php

	$connection = mysql_connect("localhost","root","");
	mysql_select_db("vivonew");	
	
	
		$query = "SELECT o.orderid as orderid, method, module, ordprodname as producttitle,ordprodqty as bultos,c.custconfirstname as firstname,
						c.custconlastname as lastname, pp.prodweight as peso, cc.compratoken as token
				FROM isc_orders o
				inner join isc_order_shipping os on o.orderid=os.order_id
				inner join isc_order_addresses oa on os.order_address_id=oa.id
				inner join isc_order_products p on p.orderorderid=o.orderid
				inner join isc_customers c on c.customerid=o.ordcustid
				inner join isc_products as pp on pp.productid = p.ordprodid
				inner join isc_compra as cc on cc.orderid = o.orderid
				where ordstatus=9 and module = 13";		
	
		$result= mysql_query($query);
		if(mysql_num_rows($result)!=0){
				
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
			header ("Cache-Control: no-cache, must-revalidate");
			header ("Pragma: no-cache");
			header ("Content-type: application/x-msexcel");
			header ("Content-Disposition: attachment; filename=ordenes_export_domicilio_".date("d_m_Y").".xls" );
			header ("Content-Description: PHP/INTERBASE Generated Data" );
				
				xlsBOF(); 
				
					xlsWriteCell(0,0,"TRACKING NUMBER");
					xlsWriteCell(0,1,"ALTERNATIVO");
					xlsWriteCell(0,2,"DESTINATARIO");
					xlsWriteCell(0,3,"NOMBRES");
					xlsWriteCell(0,4,"DOMICILIO");
					xlsWriteCell(0,5,"CODPOSTAL");
					xlsWriteCell(0,6,"LOCALIDAD");
					xlsWriteCell(0,7,"PROVINCIA");
					xlsWriteCell(0,8,"BULTOS");
					xlsWriteCell(0,9,"PESO");
					xlsWriteCell(0,10,"OBSERVACIONES");
					xlsWriteCell(0,11,"PAIS");
					xlsWriteCell(0,12,"CONTRAREEMBOLSO");
					xlsWriteCell(0,13,"SEGURO");
					xlsWriteCell(0,14,"DIAS PLAZO");
					xlsWriteCell(0,15,"SERVICIOS");
					xlsWriteCell(0,16,"CANTREMITOS");
					xlsWriteCell(0,17,"CUENTA");
					xlsWriteCell(0,18,"FACTURA");
					xlsWriteCell(0,19,"REMITO");
					xlsWriteCell(0,20,"FECHA");
					xlsWriteCell(0,21,"CODIGO DE SEG. CUPON");
				
					
					$r = 1;
					while($rows = mysql_fetch_object($result)) {	
						
						$orderID = $rows->orderid;
						
						$data = setDataForDomicilio($rows->method);
						$destinatario = utf8_encode($data[0]);
						$domicilio = utf8_encode($data[1]);
						$codpostal = utf8_encode($data[2]);
						$localidad = utf8_encode($data[3]);
						$provincia = utf8_encode($data[4]);
						$pais = utf8_encode($data[5]);
						$dni = $data[6];
						$bultos = $rows->bultos;
						$peso = $rows->peso;
						$observaciones = utf8_encode($rows->producttitle);
						$nombres = utf8_encode($rows->firstname) . " " . utf8_encode($rows->lastname);
						$codigoSeguridadCupon = $rows->token;
					
						xlsWriteCell($r,0,$orderID);
						xlsWriteCell($r,1,"");
						xlsWriteCell($r,2,$destinatario);
						xlsWriteCell($r,3,$nombres);
						xlsWriteCell($r,4,$domicilio);
						xlsWriteCell($r,5,$codpostal);
						xlsWriteCell($r,6,$localidad);
						xlsWriteCell($r,7,$provincia);
						xlsWriteCell($r,8,$bultos);
						xlsWriteCell($r,9,$peso);
						xlsWriteCell($r,10,$observaciones);
						xlsWriteCell($r,11,$pais);
						xlsWriteCell($r,12,"");
						xlsWriteCell($r,13,"");
						xlsWriteCell($r,14,"");
						xlsWriteCell($r,15,"");
						xlsWriteCell($r,16,"");
						xlsWriteCell($r,17,"");
						xlsWriteCell($r,18,"");
						xlsWriteCell($r,19,"");
						xlsWriteCell($r,20,"");
						xlsWriteCell($r,21,$codigoSeguridadCupon);
					$r++;
					
						/*actualizar status*/
						$queryUP1 = "UPDATE isc_compra SET compraStatusid = 2 WHERE orderid = ".$orderID;
						mysql_query($queryUP1);
						$queryUP2 = "UPDATE isc_orders SET ordstatus = 2 WHERE orderid = ".$orderID;
						mysql_query($queryUP2);							
					
					}

				xlsEOF(); // close the stream				
			
			
			mysql_free_result($result);
			mysql_close($connection);		
							
		}else{?>			
				<script type="text/javascript">
				//location.href="index.php?option=com_rsttarticles&view=mls";
				alert("No hay pedidos esperando envio.");
				</script>			
			<?php 
		}				


mysql_close($connection);

function setDataForSucursal($get){
	
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
		$result1= mysql_query($query);	
		$result1 = mysql_result($result1, 0);
		
	$result[4] = trim($result1);
	$result[5] = "Argentina";
	
	$getdni = explode("DNI: ", $get);
	$result[6] = "DNI: ".trim($getdni[1]);
	

	return $result;
	
}

function setDataForDomicilio($get){
	
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

function xlsBOF(){
	echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
	return;
}

function xlsEOF(){
	echo pack("ss", 0x0A, 0x00);
	return;
}

function xlsWriteLabel($Row, $Col, $Value = '' ){
	$Value2UTF8=utf8_decode($Value);
	$L = strlen($Value2UTF8);
	echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
	echo $Value2UTF8;
	return;
}

function xlsWriteCell($Row, $Col, $Value = '' ){
	xlsWriteLabel($Row, $Col, $Value);
}


?>


