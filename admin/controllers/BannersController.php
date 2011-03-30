<?php

namespace admin\controllers;

use \admin\models\Banner;

class BannersController extends \lithium\action\Controller {

	public function index() {
		$banners = Banner::all();
		return compact('banners');
	}

	public function view() {
		$banner = Banner::first($this->request->id);
		return compact('banner');
	}

	public function add() {
		$banner = Banner::create();

		if (($this->request->data) && $banner->save($this->request->data)) {
			$this->redirect(array('Banners::view', 'args' => array($banner->id)));
		}
		return compact('banner');
	}

	public function edit() {
		$banner = Banner::find($this->request->id);

		if (!$banner) {
			$this->redirect('Banners::index');
		}
		if (($this->request->data) && $banner->save($this->request->data)) {
			$this->redirect(array('Banners::view', 'args' => array($banner->id)));
		}
		return compact('banner');
	}
}

?>