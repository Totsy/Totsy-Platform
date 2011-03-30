<?php

namespace admin\controllers;

use \admin\models\Banner;
use li3_flash_message\extensions\storage\FlashMessage;

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
		var_dump($this->request->data);
		if(!empty($this->request->data)){
			$check = $this->check();
			if ($check) {
				$datas = $this->request->data;
				$images = $this->parseImages();
				var_dump($images);
				$seconds = ':'.rand(10,60);
				$datas['end_date'] = new MongoDate(strtotime($datas['end_date'].$seconds));
				/**if($datas)
				$eventData = array_merge(
					Event::castData($this->request->data),
					compact('items'), 
					compact('images'), 
					array('created_date' => new MongoDate()),
					array('url' => $url)
				);
				//Remove this when $_schema is setup
				unset($eventData['itemTable_length']);
				if ($event->save($eventData)) {	
					$this->redirect(array('Events::edit', 'args' => array($event->_id)));
				}**/
				FlashMessage::set("Your banner has been saved.", array('class' => 'pass'));
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
			"end_date"
			);
		if(!empty($this->request->data)) {
			$datas = $this->request->data;
			foreach($to_check as $data) {
				if(empty($datas[$data])) {
					$check = false;
				}
			}
			//check images
			foreach ($datas as $key => $value) {
				if (substr($key, -6) == '_image' ) {
					$img_exist = true;
				}
			}
			if(empty($img_exist)) {
				$check = false;
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
		foreach ($this->request->data as $key => $value) {
			if (substr($key, -6) == '_image' ) {
				$images["$key"] = $value;
			}
		}
		if (empty($images) && !empty($imageRecord)) {
			$images = $imageRecord->data();
		}
		return $images;
	}
}

?>