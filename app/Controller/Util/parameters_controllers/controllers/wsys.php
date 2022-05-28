<?php

include_once("utils/file_reader.php");
include_once("parameter_controller.php");

class ParameterController_WSYS extends ParameterController {

    public function loadData($project_id, $power_source_id, $datablock_id, $file_type, $scope) {
        $indicators = array("W_SYS");
        $magnitudes = array("KW");
        $titleMain = '<b>W_SYS</b> for 3 phases';

        $contents = $this->getFilesContentByType($project_id, $power_source_id, $datablock_id, $file_type);
        $content = $contents[$scope];
        $data = PQMFileReader::readParametersInFile($content, $indicators, $magnitudes);

        $data["analisis"] = array(
            "main" => array(
                'title'=>$titleMain,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'KW'),
                "events" => array("max", "min"),
                "indicators" => $indicators));

        return $data;
    }

}

?>
