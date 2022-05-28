<?php

App::uses('AppModel', 'Model');

class PowerSource extends AppModel {
    
    public $order = 'PowerSource.id';
    
    public $belongsTo = array(
        
        'Project' => array(
            'fields'=>array('id', 'name'),
            'counterCache'=>true
        )        
    );

    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A name is required'
            )
        ),
        /*'kva' => array(
            'rule'=>array('decimal', 2)
        ),*/
        /*'kva' => array(
                'numeric' => array(
                'rule' => 'numeric',
                'message' => 'Numbers only'
            )
        ),*/
        'kva' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'KVA is required'
            )
        ),
        'reactance' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Reactance is required'
            )
        ),
        'voltage' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Voltage is required'
            )
        )
    );
}

?>