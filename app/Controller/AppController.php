<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $helpers = array(
        'Html' => array(
            'className' => 'EnhancedHtml'
        ), 
        'Form' => array(
            'className' => 'BootstrapForm'
        ),
        'Session', 
        'Js');
    
    public $components = array(
        'Session',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'projects', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'pages', 'action' => 'home'/* , 'home' */),
            'authorize' => array('Controller')
        )
    );

    public function beforeFilter() {
        $this->Auth->allow('display', 'login', 'register', 'authorize', 'logout', 'recover_password', 'search_tags', 'analyse');
    }

    public function isAuthorized($user) {
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        // Default deny
        return false;
    }

    public function index() {
        
    }
    
    
    protected function setErrorMessage($message) {
        $this->Session->setFlash($message, 'error_message');
    }
    
    protected function setWarningMessage($message) {
        $this->Session->setFlash($message, 'warning_message');
    }
    
    protected function setSuccessMessage($message) {
        $this->Session->setFlash($message, 'success_message');
    }

}
