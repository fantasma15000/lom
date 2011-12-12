<?php
$base= "mo000235_vivoensale";
$servidor= "localhost";
$usuario= "mo000235_publi";
$clave="Publi5598";

$sConn = mysql_connect($servidor, $usuario, $clave) or die ("Error de conexion con el server"); 
$dConn = mysql_select_db($base, $sConn) or die ("Imposible conectar con la base de datos"); 
?>
