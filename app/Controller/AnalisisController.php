<?php

App::uses('Datablock', 'Model');
App::uses('Project', 'Model');
include_once("Util/parameters_reader.php");

class AnalisisController extends AppController {
    
    public $uses = array('Project', 'PowerSource', 'Datablock', 'Datafile', 'Analisi'/*, 'AnalisisUserTag'*/);

    public function isAuthorized($user) {
        return true;
    }

    public function analyse($proId, $devId, $datablockCode, $fileTag = null, $param = null) {
        $noPro = !isset($proId) || $proId == null;
        $noDev = !isset($devId) || $devId == null;
        $noDb= !isset($datablockCode) || $datablockCode == null;
        if($noPro || $noDev || $noDb) throw new NotFoundException();
        
        $data = array();
        
        // Find project
        $dbPro = $this->Project->findById($proId); if($dbPro == null) throw new NotFoundException();
        $data['project'] = $dbPro['Project'];
        
        // Find power source
        $dbDev = $this->PowerSource->findById($devId); if($dbDev == null) throw new NotFoundException();
        $data['powersource'] = $dbDev['PowerSource'];
        
        // Find datablock
        $dbDb = $this->Datablock->find('first', array('conditions'=>array('power_source_id'=>$devId, 'code'=>$datablockCode))); if($dbDb == null) throw new NotFoundException();
        $data['datablock'] = $dbDb['Datablock'];
        
        // Find files
        $dbFiles = $this->Datafile->find('all', array('conditions'=>array('power_source_id'=>$devId, 'datablock_code'=>$datablockCode)));
        $data['files'] = array();
        foreach ($dbFiles as $f) {
            $data['files'][] = $f['Datafile'];
        }

        // Find current file according to requested url
        $files = $data['files'];
        if (!empty($files)) {
            $currentFile = null;
            if ($fileTag == null) {
                $currentFile = $files[0];
                $fileTag = $currentFile['tag'];
            } else {
                foreach ($files as $f)
                    if ($f['label'] == $fileTag) {
                        $currentFile = $f;
                        break;
                    }
            }
            if ($param == null)
                $param = $currentFile['default'];
            
            // Verificar que exista el analisis, y si no, crearlo
            $dbAnalisis = $this->Analisi->find('first', array('conditions'=>array('datafile_id'=>$currentFile['id'], 'param'=>$param)));
            if(empty($dbAnalisis)) {
                $analisis = array('project_id'=>$proId, 
                                  'power_source_id'=>$devId, 
                                  'datablock_code'=>$datablockCode, 
                                  'datafile_id'=>$currentFile['id'], 
                                  'param'=>$param, 
                                  'tags'=>null);
                $this->Analisi->save(array('Analisi'=>$analisis));
                $analisis['id'] =  $this->Analisi->getLastInsertID();
            } else $analisis = $dbAnalisis['Analisi'];
            
            // Set file tags
            $tags = array();
            if($analisis['tags'] != null) $tags = array_map('trim',explode(',', $analisis['tags']));
            $currentFile['user_tags'] = $tags;
            
            // Create analisis context
            $reader = new ParameterReader();
            $analisisContext = $reader->getResponseForParameter($proId, $devId, $datablockCode, $currentFile['type'], $param, $currentFile['scope']);

            // Output
            $this->set('project', $data['project']);
            $this->set('powersource', $data['powersource']);
            $this->set('datablock', $data['datablock']);
            $this->set('file', $currentFile);
            $this->set('param', $param);
            $this->set('datablockFiles', $files);
            $this->set('selectors', $analisisContext['selectors']);
            $this->set('pretty_selectors', $analisisContext['pretty_selectors']);
            $this->set('fileData', $analisisContext['data']);
            $this->set('analisis', $analisis);
            $this->set('analisisContext', $analisisContext);
            $this->set('isOwner', $this->Project->isOwnedBy($proId, $this->Auth->user('id')));
            
            $this->set('disqusId', $proId.'.'.$devId.'.'.$datablockCode.'.'.$fileTag.'.'.$param);
            //print_r( $analisis);
        }
    }

    public function set_file_tags($analisis_id, $param) {
        $this->autoRender = false;
        
        $tags = implode(',', $_POST['value']);
        
        $dbAn = $this->Analisi->findById($analisis_id);
        $dbAn['Analisi']['tags'] = $tags; 
        
        $this->Analisi->save($dbAn);
    }
    
    public function search_tags() {
        $strTags = $_GET['tags'];
        $tags = array_map('trim',explode(',', $strTags));
        
        $analises = $this->Analisi->find('all'/*, array('conditions'=>array('tags' =>'IS NOT NULL'))*/);
        
        $matches = array();
        foreach ($analises as $a) {
            $user_tags = explode(',', $a['Analisi']['tags']);
            
            $tagMatch = array_intersect($tags, $user_tags);
            if(!empty ($tagMatch)) {
                //$file = $this->Datafile->findById($a['datafile_id']);
                //$file = $file['Datafile'];
                $a['matches'] = $tagMatch;
                $matches[] = $a/*array('project_id'=>$a['project_id'],
                                   'power_source_id'=>$a['power_source_id'],
                                   'datablock_code'=>$a['datablock_code'],
                                   'label'=>$file['label'],
                                   'param'=>$a['param'],
                                   'matches'=>$tagMatch)*/;
            }
                        
        }

        $this->set('tags', $tags);
        $this->set('results', $matches);
    }
}

?>