<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Event;


/**
 * The 404 handler redirects to the `view()` method, and attempts to use it to do a regex match
 */
class SearchController extends BaseController {

	public function view() {
		$events = null;

		if ($this->request->search) {
			$events = Event::all(array('conditions' => array('blurb' => array(
				'like' => '/' . preg_quote($this->request->search, '/') . '/'
			))));
		}
		return compact('events');
	}
}

?>