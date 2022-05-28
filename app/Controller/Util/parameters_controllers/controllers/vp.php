<?php

include_once("utils/file_reader.php");
include_once("utils/parameters_utils.php");
include_once("parameter_controller.php");

class ParameterController_VP extends ParameterController {

    public function loadData($project_id, $power_source_id, $datablock_id, $file_type, $scope) {
        if ($file_type == "3P4W" || $file_type == "3P3W") {
            $indicators = array("V1", "V2", "V3");
            $magnitudes = array("V", "V", "V");
            $titleMain = '<b>Phase Voltage</b> for 3 phases';
        } else if ($file_type == "1P3W") {
            $indicators = array("V1", "V2");
            $magnitudes = array("V", "V");
            $titleMain = '<b>Phase Voltage</b> for 2 phases';
        }
        
        $contents = $this->getFilesContentByType($project_id, $power_source_id, $datablock_id, $file_type);
        $content = $contents[$scope];
        $data = PQMFileReader::readParametersInFile($content, $indicators, $magnitudes);        

        $data["analisis"] = array(
            "main" => array(
                "title" => $titleMain,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'V'),
                "events" => array("max", "min"),
                "indicators" => $indicators)          
        );

        return $data;
    }

}

?>
