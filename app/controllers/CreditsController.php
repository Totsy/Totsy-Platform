<?php

namespace app\controllers;

use app\models\Credit;
use app\models\User;
use lithium\storage\Session;
use app\controllers\BaseController;

/**
 * All the users to see their credits.
 */
class CreditsController extends BaseController
{
	/**
	 * Display the credits for a user.
	 */
	public function view() {
		$userInfo = Session::read('userLogin');
		if ($userInfo) {
			$user = User::find('first', array(
				'conditions' => array('_id' => $userInfo['_id']),
				'fields' => array('total_credit')
			));
			$credits = Credit::find('all', array(
				'conditions' => array(
					'$or' => array(
						array('user_id' => $userInfo['_id']),
						array('customer_id' => $userInfo['_id'])
			))));
		}
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_view';
		}
		return compact('user', 'credits', 'userInfo');
	}
}

?>