<?php
include "../../../config/config.php";

$servidor = $GLOBALS['ISC_CFG']["dbServer"];
$usuariodb = $GLOBALS['ISC_CFG']["dbUser"];
$senhadb = $GLOBALS['ISC_CFG']["dbPass"];
$bancodados = $GLOBALS['ISC_CFG']["dbDatabase"];
$prefixotabela = $GLOBALS['ISC_CFG']["tablePrefix"];
$nomeloja = $GLOBALS['ISC_CFG']["StoreName"];
$emailloja = $GLOBALS['ISC_CFG']["OrderEmail"];
$urlloja = $GLOBALS['ISC_CFG']["ShopPath"];

$conexao2 = mysql_connect($servidor, $usuariodb, $senhadb) or print(mysql_error());
$selecionabanco = mysql_select_db($bancodados,$conexao2) or print(mysql_error());

//Modulo
function corinthias($modulo, $alvo){
$prefixotabela = $GLOBALS['ISC_CFG']["tablePrefix"];
$SCBoleto = mysql_query("select * from ".$prefixotabela."module_vars where modulename='$modulo' and variablename='$alvo'") or print(mysql_error());
$ftM = mysql_fetch_array($SCBoleto);
return $ftM['variableval'];
}

//Form
function ronaldo($valor){
$prefixotabela = $GLOBALS['ISC_CFG']["tablePrefix"];
$form = mysql_query("select * from ".$prefixotabela."formfieldsessions where formfieldsessioniformsessionid='$valor' and formfieldfieldlabel ='CPF'") or print(mysql_error());
$dados = mysql_fetch_array($form);

$var = explode('"',$dados[formfieldfieldvalue]);

return $var[1];
}


//Pedido
$selectorder = mysql_query("select * from ".$prefixotabela."orders where orderid='".$itemId."'") or print(mysql_error());
$fetch_order = mysql_fetch_array($selectorder);
$clientecustomer = $fetch_order['ordcustid'];

//Cliente
$selectcustomer = mysql_query("select * from ".$prefixotabela."customers where customerid='".$clientecustomer."'") or print(mysql_error());
$fetch_customer = mysql_fetch_array($selectcustomer);

?>