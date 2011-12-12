<?php

	include(dirname(__FILE__)."/init.php");
	$GLOBALS['ISC_CLASS_MERCADOPAGO'] = GetClass('ISC_MERCADOPAGO');
	$GLOBALS['ISC_CLASS_MERCADOPAGO']->HandleOffline();