<?php

App::uses('Analisi', 'Model');
App::uses('PowerSource', 'Model');

include_once("utils/file_reader.php");
include_once("utils/parameters_utils.php");
include_once("parameter_controller.php");

//include_once("../../pqm-projects-management/server/pqm_projects_manager.php");

abstract class ParameterController_HARMO extends ParameterController {
    
    protected abstract function getIndicatorsAndMagnitudes();

    public function loadData($project_id, $power_source_id, $datablock_id, $file_type, $scope) {
        $indmag = $this->getIndicatorsAndMagnitudes();
        $indicators = $indmag['indicators'];
        $magnitudes = $indmag['magnitudes'];
        $disabled = $indmag['disabled'];
        $titleMain = 'Odd <b>harmonics ('.$scope.')</b>';
        $titleTHD = '<b>Total harmonics distortion (THD)</b> for odd harmonics (<b>'.$scope.'</b>)';
        
        $indicatorsWithTHD = array('THD-F');foreach ($indicators as $ii) $indicatorsWithTHD[] = $ii;        
        $magnitudesWithTHD = array('%');foreach ($magnitudes as $mm) $magnitudesWithTHD[] = $mm;

        $contents = $this->getFilesContentByType($project_id, $power_source_id, $datablock_id, $file_type);
        $content = $contents[$scope];
        $data = PQMFileReader::readParametersInFile($content, $indicatorsWithTHD, $magnitudesWithTHD);

        // Include normalization data (if 3P4W file exists)
        $thresholdTHD = 100000; // A big value to hide it in case it's not calculated
        $powerContents = $this->getFilesContentByType($project_id, $power_source_id, $datablock_id, "3P4W");
        if (isset($powerContents) && isset($powerContents["ALL"])) {
            $content = $powerContents["ALL"];
            $powerData = PQMFileReader::readParametersInFile($content, array($scope), array("A"));

            // Find power source in DB
            $devModel = new PowerSource();
            $dbDev = $devModel->findById($power_source_id);
            $powersource = $dbDev['PowerSource'];

            $thresholdTHD = $this->calculateThresholdTHD($powersource, $powerData["data"], $scope);
        }

        $data["analisis"] = array(
            "main" => array(
                "title" => $titleMain,
                "chart" => array("type" => "multiGraphStockChart", "magnitude"=>'%', "options" => array("disabled" => $disabled)),
                "events" => array(),
                "indicators" => $indicators),
            "THD" => array(
                "title" => $titleTHD,
                "chart" => array("type" => "multiDatasetStockChart", "magnitude"=>'%'),
                "events" => array("max", "min"),
                "indicators" => array("THD-F"),
                "normalization" => array("type" => "threshold-line", "value" => $thresholdTHD, "label" => "THD limit: ".$thresholdTHD."%")));

        //$data["reports"] = $powersource["Isc"];

        return $data;
    }

    private function calculateThresholdTHD($powersourceData, $indicatorData, $indicator) {
        if ($indicator == "I1" || $indicator == "I2" || $indicator == "I3")
            return $this->getThresholdTHD_I($powersourceData, $indicatorData, $indicator);
        else if ($indicator == "V1" || $indicator == "V2" || $indicator == "V3")
            return $this->getThresholdTHD_V($powersourceData, $indicatorData, $indicator);
    }

    private function getThresholdTHD_I($powersourceData, $indicatorData, $indicator) {
        $Isc = $powersourceData["isc"];

        $Irms = 0;
        foreach ($indicatorData as $k => $ind) {
            $Irms += floatval($ind[$indicator]);
        }
        $Irms /= count($indicatorData);

        $rate = $Isc / $Irms;
        if ($rate < 20)
            return 5;
        else if ($rate >= 20 && $rate < 50)
            return 8;
        else if ($rate >= 50 && $rate < 100)
            return 12;
        else if ($rate >= 100 && $rate < 1000)
            return 15;
        else if ($rate >= 1000)
            return 20;

        return -1;
    }

    private function getThresholdTHD_V($powersourceData, $indicatorData, $indicator) {
        $rate = $powersourceData["voltage"] / 1000;
        if ($rate <= 69)
            return 5;
        else if ($rate > 69 && $rate <= 161)
            return 2.5;
        else if ($rate > 161)
            return 1.5;

        return -1;
    }
}

?>
