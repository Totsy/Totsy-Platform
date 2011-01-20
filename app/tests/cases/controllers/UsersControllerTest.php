<?php

namespace app\tests\cases\controllers;

use lithium\action\Request;
use \app\controllers\UsersController;


class UsersControllerTest extends \lithium\test\Unit {


	public function setUp() {}

	public function testUserRegistration() {
		$post=array(
				'firstname'=>'totsyfirst',
				'lastname'=>'totsylast',
				'email'=>'iceangelmist@aim.com',
				'confirmemail'=>'iceangelmist@aim.com',
				'password'=>'yaabaadaabaadoo',
				'terms'=>'on',
				'emailcheck'=>true
			);
		$response= new Request(array('data'=>$post));
		$testcode= 'testaffiliate';
		$userRemote = new UsersController(array('request'=>$response));
		$result= $userRemote->registration($testcode);
		$this->assertEqual(true, $result, $result);
	}

	public function tearDown() {}


}

?>