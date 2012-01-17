<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Event;

/*
	@DAVID F'ING AROUND IN HERE
		* trying to call in events… 
		* updated SearchController.php to call in same stuff as EventsController, all dropped in below... no surprise: ain't working
*/
use app\controllers\EventsController;
use app\models\Item;
use app\models\Banner;
use MongoDate;
use lithium\storage\Session;
use app\models\Affiliate;
/*
	/ @DAVID F'ING AROUND IN HERE
*/


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