<?php

include_once("parameters_controllers/parameter_controller_locator.php");

class ParameterReader {

    public function getResponseForParameter($project_id, $power_source_id, $datablock_code, $file_type, $param, $scope) {
        $controller = ParameterControllerLocator::getParameterController($param);
        $fileData = $controller->loadData($project_id, $power_source_id, $datablock_code, $file_type, $scope);

        $options = $fileData["options"];
        if ($options["TYPE"] == "3P4W" || $options["TYPE"] == "1P3W") {
            $response["selectors"] = array("I", "VP", "VL", "P", "S", "Q", "PF", /*"W_SYS",*/ /*"VA_SYS",*/ /*array("label" => "KVAR", "param" => "VAR_SYS"),*/ array("label" => "E", "param" => "WH_SYS"));
            $response["pretty_selectors"] = array("Current", "Phase Voltage", "Line Voltage", "Working Power", "Apparent Power", "Reactive Power", "Power Factor", /*"W_SYS",*/ /*"VA_SYS",*/ /*"VAR_SYS",*/ "Energy Consumption");
        } else if ($options["TYPE"] == "3P3W") {
            $response["selectors"] = array("I", "VP", "VL", "PF", /*"W_SYS",*/ /*"VA_SYS",*/ /*array("label" => "KVAR", "param" => "VAR_SYS"),*/ "WH_SYS");
            $response["pretty_selectors"] = array("Current", "Phase Voltage", "Voltage", "Power Factor", /*"W_SYS",*/ /*"VA_SYS",*/ /*"VAR_SYS",*/ "Energy Consumption");
        } else if ($options["TYPE"] == "HARMO") {
            $response["selectors"] = array("H(1-31)", "H(33-49)");
            $response["pretty_selectors"] = array("Odd Harmonics from 1 to 31", "Odd Harmonics from 33 to 49");
        }

        $response["options"] = $options;
        $response["data"] = $fileData["data"];
        $response["analisis"] = $fileData["analisis"];
        if (isset($fileData["reports"]))
            $response["reports"] = $fileData["reports"];
        return $response;
    }

    /* private function getConfigurations($type) {
      if($type == "I") {
      $analisis = array("main"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("I1", "I2", "I3")),
      "unbalance"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("unbalance")));
      } else if($type == "V"){
      $analisis = array("main"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("V12", "V23", "V31")),
      "unbalance"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("unbalance")));
      } else if($type == "P") {
      $analisis = array("main"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("P1", "P2", "P3")),
      "unbalance"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("unbalance")));
      } else if($type == "S") {
      $analisis = array("main"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("S1", "S2", "S3")));
      } else if($type == "PF") {
      $analisis = array("main"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("PF_SYS", "PFH_SYS", "PF_SYS_NO_HARMO")));
      } else if($type == "W_SYS") {
      $analisis = array("main"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("W_SYS")));
      } else if($type == "VA_SYS") {
      $analisis = array("main"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("VA_SYS")));
      } else if($type == "WH_SYS") {
      $analisis = array("main"=>array("chart"=>"multiDatasetStockChart", "events"=>array(), "indicators"=>array("WH_SYS")));
      } else if($type == "H") {
      $analisis = array("main"=>array("chart"=>"multiGraphSerialChart", "events"=>array(), "indicators"=>array("1", "3", "5", "7", "9", "11", "13", "15")),
      "THD"=>array("chart"=>"multiDatasetStockChart", "events"=>array("max", "min"), "indicators"=>array("THD-F")));
      }

      return array("analisis"=>$analisis,);
      } */
}

?>
