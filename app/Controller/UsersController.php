<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class UsersController extends AppController {
    
    public $uses = array('User', 'PendingUser');

    //public $layout = 'users';

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('register');
        $this->Auth->allow('register_welcome');
        //$this->Auth->allow('add');
    }

    public function isAuthorized($user) {
        if (in_array($this->action, array('register_welcome', 'password_changed', 'profile'))) { // Allow these actions for the logged-in user
            return true;
        }
        return parent::isAuthorized($user);
    }

    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirect());
            }
            $this->setErrorMessage(__('Invalid username or password, try again'));
        }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    public function register() {
        if ($this->request->is('post')) {
            if($this->User->loginExists($this->request->data['User']['username'])) {
                $this->setErrorMessage(__('This email is already in use. Please use a different email.'));
                return;
            }
            if($this->PendingUser->loginExists($this->request->data['User']['username'])) {
                //throw new ForbiddenException(__('[PENDING] This email is already registered in the app. Please authorize your account using the link we sent to your email.'));
                $this->setErrorMessage(__('This email is already registered in the app. Please authorize your account using the link we sent to your email.'));
                return;
            }
            
            $this->PendingUser->create();
            
            $activation_id = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1).substr(md5(time()),1);
            $this->request->data['User']['activation_id'] =  $activation_id;
            if ($this->PendingUser->save($this->request->data['User'])) {
                /*// Send email and redirect to a welcome page
                $Email = new CakeEmail('desoft');
                $Email->template('welcome')
                ->viewVars(array('user_id' => $activation_id))
                ->emailFormat('html')
                ->to($this->request->data['User']['username'])
                ->subject('Your authorization link')
                ->send();

                //return $this->redirect(array('controller' => 'users', 'action' => 'register_welcome'));
                return $this->render('register_welcome');*/
                return $this->authorize($activation_id);
            }
            $this->setErrorMessage(__('Unable to register this user.'));
        }
    }

    /*public function register_welcome() {
        // Show view only
    }*/

    public function authorize($activation_id) {
        $pending_user = $this->PendingUser->find('first', array('conditions'=>array('activation_id'=>$activation_id)));
        if($pending_user != null) {
            $id = $pending_user['PendingUser']['id'];
            $pending_user['PendingUser']['id'] = null; // Let user create its own id
            $this->User->save(array('User' => $pending_user['PendingUser']));
            $this->PendingUser->delete($id);
            $this->setSuccessMessage(__('You have been authorized, login now.'));

            return $this->redirect(array('controller' => 'users', 'action' => 'login'));
        }
        
        $this->setErrorMessage("There was an error registering this user, or the link you are using has expired (ex. you already used this link)");
    }
    
    public function recover_password() {
        if ($this->request->is('post')) {
            
            $user = $this->User->find('first', array('conditions'=>array('username'=>$this->request->data['User']['username'])));
            
            // TODO: Verificar existencia de usuario
            if($user == null || empty ($user)) {
                $this->setErrorMessage('This email does not belong to any user.');
                return;
            }
            
            
            $newPass = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1).substr(md5(time()),1);
            
            $this->request->data['User']['id'] = $user['User']['id']; // Poner id para que el save() lo que haga sea modificar
            $this->request->data['User']['password'] = $newPass;
            if ($this->User->save($this->request->data['User'])) {               
                // Send email and redirect to a welcome page
                $Email = new CakeEmail('desoft');
                $Email->template('recover_password')
                ->viewVars(array('newPass' => $newPass))
                ->emailFormat('html')
                ->to($this->request->data['User']['username'])
                ->subject('Your new password')
                ->send();

                return $this->render('password_changed');
                //return $this->authorize($activation_id);
            }
        }
    }
    
    public function profile() {
        if ($this->request->is('post')|| $this->request->is('put')) {
            $user = $this->request->data;
            
            if(strlen($user['User']['password']) == 0) unset ($user['User']['password']);
            if($this->User->save($user)) {
                $this->Session->write('Auth.User', $user['User']);
                
                // TODO: redirect???
                $this->setSuccessMessage('Your new info has been saved');
            } else {
                $this->setErrorMessage('There was a problem saving your info. Try again');
            }
        } else {
            // Find user to complete password field
            //$user = $this->User->findById($this->Auth->user('id'));
            //$user['User']['password'] = ''; // TODO: unhash password
            $this->request->data['User'] = $this->Auth->user();
        }
    }

    /*public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->setSuccessMessage(__('The user has been saved'));
                return $this->redirect(array('controller' => 'projects', 'action' => 'index'));
            }
            $this->setErrorMessage(__('The user could not be saved. Please, try again.'));
        }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->setSuccessMessage(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->setErrorMessage(__('The user could not be saved. Please, try again.'));
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->setSuccessMessage(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->setErrorMessage(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }*/

}

?>