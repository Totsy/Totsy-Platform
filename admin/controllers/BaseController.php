<?php

namespace admin\controllers;


class BaseController extends \lithium\action\Controller {

	/**
	 * Common method to clean URLs
	 */
	protected function cleanUrl($str) {
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
		$clean = strtolower(trim($clean, '-'));
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

}