<?php

class ProjectsController extends AppController {
    
    public $uses = array('Project', 'PowerSource', 'Datablock', 'Datafile');
    
    public function isAuthorized($user) {
        if (in_array($this->action, array('index', 'add'))) {
            return true;
        }

        if (in_array($this->action, array('view', 'edit', 'remove'))) {
            $id = $this->request->params['pass'][0];
            if ($this->Project->isOwnedBy($id, $user['id'])) {
                return true;
            }
        }
        return parent::isAuthorized($user);
    }

    public function index() {
        $dbPros = $this->Project->find('all', array('conditions' => array('user_id' => $this->Auth->user('id'))));
        $projects = array();
        foreach ($dbPros as $pro) {
            $projects[] = $pro['Project'];
        }
        
        $this->set('projects', $projects);
    }

    public function view($id, $dev_id = null) {
        // TODO: Verify valid project id
        //$this->layout = "project";

        $dbPro = $this->Project->findById($id);
        $pro = $dbPro['Project'];
        
        //******HACK (find all power sources)********
        $dbPowerSources = $this->PowerSource->findAllByProjectId($pro['id']);
        $pro['powersources'] = array();
        foreach ($dbPowerSources as $dev) {
            $pro['powersources'][] = $dev['PowerSource'];
        }
        //*************
        
        $this->set("project", $pro);

        if (isset($dev_id) && $dev_id != null) {
            $powersources = $pro["powersources"];
            foreach ($powersources as $d)
                if ($d["id"] == $dev_id) {
                    $currentPowerSource = $d;
                    break;
                }
        } else if (isset($pro['powersources']) && !empty($pro['powersources'])) {
            $currentPowerSource = $pro['powersources'][0]; // First power source
            $dev_id = $currentPowerSource['id'];
        }
        
        if(isset($currentPowerSource)) {
            //****HACK*******
            $dbDatablocks = $this->Datablock->findAllByPowerSourceId($currentPowerSource['id']);
            $currentPowerSource['datablocks'] = array();
            foreach ($dbDatablocks as $db) {
                $db = $db['Datablock'];
                $dbFiles = $this->Datafile->find('all', array('conditions'=>array('power_source_id'=>$dev_id, 'datablock_code'=>$db['code'])));
                $db['files'] = array();
                foreach($dbFiles as $f) {
                    unset($f['Datafile']['content']); // Remove content from file
                    $db['files'][] = $f['Datafile'];
                }
                $currentPowerSource['datablocks'][] = $db;
            }
            //**********
            
            $this->request->data['PowerSource'] = $currentPowerSource;
            $this->request->data['Project'] = $pro;
        }
    }

    public function add() {
        /*if ($this->request->is('post')) {
            $this->Project->create();

            $this->request->data['Project']['user_id'] = $this->Auth->user('id');
            if ($this->Project->save($this->request->data)) {
                $id = $this->Project->getLastInsertID();
                
                return $this->redirect(array('action' => 'view/' . $id));
            }
            $this->setErrorMessage(__('Unable to add this project.'));
        }*/
        
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $project = $this->data;
        } else if ($this->request->is('post') || $this->request->is('put')) {
            $project = $this->request->data;
        }
        
        $methodOK = $this->request->is('ajax') || $this->request->is('post') || $this->request->is('put');
        
        if ($methodOK) {
            $this->Project->create();
            
            $project['Project']['user_id'] = $this->Auth->user('id');
            if ($this->Project->save($project)) {
                $id = $this->Project->getLastInsertID();
                $project['Project']['id'] = $id;
                
                if($this->request->is('ajax')) {
                    echo json_encode(array('object'=>$project['Project']));
                    return;
                }
                return $this->redirect(array('action' => 'view/' . $id));
            }
            $this->Session->setFlash(__('The project could not be saved. Please, try again.'));
        }
    }

    public function edit($proId) {
        //TODO: Validate paramters
        
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $project = $this->data;
        } else if ($this->request->is('post') || $this->request->is('put')) {
            $project = $this->request->data;
        }

        $editing = $this->request->is('ajax') || $this->request->is('post') || $this->request->is('put');
        if($editing) {
            if ($this->Project->save($project)) {
                if($this->request->is('ajax')) {
                    echo json_encode(array('object'=>$project['Project']));
                    return;
                }
                return $this->redirect(array('action' => 'index'));
            }
            $this->setErrorMessage(__('Unable to update this project.'));
        }
        
        $project = $this->Project->findById($proId);
        if (!$this->request->data) {
            $this->request->data['Project'] = $project['Project'];
        }
    }

    public function remove($proId) {
        if ($this->Project->delete($proId)) {
            return $this->redirect(array('action' => 'index'));
        }
        $this->setErrorMessage(__('Unable to delete this project.'));
    }
}

?>