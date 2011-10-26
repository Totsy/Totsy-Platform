<?php

namespace admin\controllers;

use admin\models\Vendor;

class VendorsController extends \lithium\action\Controller {

	public function index() {
		$vendors = Vendor::all();
		return compact('vendors');
	}

	public function view($id = null) {
		$vendor = Vendor::find($id);
		return compact('vendor');
	}

	public function add() {
		if (!empty($this->request->data)) {
			$vendor = Vendor::create($this->request->data);
			if ($vendor->save()) {
				$this->redirect(array(
					'controller' => 'vendors', 'action' => 'view',
					'args' => array($vendor->id)
				));
			}
		}
		if (empty($vendor)) {
			$vendor = Vendor::create();
		}
		return compact('vendor');
	}

	public function edit($id = null) {
		$vendor = Vendor::find($id);
		if (empty($vendor)) {
			$this->redirect(array('controller' => 'vendors', 'action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($vendor->save($this->request->data)) {
				$this->redirect(array(
					'controller' => 'vendors', 'action' => 'view',
					'args' => array($vendor->id)
				));
			}
		}
		return compact('vendor');
	}
}

?>