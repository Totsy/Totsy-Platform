<?php

namespace admin\controllers;

use \admin\models\Banner;
use li3_flash_message\extensions\storage\FlashMessage;
use lithium\storage\Session;
use MongoDate;

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
		if(!empty($this->request->data)){
			$check = $this->check();
			if ($check) {
				echo "check passed";
				$datas = $this->request->data;
				$images = $this->parseImages();
				$current_user = Session::read('userLogin');
				$author = $current_user["email"];
				$seconds = ':'.rand(10,60);
				$datas['end_date'] = new MongoDate(strtotime($datas['end_date'].$seconds));
				if($datas)
				$bannerDatas = array(
					"img" => $images,
					"end_date" => $datas['end_date'],
					"name" => $datas['name'],
					'author' => $author,
					'created_date' =>  new MongoDate(strtotime('now')),
					'enable' => $datas['enabled']
				);
				$banner = Banner::Create();
				if ($banner->save($bannerDatas)) {	
					//$this->redirect(array('Banner::edit', 'args' => array($event->_id)));
					FlashMessage::set("Your banner has been saved.", array('class' => 'pass'));
				}
			} else {
				FlashMessage::set("You must fill all the requested informations", array('class' => 'warning'));
			}
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
	
	/**
	* The check method verify if all the datas are collected to save the bannerr
	* @return boolean $check
	*/
	public function check() {
		$check = true;
		$images = array();
		$to_check = array(
			"name",
			"end_date",
			"img"
			);
		if(!empty($this->request->data)) {
			$datas = $this->request->data;
			foreach($to_check as $data) {
				if(empty($datas[$data])) {
					$check = false;
				}
			}
		} else {
			$check = false;
		}
		return $check;
	}
	
	/**
	 * Parse the images from the request using the key
	 * @param object
	 * @return array
	 */
	protected function parseImages($imageRecord = null) {
		$images = array();
		$datas = $this->request->data;
		foreach ($datas["img"] as $key => $value) {
			$images[$key]["_id"] = $value;
			if(!empty($datas['url'][$value])) {
				$images[$key]["url"] = $datas['url'][$value];
			}
		}
		return $images;
	}
}

?>