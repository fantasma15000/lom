<?php
	
	if(isset($_GET['save']) && $_GET['save'] == 1){
		
		mysql_connect ("localhost", "mo000235_inter", "nafuma83PE") or die('No se puede conectar a la base de datos. ' . mysql_error());
		mysql_select_db ("mo000235_interspire"); 	
		
		$ciudad = $_GET['ciudad'];
		if(!$ciudad){$ciudad = 18;}
		$email = $_GET['email'];
		
		$sql = "insert into isc_suscriptions (email, provincia_id) values ('".$email."', ".$ciudad.")";
		$saved = mysql_query($sql);
		
		echo $saved;
		
	}

?>