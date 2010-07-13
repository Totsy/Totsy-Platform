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

}