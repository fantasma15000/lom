<?php

CLASS ISC_LOGIN_PANEL extends PANEL{

	public function SetPanelSettings(){
	
		$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
		$customerData = $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerDataByToken();
		
		if ($customerData['customerid'] != 0){
			$GLOBALS['ShowLogin'] = "None";
			$GLOBALS['ShowLogOut'] = "Block";
			$fullCustomerName = $customerData['custconfirstname']." ".$customerData['custconlastname'];
			$GLOBALS['UserNames'] = $fullCustomerName;
			$GLOBALS['ShowSalute'] = "Block";
		}else{
			$GLOBALS['ShowLogOut'] = "None";
			$GLOBALS['ShowLogin'] = "Block";
			$GLOBALS['ShowSalute'] = "none";
		}
					
	}
}


?>