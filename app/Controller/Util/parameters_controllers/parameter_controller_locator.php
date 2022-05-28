<?php

include_once("controllers/i.php");
include_once("controllers/vl.php");
include_once("controllers/vp.php");
include_once("controllers/p.php");
include_once("controllers/s.php");
include_once("controllers/q.php");
include_once("controllers/pf.php");
include_once("controllers/wsys.php");
include_once("controllers/vasys.php");
include_once("controllers/varsys.php");
include_once("controllers/whsys.php");
include_once("controllers/harmo_1_31.php");
include_once("controllers/harmo_33_49.php");

class ParameterControllerLocator {

	public static function getParameterController($paramType) {
		if($paramType == "I") return new ParameterController_I();
		else if($paramType == "VL") return new ParameterController_VL();
                else if($paramType == "VP") return new ParameterController_VP();
		else if($paramType == "P") return new ParameterController_P();
		else if($paramType == "S") return new ParameterController_S();
		else if($paramType == "Q") return new ParameterController_Q();
		else if($paramType == "PF") return new ParameterController_PF();
		else if($paramType == "W_SYS") return new ParameterController_WSYS();
		else if($paramType == "VA_SYS") return new ParameterController_VASYS();
		else if($paramType == "VAR_SYS") return new ParameterController_VARSYS();
		else if($paramType == "WH_SYS") return new ParameterController_WHSYS();
		else if($paramType == "H(1-31)") return new ParameterController_HARMO_1_31();
                else if($paramType == "H(33-49)") return new ParameterController_HARMO_33_49();
                
		
		// throw new Exception("No controller found for parameter $paramType");
	}
}

?>