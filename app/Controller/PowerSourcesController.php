<?php

App::uses('Project', 'Model');

class PowerSourcesController extends AppController {

    public $components = array('RequestHandler');

    public function isAuthorized($user) {
        return true;
    }

    public function add($proId) {
        if ($this->request->is('post')) {
            $this->PowerSource->create();

            $powersource = $this->request->data['PowerSource'];
            $this->request->data['PowerSource']['project_id'] = $proId;
            $this->request->data['PowerSource']['isc'] = round((1000 * $powersource["kva"]) / ($powersource["reactance"] / 100 * 1.73 * $powersource["voltage"]), 2);
            if ($this->PowerSource->save($this->request->data)) {
                $devId = $this->PowerSource->getLastInsertID();
                return $this->redirect(array('controller' => 'projects', 'action' => 'view/' . $proId . '/' . $devId));
            }
            $this->setErrorMessage(__('Unable to add this power source.'));
        }

        $proModel = new Project();
        $dbPro = $proModel->findById($proId);
        $pro = $dbPro['Project'];
        $this->set("project", $pro);
    }

    public function edit($proId, $devId) {
        //TODO: Validate paramters

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $powersource = $this->data;
        } else if ($this->request->is('post') || $this->request->is('put')) {
            $powersource = $this->request->data;
        }
        
        $powersource['PowerSource']['isc'] = round((1000 * $powersource['PowerSource']["kva"]) / ($powersource['PowerSource']["reactance"] / 100 * 1.73 * $powersource['PowerSource']["voltage"]), 2);

        if ($this->PowerSource->save($powersource)) {
            if($this->request->is('ajax')) {
                echo json_encode(array('object'=>$powersource['PowerSource']));
                return;
            }
            return $this->redirect(array('controller' => 'projects', 'action' => 'view/' . $proId . '/' . $devId));
        }
        $this->setErrorMessage(__('Unable to update this power source.'));
        if (!$this->request->data['PowerSource']) {
            $this->request->data['PowerSource'] = $powersource['PowerSource'];
        }
    }

    public function remove($proId, $devId) {
        if ($this->PowerSource->delete($devId)) {
            return $this->redirect(array('controller' => 'projects', 'action' => 'view/' . $proId));
        }
        $this->Session->setFlash(__('Unable to delete this power source.'));
    }

}

?>