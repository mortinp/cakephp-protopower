<?php

include_once("utils/file_reader.php");
include_once("utils/parameters_utils.php");
include_once("parameter_controller.php");

class ParameterController_Q extends ParameterController {

    public function loadData($project_id, $power_source_id, $datablock_id, $file_type, $scope) {
        if ($file_type == "3P4W" || $file_type == "3P3W") {
            $indicators = array("Q1", "Q2", "Q3");
            $magnitudes = array("KVAR", "KVAR", "KVAR");
            $titleMain = '<b>Reactive power</b> for 3 phases';
            $titleUnbalance = '<b>Reactive power unbalance</b> for 3 phases';
            $titleVARSys = '<b>System reactive power</b> for 3 phases';
        } else if ($file_type == "1P3W") {
            $indicators = array("Q1", "Q2");
            $magnitudes = array("KVAR", "KVAR");
            $titleMain = '<b>Reactive  power</b> for 2 phases';
            $titleUnbalance = '<b>Reactive  power unbalance</b> for 2 phases';
            $titleVARSys = '<b>System reactive power</b> for 2 phases';
        }
        
        $indicatorsWithVARSys = array_merge($indicators, array('VAR_SYS'));
        $magnitudesWithVARSys = array_merge($indicators, array('KVA'));

        $contents = $this->getFilesContentByType($project_id, $power_source_id, $datablock_id, $file_type);
        $content = $contents[$scope];
        $data = PQMFileReader::readParametersInFile($content, $indicatorsWithVARSys, $magnitudesWithVARSys);

        // Add 'unbalance' entry to the data objects
        ParametersUtils::addUnbalanceEntry($data["data"], $data["data"], $indicators);

        $data["analisis"] = array(
            "main" => array(
                "title" => $titleMain,
                "chart" => array("type" => "multiDatasetStockChart", "magnitude"=>'KVAR'),
                "events" => array("max", "min"),
                "indicators" => $indicators),
            "system" => array(
                "title" => $titleVARSys,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'KW'),
                "events" => array("max", "min"),
                "indicators" => array("VAR_SYS")),
            "unbalance" => array(
                "title" => $titleUnbalance,
                "chart" => array("type" => "multiDatasetStockChart", "magnitude"=>'%'),
                "events" => array("max", "min"),
                "indicators" => array("unbalance"))
            );

        return $data;
    }

}

?>
