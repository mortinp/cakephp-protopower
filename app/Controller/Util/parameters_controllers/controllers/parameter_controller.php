<?php

App::uses('Datafile', 'Model');

abstract class ParameterController {

    abstract public function loadData($project_id, $power_source_id, $datablock_code, $file_type, $scope);

    protected function getFilesContentByType($project_id, $power_source_id, $datablock_code, $type) {
        $contents = array();
        
        $dfModel = new Datafile();
        $dbFiles = $dfModel->find('all', array('conditions'=>array('power_source_id'=>$power_source_id, 'datablock_code'=>$datablock_code, 'type'=>$type)));
        foreach ($dbFiles as $f) {
            $file = $f['Datafile'];
            $contents[$file['scope']] = preg_split("/(\r\n|\n|\r)/", $file['content']);
        }

        return $contents;
    }

}

?>
