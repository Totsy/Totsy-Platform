<?php

namespace admin\controllers;

use admin\models\User;
use lithium\security\Auth;
use lithium\storage\Session;
use lithium\data\Connections;
use admin\models\Cart;
use admin\models\Credit;
use admin\models\Order;



/**
 * This class provides all the methods to register and authentic a user. 
 */

/*
	TODO The authenticaion process needs another look. We should be storing
	the users information in the session instead of the cookie. 
*/
class UsersController extends \admin\controllers\BaseController {


	public function index() {
		if ($this->request->data) {
			$users = User::findUsers($this->request->data);
		}
		$headings = array('firstname','lastname', 'email');
		return compact('users', 'headings');
	}

	public function view($id = null) {
		if ($id) {
			$user = User::find(
				'first', array(
					'conditions' => array('_id' => $id)
			));
			if ($user) {
				$headings = array(
					'user' => array(
						'firstname',
						'lastname',
						'email',
						'logincounter',
						'purchase_count'
						),
					'order' => array(
						'Date',
						'Order Id',
						'Total'),
					'credit' => array(
						'Date',
						'Reason',
						'Description',
						'Amount'
				));
				$reasons = array(
					'Manual Credit' => 'Manual Credit',
					'Invitation' => 'Invitation'
				);
				$credits = Credit::find('all', array(
					'conditions' => array(
						'$or' => array(
							array('user_id' => $id),
							array('customer_id' => $id)
					))));
				$orders = Order::find('all', array('conditions' => array('user_id' => $id)));
				$data = array_intersect_key($user->data(), array_flip($headings['user']));
				$info = $this->sortArrayByArray($data, $headings['user']);
			}
		}

		return compact('user', 'credits', 'orders', 'headings', 'info', 'reasons');
	}
	/**
	 * Performs login authentication for a user going directly to the database.
	 * If authenticated the user will be redirected to the home page.
	 *
	 * @return string The user is prompted with a message if authentication failed.
	 */
	public function login() {
		$message = false;

		if ($this->request->data) {
			if (Auth::check("userLogin", $this->request)) {
				return $this->redirect('/');
			}
			$message = 'Login Failed - Please Try Again';
		}
		return compact('message');
	}

	/**
	 * Performs the logout action of the user removing '_id' from session details.
	 */
	public function logout() {
		Auth::clear('userLogin');
		$this->redirect(array('action'=>'login'));
	}

	/**
	 * @param array $sessionInfo
	 * @return boolean
	 */
	private function writeSession($sessionInfo) {
		return (Session::write('userLogin', $sessionInfo));
	}


}

?>