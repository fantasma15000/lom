<?php

global $itemId;
$itemId = $_GET['pedido'];

include "dados.php";

//Variaveis do modulo
$EmailPagSeguro	= corinthias("checkout_mercadopago","pagemail");
$token	= corinthias("checkout_mercadopago","token");

$paymenturl = "https://www.mercadopago.com/mla/buybutton";
			   
	$total = $fetch_order['total_inc_tax'];
	$valorfinal2 = number_format($total, 2, '.', ',');


echo "<br><br><center><h2><br>Aguarde, Redirecionando a Mercado Pago...</h2><br>
<form name='mercadopago' action='".$paymenturl."' method='POST'>
<input type='hidden'name='currency' value='ARG'/>
<input type='hidden' name='price' value='".$valorfinal2."' />
<input type='hidden' name='url_process' value='".$urlloja. "/'/>
<input type='hidden' name='url_succesfull' value='".$urlloja. "/'/>
<input type='hidden' name='url_cancel' value='".$urlloja. "/'/>
<input type='hidden' name='acc_id' value='".$EmailPagSeguro."'/>
<input type='hidden' name='shipping_cost' value='0'/>
<input type='hidden' name='ship_cost_mode' value=''/>
<input type='hidden' name='op_retira' value=''/>
<input type='hidden' name='enc' value='".$token."'/>
<input type='hidden' name='item_id' value='".$fetch_order['orderid']."' />
<input type='hidden' name='name' value='PEDIDO #".$fetch_order['orderid']." de vivoonsale.com' />
<input type='hidden' name='cart_cep' value='".$fetch_order['ordbillzip']."'/>
<input type='hidden' name='cart_street' value='" . $fetch_order['ordbillstreet1'] . "'/>
<input type='hidden' name='cart_number' value=''/>
<input type='hidden' name='cart_complement' value=''/>
<input type='hidden' name='cart_phone' value='" . $fetch_customer['custconphone'] . "'/>
<input type='hidden' name='cart_district' value='" . $basket['delInf']['suburb'] . "'/>
<input type='hidden' name='cart_city' value='" . $fetch_order['ordbillsuburb'] . "'/>
<input type='hidden' name='cart_name' value='".$fetch_order['ordbillfirstname']."'/>
<input type='hidden' name='cart_surname' value='".$fetch_order['ordbilllastname']."'/>
<input type='hidden' name='cart_email' value='" . $fetch_customer['custconemail'] . "'/>

</form>
";



?>
<script type="text/javascript"> window.onload = function(){ document.forms[0].submit(); } </script>
