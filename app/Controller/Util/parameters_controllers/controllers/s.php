<?php

include_once("utils/file_reader.php");
include_once("parameter_controller.php");

class ParameterController_S extends ParameterController {

    public function loadData($project_id, $power_source_id, $datablock_id, $file_type, $scope) {
        if ($file_type == "3P4W" || $file_type == "3P3W") {
            $indicators = array("S1", "S2", "S3");
            $magnitudes = array("KVA", "KVA", "KVA");
            $titleMain = '<b>Apparent power </b> for 3 phases';
            $titleVASys = '<b>System apparent power </b> for 3 phases';
        } else if ($file_type == "1P3W") {
            $indicators = array("S1", "S2");
            $magnitudes = array("KVA", "KVA");
            $titleMain = '<b>Apparent power </b> for 2 phases';
            $titleVASys = '<b>System apparent power </b> for 2 phases';
        }
        
        $indicatorsWithVASys = array_merge($indicators, array('VA_SYS'));
        $magnitudesWithVASys = array_merge($indicators, array('KVA'));

        $contents = $this->getFilesContentByType($project_id, $power_source_id, $datablock_id, $file_type);
        $content = $contents[$scope];
        $data = PQMFileReader::readParametersInFile($content, $indicatorsWithVASys, $magnitudesWithVASys);

        $data["analisis"] = array(
            "main" => array(
                "title" => $titleMain,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'KVA'),
                "events" => array("max", "min"),
                "indicators" => $indicators),
            "system" => array(
                "title" => $titleVASys,
                "chart" => array("type" => "multiDatasetStockChart", 'magnitude'=>'KW'),
                "events" => array("max", "min"),
                "indicators" => array("VA_SYS"))
            );

        return $data;
    }

}

?>
