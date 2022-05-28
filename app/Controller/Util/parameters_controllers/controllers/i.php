<?php

include_once("utils/file_reader.php");
include_once("utils/parameters_utils.php");
include_once("parameter_controller.php");

class ParameterController_I extends ParameterController {

    public function loadData($project_id, $power_source_id, $datablock_code, $file_type, $scope) {
        if ($file_type == "3P4W" || $file_type == "3P3W") {
            $indicators = array("I1", "I2", "I3");
            $magnitudes = array("A", "A", "A");
            $titleMain = '<b>Current</b> for 3 phases';
            $titleUnbalance = '<b>Current unbalance</b> for 3 phases';
        } else if ($file_type == "1P3W") {
            $indicators = array("I1", "I2");
            $magnitudes = array("A", "A");
            $titleMain = '<b>Current</b> for 2 phases';
            $titleUnbalance = '<b>Current unbalance</b> for 2 phases';
        }

        $contents = $this->getFilesContentByType($project_id, $power_source_id, $datablock_code, $file_type);
        $content = $contents[$scope];
        $data = PQMFileReader::readParametersInFile($content, $indicators, $magnitudes);

        // Add 'unbalance' entry to the data objects
        ParametersUtils::addUnbalanceEntry($data["data"], $data["data"], $indicators);

        $data["analisis"] = array(
            "main" => array(
                "title" => $titleMain,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude' => 'A'),
                "events" => array("max", "min"),
                "indicators" => $indicators),
            "unbalance" => array(
                "title" => $titleUnbalance,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude' => '%'),
                "events" => array("max", "min"),
                "indicators" => array("unbalance")));

        return $data;
    }

}

?>
