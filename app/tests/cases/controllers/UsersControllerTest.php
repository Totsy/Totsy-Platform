<?php

namespace app\tests\cases\controllers;

use lithium\action\Request;
use app\tests\mocks\controllers\MockUsersController;
use lithium\storage\Session;
use app\models\User;

class UsersControllerTest extends \lithium\test\Unit {

	public function setUp() {}

	public function testUserRegistration() {
		$post = array(
				'firstname'=>'totsyfirst',
				'lastname'=>'totsylast',
				'email'=>'iceangelmist@aim.com',
				'confirmemail'=>'iceangelmist@aim.com',
				'password'=>'yaabaadaabaadoo',
				'terms'=>'on',
				'emailcheck'=>true
			);
		$response = new Request(array(
			'data'=> $post,
			'params' => array('controller' => 'users', 'action' => 'register')
		));
		$testcode = 'testaffiliate';
		$userRemote = new MockUsersController(array('request'=>$response));
		$result = $userRemote->register($testcode);
		$this->assertEqual(true, $result, $result);
	}

	public function tearDown() {}

	/*
	* Testing the Password method from the CartController
	*/
	public function testUserPassword() {
		//Configuration Test
		$temp_password = "0A0DSDSDA0A";
		$temp_hashed_password = sha1($temp_password);
		$datas_user = array(
			"confirmemail" => "test_user_test@totsy.com",
			"email" => "test_user_test@totsy.com",
			"emailcheck" => true,
			"firstname" => "test_user",
			"invitation_codes" => "test_user",
			"invited_by" => null,
			"lastname" => "test_user",
			"password" => $temp_hashed_password,
			"terms" => "on",
			"zip" => "010101" );
		$user = User::create();
		$user->save($datas_user);
		$info = Session::write('userTemp',$user);
		$post = array(
				'password'=> $temp_password,
				'password_confirm'=>'AAA222',
				'new_password'=>'AAA222'
			);
		$request = new Request(array(
			'data' => $post,
			'params' => array('controller' => 'users', 'action' => 'password')
		));
		$remote = new MockUsersController(compact('request'));
		$remote->sessionKey = "userTemp";
		$remote->request->data = $post;
		$remote->request->params['type'] = 'html';
		//Request the tested method
		$result = $remote->password();
		//Test result
		$this->assertEqual( 'true' , $result["status"] );
		//Clean DB
		User::remove(array("_id" => $user['_id']));
	}
}

?>