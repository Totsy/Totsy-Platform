<?php

namespace app\controllers;
use app\models\User;

class UsersController extends \lithium\action\Controller {
	
	public function index(){
		
	}
	
	public function register(){
		$success = false;
		
        if ($this->request->data) {
        	$this->request->data['password'] = sha1($this->request->data['password']);
            $post = User::create($this->request->data);
            $success = $post->save();
        }
        return compact('success');
	}
    

}
?>