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
 * The Users Contoller for the namespace admin provides authentication and
 * CRUD functionality. Currently, the Users Controller is only providing read functionality
 * but it will be expanded to handle the full end to end CRUD.
 */

class UsersController extends \admin\controllers\BaseController {

	/**
	 * Associative array of headings used in view.
	 */
	protected $_headings = array(
		'user' => array(
			'firstname',
			'lastname',
			'email',
			'register date',
			'zip',
			'logincounter',
			'purchase_count',
			'invited_by'
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

	public function index() {
		if ($this->request->data) {
			$users = User::findUsers($this->request->data);
		}
		$headings = array('Ref','Last Name', 'First Name','Email','Zip/postal code');
		return compact('users', 'headings');
	}

	public function view($id = null) {
		if ($id) {
			$admin = Session::read('userLogin');
			$user = User::find(
				'first', array(
					'conditions' => array('_id' => $id)
			));
			if ($user) {
				$headings = $this->_headings;
				$reasons = array(
					'Credit Adjustment' => 'Credit Adjustment',
					'Invitation' => 'Invitation'
				);
				$credits = Credit::find('all', array(
					'conditions' => array(
						'$or' => array(
							array('user_id' => $id),
							array('customer_id' => $id)
					))));
				$orders = Order::find('all', array('conditions' => array('user_id' => $id)));
				$userData = $user->data();

				$userData['register date'] = (array_key_exists('created_orig', $userData)) ?
				date("M d, Y", $userData['created_orig']['sec']) :
				date("M d, Y",strtotime($info["created_date"]));
				$data = array_intersect_key($userData, array_flip($headings['user']));
				$info = $this->sortArrayByArray($data, $headings['user']);
			}
		}

		return compact('user', 'credits', 'orders', 'headings', 'info', 'reasons', 'admin');
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
		    $this->request->data['email'] = strtolower($this->request->data['email']);
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