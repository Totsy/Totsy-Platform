<?php

namespace app\controllers;
use app\models\User;
use \lithium\security\Auth;
use \lithium\storage\Session;

class UsersController extends \lithium\action\Controller {

	public function index(){

	}
	
	/**
	 * Performs basic registration functionality. All validation checks should happen via
	 * JavaScript so no empty data is going into Mongo.
	 * @todo Refactor to use count() from Mongo instead of array PHP count
	 * @todo Confirm redirect location and message upon sucessful registration.
	 * @return string User will be promoted that email is already registered.
	 */
	public function register(){
		$message = false;
        if ($this->request->data) {
        	$this->request->data['password'] = sha1($this->request->data['password']);
			$User = User::find('all',array('conditions' => array('email' => $this->request->data['email'])));
			if (count($User->data()) < 1 ) {
				$User = User::create($this->request->data);
	            $success = $User->save();
				if ($success) {
					 $this->redirect('/');
				}
			} else {
				$message = 'This email address is already registered';
			}
		}
		$this->_render['layout'] = 'base';
        return compact('message');
	}
	
	/**
	 * Performs login authentication for a user going directly to the database.
	 * If authenticated the user will be redirected to the home page.
	 *
	 * @return string The user is promted with a message if authentication failed.
	 */
	public function login() {
		
		$message = false;
		Auth::config(array(
			        'userLogin' => array(
						'model' => 'User',
						'adapter' => 'Form',
			            'fields' => array('email', 'password')
			        )
			    ));
		if ($this->request->data) {
			$auth = Auth::check("userLogin", $this->request, array('checkSession'=> false, 'writeSession' => false));
			if ($auth == false) {
				$message = 'Login Failed - Please Try Again';
			} else {
					Session::write('_id', $auth['_id']);
					Session::write('firstname', $auth['firstname']);
					Session::write('lastname', $auth['lastname']);
					Session::write('email', $auth['email']);				
					$this->redirect('/');
			}		
		}	

		return compact('message');		
	}
	
	/**
	 * Performs the logout action of the user removing '_id' from session details.
	 */
	public function logout() {
		//Delete session information
		Session::delete('_id');
		Session::delete('firstname');
		Session::delete('lastname');
		Session::delete('email');
		$this->redirect(array('action'=>'login'));
	}
}
?>