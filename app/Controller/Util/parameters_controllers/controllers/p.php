<?php

include_once("utils/file_reader.php");
include_once("utils/parameters_utils.php");
include_once("parameter_controller.php");

class ParameterController_P extends ParameterController {

    public function loadData($project_id, $power_source_id, $datablock_id, $file_type, $scope) {
        if ($file_type == "3P4W" || $file_type == "3P3W") {
            $indicators = array("P1", "P2", "P3");
            $magnitudes = array("KW", "KW", "KW");
            $titleMain = '<b>Power</b> for 3 phases';
            $titleUnbalance = '<b>Power unbalance</b> for 3 phases';
            $titleWSys = '<b>System power</b> for 3 phases';
        } else if ($file_type == "1P3W") {
            $indicators = array("P1", "P2");
            $magnitudes = array("KW", "KW");
            $titleMain = '<b>Power</b> for 2 phases';
            $titleUnbalance = '<b>Power unbalance</b> for 2 phases';
            $titleWSys = '<b>System power</b> for 2 phases';
        }
        
        $indicatorsWithWSys = array_merge($indicators, array('W_SYS'));
        $magnitudesWithWSys = array_merge($indicators, array('KW'));

        $contents = $this->getFilesContentByType($project_id, $power_source_id, $datablock_id, $file_type);
        $content = $contents[$scope];
        $data = PQMFileReader::readParametersInFile($content, $indicatorsWithWSys, $magnitudesWithWSys);

        // Add 'unbalance' entry to the data objects
        ParametersUtils::addUnbalanceEntry($data["data"], $data["data"], $indicators);

        $data["analisis"] = array(
            "main" => array(
                "title" => $titleMain,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'KW'),
                "events" => array("max", "min"),
                "indicators" => $indicators),
            "system" => array(
                "title" => $titleWSys,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'KW'),
                "events" => array("max", "min"),
                "indicators" => array("W_SYS")),
            "unbalance" => array(
                "title" => $titleUnbalance,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'%'),
                "events" => array("max", "min"),
                "indicators" => array("unbalance"))
            );

        return $data;
    }

}

?>
