<?php

	
	if(isset($_GET['save']) && $_GET['save'] == 1){
		
		mysql_connect ("localhost", "mo000235_inter", "nafuma83PE") or die('No se puede conectar a la base de datos. ' . mysql_error());
		mysql_select_db ("mo000235_interspire"); 	
		
		$tunombre = $_GET['tunombre'];
		$tuemail = $_GET['tuemail'];
		$rnombre1 = $_GET['rnombre1'];
		$remail1 = $_GET['remail1'];
		$rnombre2 = $_GET['rnombre2'];
		$remail2 = $_GET['remail2'];
		
		$sql = "insert into isc_recommend (recomienda_nombre, recomienda_email, amigo1_nombre, amigo1_email, amigo2_nombre, amigo2_email)
		values ('".$tunombre."', '".$tuemail."', '".$rnombre1."', '".$remail1."', '".$rnombre2."', '".$remail2."')";
		$saved = mysql_query($sql);
		
		$headers = "From: info@vivoensale.com\r\nContent-type: text/html\r\n";
		
			$subject = utf8_decode($rnombre1).", ".$tunombre." te recomienda Vivo en Sale";
			$texto = $rnombre1.": <br />".$tunombre." te recomienda Vivo en Sale<br /><br />";
			$texto .= "<img src='http://www.vivoensale.com/product_images/email_logo.png' alt='Vivo en Sale' /><br /><br />";
			$texto .= "Ingresa a <a href='http://www.vivoensale.com/categories.php?category=Buenos-Aires'>http://www.vivoensale.com/</a> y empez&aacute; a Vivir en Sale!";		
			$sendmail = mail($remail1, $subject, utf8_decode($texto), $headers);
		
		if($remail2 != ""){
			$subject = utf8_decode($rnombre2).", ".$tunombre." te recomienda Vivo en Sale";
			$texto = $rnombre2.": <br />".$tunombre." te recomienda Vivo en Sale<br /><br />";
			$texto .= "<img src='http://www.vivoensale.com/product_images/email_logo.png' alt='Vivo en Sale' /><br /><br />";
			$texto .= "Ingresa a <a href='http://www.vivoensale.com/categories.php?category=Buenos-Aires'>http://www.vivoensale.com/</a> y empez&aacute; a Vivir en Sale!";		
			$sendmail = mail($remail2, $subject, utf8_decode($texto), $headers);
		}
		
		echo $sendmail;
		
	}

?>