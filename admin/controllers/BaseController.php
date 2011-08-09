<?php

namespace admin\controllers;
use admin\models\Event;
use lithium\util\Inflector;
use \lithium\core\Environment;
use li3_flash_message\extensions\storage\FlashMessage;
use MongoDate;
class BaseController extends \lithium\action\Controller {

    public function _init() {

        if(!Environment::is('production')){
            $branch = "<h4 id='#global_site_msg'>Current branch " . $this->currentBranch() ."</h4>";
            $this->set(compact('branch'));
        }
		parent::_init();
    }

	/**
	 * Common method to clean URLs
	 */
	protected function cleanUrl($str) {
		return strtolower(Inflector::slug($str));
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
		$events = null;
		if(!empty($this->request->data)) {
			$month_delay = (int) $this->request->data['month_delay']; 	
			if ($month_delay === 48) {
				$events = Event::find('all');
			} else {
				$date_limit = mktime(0, 0, 0, (date("m") - $month_delay), date("d"), date("Y"));
				$conditions = array(
					'created_date' => array(
    		   		'$gt' => new MongoDate($date_limit)
				));
				$events = Event::find('all', array('conditions' => $conditions));
			}
		}
		return compact('events', 'type', 'month_delay');
	}

	protected function _asciiClean($description) {
		return preg_replace('/[^(\x20-\x7F)]*/','', $description);
	}

	public static function randomString($length = 8, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
		$chars_length = (strlen($chars) - 1);
		$string = $chars{rand(0, $chars_length)};
		for ($i = 1; $i < $length; $i = strlen($string)) {
			$r = $chars{rand(0, $chars_length)};
			if ($r != $string{$i - 1}) $string .=  $r;
		}
		return $string;
	}

	public function currentBranch() {
        $out = shell_exec("git branch --no-color");
        preg_match('#(\*)\s(\w+)-(\w+)#', $out, $parse);
        $pos = stripos($parse[0], " ");
        return trim(substr($parse[0], $pos));
	}
}
