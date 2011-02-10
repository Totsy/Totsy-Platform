<?php

namespace admin\controllers;

use admin\models\Group;
use \lithium\data\Connections;
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
			if($datas["add_group"] != ""){
				$this->create($datas["add_group"]);
			}
			elseif($datas["remove_group"] != ""){
				$this->remove($datas["remove_group"]);
			}
			else{
				$group = Group::find('first', array('conditions' => array('name' => $datas["select_group"])));
				if(!empty($datas["select_group"]) && ($datas["current"] == $datas["select_group"])){
					$this->update($group["_id"]);
				}
				$group = Group::find('first', array('conditions' => array('name' => $datas["select_group"])));
			}
		}
		
		$groups = Group::find('all');
		$select_groups["undefined"] = "select a group";
		foreach($groups as $gro){
			$select_groups[$gro["name"]] = $gro["name"];
		}
		return compact('group','select_groups');
	}
	
	/**
	* The view method 
	*
	* @param string $id The _id of the order
	*/
	public function view($id = null) {
	}
	
	/**
	 * Update the informations of the groups.
	 */
	public function update($id = null){
		if($id != null){
			$datas = $this->request->data;
			$groupsCollection = Group::collection();

			foreach($datas as $key => $data)
			{
				$param = explode("_", $key);
				if($param[0] == "acl" && !empty($data) ) $acls[$param[2]][$param[1]] = $data;
			}
			if(!empty($acls) || $datas["type"] == "cancel"){
				$groupsCollection->update(array("_id" => $id) , array('$unset' => array( "acls" => 1)));
				if(!empty($acls)){
					foreach($acls as $acl){
						if(($acl["connection"] != "") && ($acl["route"] != "")){
							$groupsCollection->update(array("_id" => $id) , array('$push' => array( "acls" => $acl)));
						}
					}
				}
			}
		}
	}
	
	public function create($name = null)
	{
		$test_group = Group::find('first', array('conditions' => array('name' => $name)));
		if(empty($test_group)){
			$group = Group::create(array("name" => $name));
			$group->save();
		}
	}
	
	public function remove($name = null)
	{
		$test_group = Group::find('first', array('conditions' => array('name' => $name)));
		if(!empty($test_group)){
			Group::remove(array("name" => $name));
		}
	}
}



?>