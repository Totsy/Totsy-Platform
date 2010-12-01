<?php

namespace app\controllers;

use app\controllers\BaseController;


class BlogController extends BaseController {
	
	public function index(){
		$this->redirect('http://blog.totsy.com');
	}
}

?>