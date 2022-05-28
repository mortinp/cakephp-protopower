<?php

include_once("utils/file_reader.php");
include_once("parameter_controller.php");

class ParameterController_WHSYS extends ParameterController {

    public function loadData($project_id, $power_source_id, $datablock_id, $file_type, $scope) {
        $indicators = array("WH_SYS");
        $magnitudes = array("KWH|MWH(1000)");
        $titleMain = '<b>Energy Consumption</b> for 3 phases';

        $contents = $this->getFilesContentByType($project_id, $power_source_id, $datablock_id, $file_type);
        $content = $contents[$scope];
        $data = PQMFileReader::readParametersInFile($content, $indicators, $magnitudes);

        $data["analisis"] = array(
            "main" => array(
                'title'=>$titleMain,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'KWH', "options" => array("fillAlphas" => 0.3)),
                "events" => array(),
                "indicators" => $indicators));

        return $data;
    }

}

?>
