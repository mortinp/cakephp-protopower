<?php

App::uses('FormHelper', 'View/Helper');

/**
 * BootstrapFormHelper.
 *
 * Applies styling-rules for Bootstrap 3
 *
 * To use it, just save this file in /app/View/Helper/BootstrapFormHelper.php
 * and add the following code to your AppController:
 *   	public $helpers = array(
 * 		    'Form' => array(
 * 		        'className' => 'BootstrapForm'
 * 	  	  	)
 * 		);
 *
 * @link https://gist.github.com/Suven/6325905
 */
class BootstrapFormHelper extends FormHelper {

    public function create($model = null, $options = array()) {
        
        $defaultOptions = array(
            'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
            ),
        );

        if (!empty($options['inputDefaults'])) {
            $options = array_merge($defaultOptions['inputDefaults'], $options['inputDefaults']);
        } else {
            $options = array_merge($defaultOptions, $options);
        }
        return parent::create($model, $options);
    }    

    public function submit($caption = null, $options = array()) {
        $defaultOptions = array(
            'class' => 'btn btn-primary',
        );
        $options = array_merge($defaultOptions, $options);
        return parent::submit($caption, $options);
    }
    
    public function button($caption = null, $options = array()) {
        $defaultOptions = array(
            'class' => 'btn',
            'type'=>'button',
            'style'=>'display:inline-block'
        );
        $options = array_merge($defaultOptions, $options);
        return parent::submit($caption, $options);
    }
    
    public function input($caption = null, $options = array()) {
        $defaultOptions = array();
        $options = array_merge($defaultOptions, $options);
        return parent::input($caption, $options);
    }
    
    // Remove this function to show the fieldset & language again
    public function inputs($fields = null, $blacklist = null, $options = array()) {
        $options = array_merge(array('fieldset' => false), $options);
        return parent::inputs($fields, $blacklist, $options);
    }
}

?>
