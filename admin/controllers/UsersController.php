<?php

namespace admin\controllers;

use admin\models\User;
use lithium\security\Auth;
use lithium\storage\Session;
use lithium\data\Connections;
use lithium\util\String;
use admin\models\Cart;
use admin\models\Credit;
use admin\models\Order;
use admin\models\Promotion;
use MongoId;
use MongoDate;
use admin\extensions\Mailer;
use MongoRegex;


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
			'Amount',
			'Applied By'),
		'promo' => array(
			'Date',
			'Order Id',
			'Code',
			'Type'
		)
	);

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
			$promocodes_used = Promotion::find('all', array('conditions' => array('user_id' => $user['_id'])));
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
				foreach($credits as $credit) {
					if ($credit->admin_id) {
						$appliedby = User::lookup($credit->admin_id);
						$credit->admin_user = $appliedby->email;
					}
				}
				$orders = Order::find('all', array('conditions' => array('user_id' => $id)));
				$userData = $user->data();
				if (array_key_exists('created_orig', $userData)) {
				    $userData['register date'] = date("M d, Y", $userData['created_orig']);
				}
				if (array_key_exists('created_date', $userData)) {
				    if(is_array($userData['created_date'])){
				        $userData['register date'] = date("M d, Y",$userData["created_date"]['sec']);
				    } else {
				        $userData['register date'] = date("M d, Y",$userData["created_date"]);
				    }
				}
				if (array_key_exists('created_on', $userData)) {
				    $userData['register date'] = date("M d, Y", $userData['created_on']);
				}
				if (array_key_exists('deactivated_date', $userData)) {
				    if(is_array($userData['deactivated_date'])){
				        $userData['register date'] = date("M d, Y",$userData['deactivated_date']['sec']);
				    } else {
				        $userData['register date'] = date("M d, Y",$userData['deactivated_date']);
				    }
				}
				if (array_key_exists('reactivate_date', $userData)) {
				    if(is_array($userData['reactivate_date'])){
				        $userData['reactivate_date'] = date("M d, Y",$userData['reactivate_date']['sec']);
				    } else {
				        $userData['reactivate_date'] = date("M d, Y",$userData['reactivate_date']);
				    }
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

		return compact('user', 'credits', 'orders', 'headings', 'info', 'reasons', 'admin', 'deactivated', 'promocodes_used');
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

	public function adminManager() {
	    $UserCollection = User::collection();
	    $admin = Session::read('userLogin');

	    $admins = $UserCollection->find(
	    array('$or'=> array(
	            array('admin' => array('$exists' => true)),
	            array('email' => new MongoRegex('/@totsy.com/i'))
	        ),
	        'firstname' => array('$ne' => 'Affiliate')),
	        array(
	            'email' => true,
	            'firstname' => true,
	            'lastname' => true,
	            'admin'=> true,
	            'superadmin' => true,
	            'created_date' => true,
	            'created_orig' => true)
	    );
	    if ($this->request->data) {
	        $email = $this->request->data['email'];
	        $access = $this->request->data['access'];
	        $level = $this->request->data['type'];

	        if ($access == "deny") {
	            $access = false;
	        } else {
	            $access = true;
	        }

	        $conditions = array("email" => $email);
	        $set = array('$set' => array($level => $access));

	        $UserCollection->update($conditions, $set);

	    }
	    return compact('admins', 'admin');
	}
	/**
	* Deactivate/Activate Users
	*
	**/
	public function accountStatus($id = null) {
	    $this->_render['layout'] = false;
	    $type = $this->request->data['type'];
	    $reason = $this->request->data['deactivate_reason'];
	    $comment = trim($this->request->data['comment']);
	    $collection = User::collection();
	    $deactivate_log = User::collections('deactivation.log');
	    $user = $collection->findOne(array('_id' => new MongoId($id)));

	    if ($user) {
	        if ($type == "deactivate") {
	            $date = new MongoDate(strtotime("now"));
	            if (strlen($id) > 10) {
	                $id = new MongoId($id);
	            }
                $collection->update(
                    array('_id' => $id),
                    array(
                        '$unset'=>array('reactivate_date' => 1),
                        '$set'=>array(
                            'deactivated' => true,
                            'deactivated_date' => $date
                        )
                    ));
	            $deactivate_log->save(array(
	                "user_id" => $id,
	                'reason' => $reason,
	                "comment" => $comment,
	                "date_created" => $date,
	                "created_by" => User::createdBy()
	            ));
	            $data['to_email'] = $user['email'];
	            $data['from_email'] = 'support@totsy.com';
	            $data['reason'] = $type;
	         //   Mailer::send('Account_Status', $data['to_email']);
	        //   Mailer::optOut($data['to_email'], null, array('internal_tests_eric' => 0));
	        } else {
	            $collection->update(array('_id' => new MongoId($id)), array(
	                '$unset'=>array('deactivated_date' => 1),
	                '$set'=>array(
	                    'deactivated' => false,
	                    'reactivate_date' =>  new MongoDate(strtotime("now"))
	                )));
	           // Mailer::send('Account_Status', $data['to_email'], $data['type']);
	        }
	    }
	    $this->redirect(array('Users::view', 'args' => array($id)));
	}

	public function deactivateHistory($id = null) {
	    $this->_render['layout'] = false;
	    $collection = User::collections("deactivation.log");
        $results = $collection->find(array('user_id' => $id));
        foreach($results as $entry) {
            User::meta('source','users');
            $conditions = array('conditions'=>array('_id' => new MongoId($entry['created_by'])));
            $admin = User::find( 'first', $conditions );
            $admin = $admin->data();
            if (array_key_exists('firstname', $admin)) {
                $entry['created_by'] = $admin['firstname'] . ' ' . $admin['lastname'];
            } else {
                $entry['created_by'] = $admin['email'];
            }
            $entry['date_created'] = date("M d, Y", $entry['date_created']->sec);
            $history[] = $entry;
        }
        return compact('history');
	}

	public function token() {
		$session = Session::read('userLogin');
		do { /* Ensure we don't have a dot in the token. */
			$token = String::random(6, array('encode' => String::ENCODE_BASE_64));
		} while (strpos($token, '.') !== false);

		$user = User::first(array('conditions' => array('_id' => $session['_id'])));
		$user->save(compact('token'));
		$session['token'] = $token;
		Session::write('userLogin', $session);

		return $this->redirect($this->request->referer());
	}
}

?>