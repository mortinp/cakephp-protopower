<?php

include_once('Util/upload_file.php');

class DatablocksController extends AppController {
    
    public $uses = array('Datablock', 'Datafile');

    public function isAuthorized($user) {
        return true;
    }

    public function upload_file() {
        $this->autoRender = false;

        $project_id = $_POST['project_id'];
        $power_source_id = $_POST['power_source_id'];
        $datablock_code = $_POST['datablock_code'];
        $datablock_index = $_POST['datablock_index'];
        if(isset($_POST['datablock_id'])) $datablock_id = $_POST['datablock_id'];

        // Handle file upload
        $uploader = new FileUploader(array('txt', 'csv', 'xls'), 2 * 1024 * 1024);
        $result = $uploader->handleUpload();

        if (isset($result["success"])) {
            $file = $result["file"];

            $existingFiles = $this->Datafile->find('all', array('conditions'=>array('power_source_id'=>$power_source_id, 'datablock_code'=>$datablock_code)));
            if (empty($existingFiles)) { // If it's the first file, create a new datablock
                $newDatablock = array('code' => $datablock_code, 'power_source_id'=>$power_source_id);
                $this->Datablock->save(array('Datablock'=>$newDatablock));
                $datablock_id = $this->Datablock->getLastInsertID();
            } else {
                // Validate file
                foreach ($existingFiles as $f) {
                    $f = $f['Datafile'];                    
                    
                    if ($f['type'] == $file['type'] && $f['scope'] == $file['scope']) {
                        throw new ForbiddenException('The file ' . $file['label'] . ' already exists in this datablock.');
                    }
                    if (in_array($file['type'], array('3P4W', '1P3W', '3P3W')) && in_array($f['type'], array('3P4W', '1P3W', '3P3W'))) {
                        throw new ForbiddenException('A file for analysing POWER already exists in this datablock: '.$f['label'].'. Upload this file to a different datablock.');
                    }
                    if(($file['scope'] == 'I3' || $file['scope'] == 'V3') && $f['type'] == '1P3W') {
                        throw new ForbiddenException('You cannot upload HARMO files for phase 3 when you have a 1P3W file in this datablock.');
                    }
                    if($file['type'] == '1P3W' && ($f['scope'] == 'I3' || $f['scope'] == 'V3')) {
                        throw new ForbiddenException('You cannot upload a 1P3W file when you have HARMO files for phase 3 in this datablock.');
                    }
                }
            }
            
            if(!isset($datablock_id) || $datablock_id == null) {
                $db = $this->Datablock->find('first', array('conditions'=>array('power_source_id'=>$power_source_id, 'code'=>$datablock_code)));
                $datablock_id = $db['Datablock']['id'];
            }

            //$file['project_id'] = $project_id;
            $file['power_source_id'] = $power_source_id;
            $file['datablock_code'] = $datablock_code;
            $file['datablock_id'] = $datablock_id;
            $this->Datafile->save(array('Datafile'=>$file));

            $file_id = $this->Datafile->getLastInsertID();
            echo json_encode(array(
                'file_id' => $file_id,
                'file_info' => $file['label'],
                'file_type' => $file['type'],
                'file_tag' => $file['label']));
        } else {
            throw new InternalErrorException('There was a problem uploading the file. Try again.');
        }
    }

    public function remove_file($fileId) {
        $this->autoRender = false;        

        $project_id = $_POST['project_id'];
        $power_source_id = $_POST['power_source_id'];
        $datablock_id = $_POST['datablock_id'];
        $datablock_code = $_POST['datablock_code'];
        $datablock_index = $_POST['datablock_index'];

        $existingFiles = $this->Datafile->find('all', array('conditions'=>array('power_source_id'=>$power_source_id, 'datablock_code'=>$datablock_code)));
        
        if (count($existingFiles) == 1) // If it's the last file, remove the datablock
            $this->Datablock->delete($datablock_id);
        else
            $this->Datafile->delete($fileId);
    }
}

?>