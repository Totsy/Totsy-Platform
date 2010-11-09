<?php

namespace app\controllers;

use app\controllers\BaseController;


class BlogController extends BaseController {
	
	public function index(){
		$url = 'http://totsyblog.blogspot.com/feeds/posts/default?alt=rss';
		$xml = simplexml_load_file($url);
		$rss = $xml->channel;
		return compact('rss');
	}
}

?>