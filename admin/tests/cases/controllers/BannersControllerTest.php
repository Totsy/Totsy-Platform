<?php

namespace admin\tests\cases\controllers;

use admin\controllers\BannersController;
use admin\controllers\EventsController;
use admin\models\Banner;
use MongoDate;
use MongoId;
use lithium\action\Request;

class BannersControllerTest extends \lithium\test\Unit {

	public function skip() {
		$message = "SAPI has no session support.";
		$this->skipIf(PHP_SAPI == 'cli', $message);
	}

	/*
	* Testing the check method from the BannersController
	*/
	public function testCheck() {
		$post = array(
			"name" => Banner::createdBy(),
			"end_date" =>  new MongoDate(strtotime('now')),
			"img" => "552656536256212121");
		$response = new Request(array('data'=>$post));
		$remote = new BannersController(array('request' => $response));
		$remote->request->params['type'] = 'html';
		$result = $remote->check();
		$this->assertEqual(true , $result);
	}
	
	/*
	* Testing the parseImage method from the BannersController
	*/
	public function testparseImages() {
		$img = array(
			"0" => "6767676776767763",
				"url" => "http://www.test.com"
		);
		$url = array(
			"6767676776767763" => "http://www.test.com"
		);
		$post = array(
			"img" => $img,
			"url" => $url
			);
		$response = new Request(array('data'=>$post));
		$remote = new BannersController(array('request' => $response));
		$remote->request->params['type'] = 'html';
		$result = $remote->parseImages();
		$this->assertEqual(true , $result);
	}
	
	/*
	* Testing the add method from the BannersController
	*/
	public function testAdd() {
		$img = array(
			"0" => "6767676776767763",
				"url" => "http://www.test.com"
		);
		$url = array(
			"6767676776767763" => "http://www.test.com"
		);
		$post = array(
			"name" => "test",
			"author" => Banner::createdBy(),
			"end_date" =>  new MongoDate(strtotime('now')),
			"img" => $img,
			"url" => $url
			);
		$response = new Request(array('data'=>$post));
		$remote = new BannersController(array('request' => $response));
		$remote->request->params['type'] = 'html';
		$result = $remote->add();
		$datas = $result["banner"]->data();
		$this->assertEqual(true , $datas["img"]);
	}

	/*
	* Testing the preview method from the BannersController
	*/
	public function testPreview() {
		//Configuration Test
		$banner_id = new MongoId("787878787zazazag7878");
		$banner_data = array(
				"_id" => $banner_id,
				"img" => array(
					"0" => array(
					"_id" => "6767676776767763",
					"url" => "http://www.test.com"
				)),
				"end_date" =>  new MongoDate(strtotime('now')),
				"name" => "test",
				"author" => Banner::createdBy()
				);
		$banner = Banner::create();
		$banner->save($banner_data);
		$remote = new BannersController();
		$result = $remote->preview((string) $banner_id);
		$datas = $result["banner"];
		$this->assertEqual(true , $datas);
		Banner::remove(array('_id' => $banner_id ));
	}
}