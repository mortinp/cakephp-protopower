<?php

include_once("utils/file_reader.php");
include_once("utils/parameters_utils.php");
include_once("parameter_controller.php");

class ParameterController_VL extends ParameterController {

    public function loadData($project_id, $power_source_id, $datablock_id, $file_type, $scope) {
        if ($file_type == "3P4W" || $file_type == "3P3W") {
            $indicators = array("V12", "V23", "V31");
            $magnitudes = array("V", "V", "V");
            $titleMain = '<b>Voltage</b> for 3 phases';
            $titleUnbalance = '<b>Voltage unbalance</b> for 3 phases';
        } else if ($file_type == "1P3W") {
            $indicators = array("V1", "V2");
            $magnitudes = array("V", "V");
            $titleMain = '<b>Voltage</b> for 2 phases';
            $titleUnbalance = '<b>Voltage unbalance</b> for 2 phases';
        }
        $titleTempInc = 'Motors <b>temperature rise</b> due to voltage unbalance';
        $titleLifeRed = "Motors <b>life reduction</b> due to temperature rise";
        $titleLifeRedLibby = "Motors <b>life reduction</b> due to temperature rise <span class='text-warning'>(classes <em>A</em> and <em>B</em> only)</span>";

        $contents = $this->getFilesContentByType($project_id, $power_source_id, $datablock_id, $file_type);
        $content = $contents[$scope];
        $data = PQMFileReader::readParametersInFile($content, $indicators, $magnitudes);

        // Add 'unbalance' and 'temperature' entry to the data objects
        // TODO: [OPTIMIZE] Esto se puede optimizar haciendo que las dos cosas se calculen a la misma vez, en usa sola pasada. 
        ParametersUtils::addUnbalanceEntry($data["data"], $data["data"], $indicators);
        ParametersUtils::addTemperatureIncreaseEntry($data["data"], $data["data"]);
        ParametersUtils::addMotorLifeDecreaseEntry($data["data"], $data["data"]);

        $data["analisis"] = array(
            "main" => array(
                "title" => $titleMain,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'V'),
                "events" => array("max", "min"),
                "indicators" => $indicators),
            "unbalance" => array(
                "title" => $titleUnbalance,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'%'),
                "events" => array("max", "min"),
                "indicators" => array("unbalance")),
            "temperature-rise" => array(
                'name'=>'Temperature Rise',
                'title'=>$titleTempInc,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'%'),
                "events" => array("max", "min"),
                "indicators" => array("temperature-increase")),
            "life-reduction" => array(
                'name'=>'Life Reduction',
                'title'=>$titleLifeRed,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'%'),
                "events" => array("max", "min"),
                "indicators" => array(                    
                    array('title'=>'Class B insulation', 'field'=>"life-reduction-b"),
                    array('title'=>'Class A insulation', 'field'=>"life-reduction-a"),
                    array('title'=>'Class F insulation', 'field'=>"life-reduction-f"), 
                    array('title'=>'Class H insulation', 'field'=>"life-reduction-h"))),
            "life-reduction-libby" => array(
                'name'=>'Life Reduction (Libby)',
                'title'=>$titleLifeRedLibby,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'%'),
                "events" => array("max", "min"),
                "indicators" => array(
                    array('title'=>'Class B insulation', 'field'=>"life-reduction-libby-b"), 
                    array('title'=>'Class A insulation', 'field'=>"life-reduction-libby-a")))            
        );

        return $data;
    }

}

?>
