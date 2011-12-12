<?php

	class CHECKOUT_MERCADOPAGO extends ISC_CHECKOUT_PROVIDER
	{


		var $_requiresSSL = false;

		var $_paymenthelp = "";

		var	$_id = "checkout_mercadopago";


		public function __construct()
		{

			parent::__construct();
			$this->_name = 'MercadoPago - ML';
			$this->_description = 'Pago con MercadoPago';
			$this->SetImage('logo.jpg');
			$this->_help = 'Pagar con Mercado Pago';
			$this->_paymenttype = PAYMENT_PROVIDER_OFFLINE;
		}

	
		function SetCustomVars()
		{

			$this->_variables['displayname'] = array("name" => "Nome",
			   "type" => "textbox",
			   "help" => 'Nome do Modulo',
			   "default" => "MercadoPago Pagamentos",
			   "required" => true
			);


			$this->_variables['availablecountries'] = array("name" => "Continentes",
			   "type" => "dropdown",
			   "help" => GetLang('PagContinente'),
			   "default" => "all",
			   "required" => true,
			   "options" => GetCountryListAsNameValuePairs(),
				"multiselect" => true
			);


			$this->_variables['pagemail'] = array("name" => "Numero da Conta",
			   "type" => "textbox",
			   "help" => '',
			   "default" => "000000000",
			   "required" => true
			);
			
						$this->_variables['token'] = array("name" => "Chave Criptografada",
			   "type" => "textbox",
			   "help" => 'Ponha o token de retorno automatico de pedidos.',
			   "default" => "chave-criptografada",
			   "required" => false
			);
			

			
			
			
		}

	function getofflinepaymentmessage(){
	

$orders = $this->GetOrders();
		$orderIds = array();
		foreach($orders as $order) {
			$orderIds[] = $order['orderid'];
}

if(@$_REQUEST['action']=='order_status'){
$billhtml = "";
}else{
$billhtml = "
<div class='FloatLeft'><b>MercadoPago</b>
<br />
<a href=\"javascript:window.open('".$GLOBALS['ShopPath']."/modules/checkout/mercadopago/repagar.php?pedido=".$orderIds[0]."','popup','width=800,height=800,scrollbars=yes');void(0);\">
<img src='".$GLOBALS['ShopPath']."/modules/checkout/mercadopago/images/logo.jpg' border='0'></a>
</div><br>
<div style='display:none;'>
Link Direto:<br>
<a href='".$GLOBALS['ShopPath']."/modules/checkout/mercadopago/repagar.php?pedido=".$orderIds[0]."' target='_blank'>".$GLOBALS['ShopPath']."/modules/checkout/mercadopago/repagar.php?pedido=".$orderIds[0]."</a></div>
<br />
";
}						
return $billhtml;

}

}

?>