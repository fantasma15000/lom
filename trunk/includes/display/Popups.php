<?php
	CLASS ISC_POPUPS_PANEL extends PANEL
	{
		public function SetPanelSettings()
		{
		
		    if ($_COOKIE['HideUpInicio4']=="1"){
				$GLOBALS['ShowPopUp']=0;
			}else{
				$GLOBALS['ShowPopUp']=1;
			}

			setcookie("HideUpInicio4", "1", time()+604800);
				
		
		}
	}
	
	
?>