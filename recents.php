<?php

	include(dirname(__FILE__)."/init.php");
	$GLOBALS['ISC_CLASS_CATEGORY'] = GetClass('ISC_RECENTS');
	$GLOBALS['ISC_CLASS_CATEGORY']->HandlePage();