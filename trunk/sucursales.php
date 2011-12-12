<?php

	include(dirname(__FILE__)."/init.php");
	$GLOBALS['ISC_CLASS_SUCURSALES'] = GetClass('ISC_SUCURSALES');
	$GLOBALS['ISC_CLASS_SUCURSALES']->HandlePage();