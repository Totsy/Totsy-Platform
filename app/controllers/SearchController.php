<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Event;

/**
 * The 404 handler redirects to the `view()` method, and attempts to use it to do a regex match
 */
class SearchController extends BaseController {

	// show only * number of open events
	private $showEvents = 4;

	public function view() {
		$events = null;
		$openEventsData = Event::open()->data();
		$openEvents = array_slice($openEventsData,0,$this->showEvents,true);
		unset($openEventsData);

		// What's in here and available to us?
		/*
		echo 'TOTAL $openEventsData: '.count($openEventsData).'<br>';
		echo 'TOTAL: '.count($openEvents).'<br>';
		echo '<pre>';
		print_r($openEvents);
		echo '</pre>';
		*/
			
		if ($this->request->search) {
			$events = Event::all(array('conditions' => array('blurb' => array(
				'like' => '/' . preg_quote($this->request->search, '/') . '/'
			))));
		}
		return compact('events', 'openEvents');
	}
}

?>