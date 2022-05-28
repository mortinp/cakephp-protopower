<?php
include_once("harmo.php");

class ParameterController_HARMO_1_31 extends ParameterController_HARMO {
    protected function getIndicatorsAndMagnitudes() {
        return array('indicators'=>array("1", "3", "5", "7", "9", "11", "13", "15", "17", "19", "21", "23", "25", "27", "29", "31"),
                     'magnitudes'=>array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""),
                     'disabled'=>array(0, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16));
    }
    
}

?>
