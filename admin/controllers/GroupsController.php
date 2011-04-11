<?php

namespace admin\controllers;

use admin\models\Group;
use admin\models\User;
use lithium\data\Connections;
use admin\controllers\BaseController;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;

/**
 * The Groups Contoller
 *
 */

class GroupsController extends \admin\controllers\BaseController {

	/**
	* Main view
	*
	*/
	public function index() {
		if($this->request->data){
			$datas = $this->request->data;
			//Case : Create a group
			if(!empty($datas["add_group"])){
				$this->create($datas["add_group"]);
			}//Case : Remove a group
			elseif(!empty($datas["remove_group"]) && $datas["select_group"] == "undefined"){
				if(strlen($datas["remove_group"]) > 2) {
					$this->remove($datas["remove_group"]);
				}
			}elseif($datas["select_group"] == "undefined"){
				//Case Change selection
			}//Case update ACLs of groups
			else{
				$group_selected = Group::find('first', array('conditions' =>
				array('name' => $datas["select_group"])));
				$search = User::find('all', array('conditions' =>
				array('groups' => $group_selected["_id"])));
				$users = $search->data();
				if(!empty($datas["current"])) {
					if( (!empty($datas["select_group"])) && ($datas["select_group"] != "undefined") && ($datas["current"] == $datas["select_group"])) {
							$group = Group::create($this->request->data);
							if($group->validates()){
								$this->update($group_selected["_id"]);
							}
					}
				}
				//Refresh Result after update
				$search = User::find('all', array('conditions' =>
				array('groups' => $group_selected["_id"])));
				$users = $search->data();
				$group_selected = Group::find('first', array('conditions' =>
				array('name' => $datas["select_group"])));
			}
		}
		//fill empty datas
		if(empty($group)) {
			$group = null;
		}
		//Create Dropdown Menu
		$groups = Group::find('all');
		$ddm_groups["undefined"] = "select a group";
		foreach($groups as $gro){
			$ddm_groups[$gro["name"]] = $gro["name"];
		}
		return compact('group','group_selected','ddm_groups','users');
	}

	/**
	* The view method
	*
	* @param string $id The _id of the group
	*/
	public function view($id = null) {
	}

	/**
	 * Update the informations of the groups.
	 * @param string $id The _id of the group
	 */
	public function update($id = null){
		$usersCollection = User::collection();
		$groupsCollection = Group::collection();
		if($id != null){
			$datas = $this->request->data;
			foreach($datas as $key => $data) {
				$param = explode("_", $key);
				if(($param[0] == "acl" || $param[0] == "newacl") && !empty($data)){
					$acls[$param[2]][$param[1]] = $data;
				}
				if($param[0] == "user" && !empty($data)){
					$users[] = $data;
				}
			}
			if(!empty($acls) || $datas["type"] == "cancel"){
				$groupsCollection->update(array("_id" => $id) ,
				array('$unset' => array( "acls" => 1)));
				if(!empty($acls)){
					foreach($acls as $acl){
						if(($acl["connection"] != "") && ($acl["route"] != "")){
							$groupsCollection->update(array("_id" => $id) ,
							array('$push' => array( "acls" => $acl)));
						}
					}
				}
			}
			if(!empty($users)){
				foreach($users as $user){
					if(strlen($user) > 10) {
						$usersCollection->update(array("_id" => new MongoId($user)),
							array('$pull' => array( 'groups' => new MongoId($id) )));
					}else{
						$usersCollection->update(array("_id" => $user),
							array('$pull' => array( 'groups' => new MongoId($id) )));
					}
					$this->cleanAclUsers($user);
				}
			}
			$users = User::find('all', array('conditions' => array('groups' => new MongoId($id))));
			foreach($users as $user) {
				$this->cleanAclUsers($user["_id"]);
			}
		}
	}

	/**
	* Create a new group in groups collection
	* @param string $name of the group to create.
	*/
	public function create($name = null)
	{
		$test_group = Group::find('first', array('conditions' => array('name' => $name)));
		if(empty($test_group)){
			$group = Group::create(array("name" => $name));
			$group->save();
		}
	}

	/**
	* Remove a group from groups collection
	* @param string $name of the group to remove.
	*/
	public function remove($name = null)
	{
		$usersCollection = User::collection();
		$test_group = Group::find('first', array('conditions' => array('name' => $name)));
		if(!empty($test_group)) {
			Group::remove(array("name" => $name));
		}
		$users = User::find('all', array('conditions' => array('groups' => $test_group["_id"])));
		foreach($users as $user) {
			$usersCollection->update(array("_id" => $user["_id"]),
				array('$pull' => array( 'groups' => new MongoId($test_group["_id"]))));
			$this->cleanAclUsers($user["_id"]);
		}
	}

	/**
	* Clean ACLs/Groups
	* @param string $id of _id of the user to clean
	*/
	public function cleanAclUsers($id = null)
	{
		$usersCollection = User::collection();
		if(strlen($id) > 10) {
			$id = new MongoId($id);
		}
		$user = User::find('first', array('conditions' => array('_id' => $id)));
		$usersCollection->update(array("_id" => $id),
			array('$unset' => array( "acls" => 1)));
		if(!empty($user["groups"])){
			foreach($user["groups"] as $user_group){
				$group = Group::find("first", array('conditions' => array("_id" => $user_group)));
				if(!empty($group["acls"])){
						$j = 0;
						//Fill the futur acls array for the actual user
						foreach($group["acls"] as $group_acl) {
							$acls_pre_user[$j]["connection"] = $group_acl["connection"];
							$acls_pre_user[$j]["route"] = $group_acl["route"];
							$j++;
						}
				}
			}
			//Clean acls result
			if(count($acls_pre_user) > 1) {
				$acls_user = User::arrayUnique($acls_pre_user);
			} else{
				$acls_user = $acls_pre_user;
			}
			if(!empty($acls_user)){
				$usersCollection->update(array("_id" => $id) ,
					array('$set' => array( 'acls' => $acls_user)), array('upsert' => true));
			}
		}
	}
}

?>