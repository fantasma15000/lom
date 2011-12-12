<?php

	$connection = mysql_connect("localhost","root","");
	mysql_select_db("vivonew");	
	
	
		$query = "SELECT orderid, method, module FROM isc_orders o
				inner join isc_order_shipping os on o.orderid=os.order_id
				inner join isc_order_addresses oa on os.order_address_id=oa.id
				where ordstatus=9";		
	
		$result= mysql_query($query);
		if(mysql_num_rows($result)!=0){
				
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
			header ("Cache-Control: no-cache, must-revalidate");
			header ("Pragma: no-cache");
			header ("Content-type: application/x-msexcel");
			header ("Content-Disposition: attachment; filename=ordenes_export.xls" );
			header ("Content-Description: PHP/INTERBASE Generated Data" );
				
				xlsBOF(); 
				
					xlsWriteCell(0,0,"ALTERNATIVO");
					xlsWriteCell(0,1,"DESTINATARIO");
					xlsWriteCell(0,2,"DOMICILIO");
					xlsWriteCell(0,3,"CODPOSTAL");
					xlsWriteCell(0,4,"LOCALIDAD");
					xlsWriteCell(0,5,"PROVINCIA");
					xlsWriteCell(0,6,"BULTOS");
					xlsWriteCell(0,7,"PESO");
					xlsWriteCell(0,8,"OBSERVACIONES");
					xlsWriteCell(0,9,"PAIS");
					xlsWriteCell(0,10,"CONTRAREEMBOLSO");
					xlsWriteCell(0,11,"SEGURO");
					xlsWriteCell(0,12,"DIAS PLAZO");
					xlsWriteCell(0,13,"SERVICIOS");
					xlsWriteCell(0,14,"CANTREMITOS");
					xlsWriteCell(0,15,"CUENTA");
					xlsWriteCell(0,16,"FACTURA");
					xlsWriteCell(0,17,"REMITO");
					xlsWriteCell(0,18,"FECHA");
				
					
					$r = 1;
					while($rows = mysql_fetch_object($result)) {	

					if($rows->module == 12){
						$data = setDataForSucursal($rows->method);						
						$destinatario = $data[0];
						$domicilio = $data[1];
						$codpostal = $data[2];
						$localidad = $data[3];
						$provincia = $data[4];
						$pais = $data[5];
						$dni = $data[6];
					} elseif($rows->module == 13){
						$data = setDataForDomicilio($rows->method);
						$destinatario = $data[0];
						$domicilio = $data[1];
						$codpostal = $data[2];
						$localidad = $data[3];
						$provincia = $data[4];
						$pais = $data[5];
						$dni = "";
					}
					
						xlsWriteCell($r,0,$dni);
						xlsWriteCell($r,1,$destinatario);
						xlsWriteCell($r,2,$domicilio);
						xlsWriteCell($r,3,$codpostal);
						xlsWriteCell($r,4,$localidad);
						xlsWriteCell($r,5,$provincia);
						xlsWriteCell($r,6,"");
						xlsWriteCell($r,7,"");
						xlsWriteCell($r,8,"");
						xlsWriteCell($r,9,$pais);
						xlsWriteCell($r,10,"");
						xlsWriteCell($r,11,"");
						xlsWriteCell($r,12,"");
						xlsWriteCell($r,13,"");
						xlsWriteCell($r,14,"");
						xlsWriteCell($r,15,"");
						xlsWriteCell($r,16,"");
						xlsWriteCell($r,17,"");
						xlsWriteCell($r,18,"");
					$r++;
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
	$result[2] = trim($cod2[0]);
	
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


