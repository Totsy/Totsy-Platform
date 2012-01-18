<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Event;

/*
	@DAVID F'ING AROUND IN HERE
		* trying to call in events… 
		* updated SearchController.php to call in same stuff as EventsController, all dropped in below... no surprise: ain't working
*/


/*
	/ @DAVID F'ING AROUND IN HERE
*/


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

		echo 'TOTAL $openEventsData: '.count($openEventsData).'<br>';
		echo 'TOTAL: '.count($openEvents).'<br>';
		echo '<pre>';
		print_r($openEvents);
		echo '</pre>';
			
		if ($this->request->search) {
			$events = Event::all(array('conditions' => array('blurb' => array(
				'like' => '/' . preg_quote($this->request->search, '/') . '/'
			))));
		}
		return compact('events', 'openEvents');
	}
}

?>