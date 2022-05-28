<?php
App::uses('AppModel', 'Model');
class Project extends AppModel {
    
    public $order = 'id';

    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A name is required'
            )
        ),
        'description' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A description is required'
            )
        )
    );

    public function isOwnedBy($id, $user_id) {
        return $this->field('id', array('id' => $id, 'user_id' => $user_id)) == $id;
    }

    public function findProjectsByOwner($user_id) {
        return $this->getCollection('projects')->find(array("user_id" => $user_id));
    }

    /*public function insertProject($project) {
        $OK = true;
        try {
            $this->getCollection('projects')->insert($project);
        } catch (MongoException $e) {
            $OK = false;
        }
        return $OK;
    }

    public function updateProject($id, $newProject) {
        $OK = true;
        try {
            $this->getCollection('projects')->update(array("_id" => new MongoId($id)), array('$set' => $newProject));
        } catch (MongoException $e) {
            $OK = false;
        }
        return $OK;
    }

    public function removeProject($id) {
        $OK = true;
        try {
            $this->getCollection('projects')->remove(array("_id" => new MongoId($id)));
        } catch (MongoException $e) {
            $OK = false;
        }
        return $OK;
    }*/

}

?>