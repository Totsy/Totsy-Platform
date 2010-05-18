<?php

namespace app\controllers;
use app\models\User;
use \lithium\security\auth;
use \lithium\storage\session\adapter\Php;


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
				$success = 'This email is already registered';
			}
		}
        return compact('success');
	}
	
	/**
	 * Performs login authentication for a user going directly to the database.
	 * If authenticated the user will be redirected to the home page.
	 *
	 * @return string The user is promted with a message if authentication failed.
	 */
	public	function login() {
		
		$message = '';
		Auth::config(array(
			        'userLogin' => array(
						'model' => 'User',
						'adapter' => 'Form',
			            'fields' => array('email', 'password')
			        )
			    ));
		
		if ($this->request->data) {
			$auth = Auth::check("userLogin", $this->request, array('checkSession' => false));
			if (is_array($auth)){
				$this->redirect('/');
			} else {
				$message = "Login Failed: Please Try Again.";
			}
		}

		return compact('message');
		
	}
	
	/**
	 * Performs the logout action of the user removing any session details.
	 */
	public function logout() {
		$result = Php::delete('userLogin');

	}
	

}
?>