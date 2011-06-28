<?php

namespace admin\controllers;

use admin\models\User;
use lithium\security\Auth;
use lithium\storage\Session;
use lithium\data\Connections;
use admin\models\Cart;
use admin\models\Credit;
use admin\models\Order;
use MongoId;
use MongoDate;



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
			'invited_by',
			'deactivated_date',
			'reactivate_date'
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
				if (array_key_exists('created_orig', $userData)) {
				    $userData['register date'] = date("M d, Y", $userData['created_orig']['sec']);
				}
				if (array_key_exists('created_date', $userData)) {
				    if(is_array($userData['created_date'])){
				        $userData['register date'] = date("M d, Y",$userData["created_date"]['sec']);
				    } else {
				        $userData['register date'] = date("M d, Y",strtotime($userData["created_date"]));
				    }
				}
				if (array_key_exists('created_on', $userData)) {
				    $userData['register date'] = date("M d, Y", $userData['created_on']['sec']);
				}
				if (array_key_exists('deactivated_date', $userData)) {
				    $userData['deactivated_date'] = date("M d, Y", $userData['deactivated_date']['sec']);
				}
				if (array_key_exists('reactivate_date', $userData)) {
				    $userData['reactivate_date'] = date("M d, Y", $userData['reactivate_date']['sec']);
				}
				if (array_key_exists('deactivated', $userData)) {
				    $deactivated = $userData['deactivated'];
				} else {
				    $deactivated = false;
				}

				$data = array_intersect_key($userData, array_flip($headings['user']));
				$info = $this->sortArrayByArray($data, $headings['user']);
			}
		}

		return compact('user', 'credits', 'orders', 'headings', 'info', 'reasons', 'admin', 'deactivated');
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

	/**
	* Deactivate/Activate Users
	**/
	public function accountStatus($id = null) {
	    $this->_render['layout'] = false;
	    $type = $this->request->data['type'];
	    $collection = User::collection();
	    $user = $collection->find(array('_id' => new MongoId($id)));

	    if ($user) {
	        if ($type == "deactivate") {
	            $collection->update(array('_id' => new MongoId($id)), array('$unset'=>array('reactivate_date' => 1),
	            '$set'=>array(
	                'deactivated' => true,
	                'deactivated_date' => new MongoDate(strtotime("now"))
	                )));
	        } else {
	            $collection->update(array('_id' => new MongoId($id)), array(
	                '$unset'=>array('deactivated_date' => 1),
	                '$set'=>array(
	                    'deactivated' => false,
	                    'reactivate_date' =>  new MongoDate(strtotime("now"))
	                )));
	        }
	    }
	    $this->redirect(array('Users::view', 'args' => array($id)));
	}




}

?>