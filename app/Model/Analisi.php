<?php

App::uses('AppModel', 'Model');

class Analisi extends AppModel {

    public $name = "Analisis";
    
    public $belongsTo = array(
        
        'Project' => array(
            'fields'=>array('id', 'name'),
            'counterCache'=>true
        ),
        
        'PowerSource' => array(
            'fields'=>array('id', 'name')
        ),
        
        'Datafile' => array(
            'fields'=>array('id', 'label')
        ),
        
    );
    
    public function findRandom($limit = 10) {
        return $this->find('all', array('order' => 'random()', 'limit' => $limit));
    }
}

?>