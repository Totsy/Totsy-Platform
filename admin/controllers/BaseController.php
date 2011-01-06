<?php

namespace admin\controllers;
use admin\models\Event;

class BaseController extends \lithium\action\Controller {

	/**
	 * Common method to clean URLs
	 */
	protected function cleanUrl($str) {
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
		$clean = strtolower(trim($clean, ' '));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);

		return $clean;
	}

	public function sortArrayByArray($array, $orderArray) {
	    $ordered = array();
	    foreach($orderArray as $key) {
	        if(array_key_exists($key,$array)) {
	                $ordered[$key] = $array[$key];
	                unset($array[$key]);
	        }
	    }
	    return $ordered + $array;
	}

	/**
	 * The selectEvent method provides a list of events in a table
	 * based on the type configuration provided in the url {:arg}.
	 *
	 * @see /extensions/helpers/Events.php
	 */
	public function selectEvent($type = null) {
		$events = Event::all();
		return compact('events', 'type');
	}

}