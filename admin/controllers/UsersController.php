<?php

namespace admin\controllers;

use admin\models\User;
use lithium\security\Auth;
use lithium\storage\Session;
use lithium\data\Connections;
use admin\models\Cart;
use admin\models\Credit;
use admin\models\Order;
use admin\models\Group;
use MongoId;


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
		$headings = array('Ref','Last Name', 'First Name','Zip/postal code');
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
				$data = array_intersect_key($user->data(), array_flip($headings['user']));
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
	* Update the informations (Groups and ACLs) of the current user.
	* @param string $id The _id of the User
	*/
	public function update($id = null) {
		if($id){
			$headings = $this->_headings;
			$usersCollection = User::collection();
			$groupsCollection = Group::collection();
			//Test datas form
			if ($this->request->data) {
				$datas = $this->request->data;
				$n = 0;
				$j = 0;
				if(!empty($datas)) {
					foreach($datas as $key => $data) {
						$group = Group::find('first', array('conditions' => array('name' => $key)));
						//Test if group exist and the data field is not equal to null
						if(!empty($group) && ($data == 1)){
							$groups_user[$n] = $group["_id"];
							//Test if group has acls
							if(!empty($group["acls"])){
								//Fill the futur acls array for the actual user
								foreach($group["acls"] as $group_acl) {
									$acls_pre_user[$j]["connection"] = $group_acl["connection"];
									$acls_pre_user[$j]["route"] = $group_acl["route"];
									$j++;
								}
							}
							$n++;
						}
					}//end foreach datas
					//Clean acls result
					if(count($acls_pre_user) > 1){
						$acls_user = User::arrayUnique($acls_pre_user);
					}
					else {
						$acls_user = $acls_pre_user;
					}
					//Decrement total_users from group erased if groups will be updated.
					if($n > 0) {
						$user = User::find('first', array('conditions' => array('_id' => $id)));
						if(!empty($user["groups"])){
							foreach($user["groups"] as $user_group){
								$groupsCollection->update(array("_id" =>
								new MongoId($user_group)) ,
								array('$inc' => array( "total_users" => -1 )));
							}
						}
					}
					//First erase the existing groups and acls of the user
					if(strlen($id) > 10) {
						$usersCollection->update(array("_id" => new MongoId($id)),
						array('$unset' => array( "groups" => 1)));
						$usersCollection->update(array("_id" => new MongoId($id)),
						array('$unset' => array( "acls" => 1)));
					}else {
						$usersCollection->update(array("_id" => $id) ,
						array('$unset' => array( "groups" => 1)));
						$usersCollection->update(array("_id" => $id),
						array('$unset' => array( "acls" => 1)));
					}
					//Add groups and ACLs to the user document
					if(!empty($groups_user)) {
						//Test if id is a string or a MongoId
						foreach($groups_user as $group_user){
							if(strlen($id) > 10) {
								$usersCollection->update(array("_id" => new MongoId($id)),
								array('$addToSet' => array( "groups" =>  new MongoId($group_user))),
								array('upsert' => true));
								$usersCollection->update(array("_id" => new MongoId($id)) ,
								array('$set' => array( 'acls' => $acls_user)), array('upsert' => true));
							}else {
								$usersCollection->update(array("_id" => $id) ,
								array('$addToSet' => array( "groups" =>  new MongoId($group_user))),
								array('upsert' => true));
								$usersCollection->update(array("_id" => $id) ,
								array('$set' => array( 'acls' => $acls_user)),
								array('upsert' => true));
							}
							//Increment total_users for the group selected
							$groupsCollection->update(array("_id" => new MongoId($group_user)),
							array('$inc' => array( "total_users" => 1 )));
						}
					}//End of if groups to add condition
				}//End of Request->Datas condition
			}//End of Id condition
			//Get actual user informations and all groups
			$user = User::find('first', array('conditions' => array('_id' => $id)));
			$groups = Group::find('all');
			$data = array_intersect_key($user->data(), array_flip($headings['user']));
			$info = $this->sortArrayByArray($data, $headings['user']);
			return compact('user','groups','info');
		}
	}
}

?>