<?php

namespace admin\controllers;

use \admin\models\Service;
use MongoDate;
use \lithium\util\Validator;

class ServicesController extends BaseController {
    /**
    * Trigger selection fields
    **/
    public $triggers = array(
        "cart_value" => "Cart Total",
        "cart_items" => "Cart Items Count"
    );
    /**
    * Trigger action selection fields
    **/
    public $trigger_actions = array(
        "none" => "no action",
        "pop_up" => "pop up",
        "_blank" => "new window"
    );
	public function index() {
		$services = Service::all();
		$triggers = $this->triggers;
		return compact('services', 'triggers');
	}

	public function add() {
        $data = $this->request->data;
        $service = Service::create($data);
		if (($data) && $service->validates()) {
		    $save['enabled'] = (bool)$data['enabled'];
		    $save['name'] = trim($data['name']);
		    $save['start_date'] = new MongoDate(strtotime($data['start_date']));
		    $save['end_date'] = new MongoDate(strtotime($data['end_date']));
		    $save['trigger_type'] = $data['trigger_type'];
		    $save['trigger_action'] = $data['trigger_action'];
		    $save['trigger_value'] = (int)trim($data['trigger_value']);
		    $save['logo_image'] = $data['img'];
		    if(!empty($data['in_stock'])){
		        $save['in_stock'] = (int)$data['in_stock'];
		    }
		    $whitelist = array('enabled','name','start_date','trigger_type', 'trigger_value', 'logo_image', 'in_stock', 'end_date', 'trigger_action');
		    if($service->save($save, array('validate' => false,'whitelist'=>$whitelist))){
			    $this->redirect(array('Services::index'));
			}
		}
		$triggers = $this->triggers;
		$trigger_actions = $this->trigger_actions;
		return compact('service', 'triggers', 'trigger_actions');
	}

	public function edit($id = null ) {
		$service = Service::find($id);
        $data = $this->request->data;
		if (!$service) {
			$this->redirect('Services::index');
		}
		if(($data)){
		    $newData = Service::create($data);
		}else{
		    $newData = $service;
		    $newData->start_date = date("m/d/Y", $service->start_date->sec);
		    $newData->end_date = date("m/d/Y", $service->end_date->sec);
		}
		if (($data) && $newData->validates()) {
		    $save['enabled'] = (bool) $data['enabled'];
		    $save['name'] = trim($data['name']);
		    $save['start_date'] = new MongoDate(strtotime($data['start_date']));
		    $save['end_date'] = new MongoDate(strtotime($data['end_date']));
		    $save['trigger_type'] = $data['trigger_type'];
		    $save['trigger_action'] = $data['trigger_action'];
		    $save['trigger_value'] = (int) trim($data['trigger_value']);
		    if(array_key_exists('img', $data) && $data['img']){
		        $save['logo_image'] = $data['img'];
		    }
		    if(!empty($data['in_stock'])){
		        $save['in_stock'] = (int) $data['in_stock'];
		    }
		    $whitelist = array('enabled', 'name', 'start_date', 'trigger_type', 'trigger_value', 'logo_image', 'in_stock', 'end_date', 'trigger_action');
		    if($service->save($save, array('validate' => false,'whitelist'=>$whitelist))){
			    $this->redirect(array('Services::index'));
			}
		}
		$triggers = $this->triggers;
		$trigger_actions = $this->trigger_actions;
		$newData->logo_image = $service->logo_image;
		return compact('newData', 'triggers', 'trigger_actions');
	}
}

?>