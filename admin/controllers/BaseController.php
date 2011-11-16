<?php

namespace admin\controllers;
use admin\models\Event;
use lithium\util\Inflector;
use \lithium\core\Environment;
use li3_flash_message\extensions\storage\FlashMessage;
use MongoDate;
use MongoRegex;

class BaseController extends \lithium\action\Controller {

    public function _init() {

        if(!Environment::is('production')){
            $branch = "<h4 class='global_site_msg'>Current branch " . $this->currentBranch() ."</h4>";
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
		$events = array();
		if($this->request->data) {
			$conditions = array();
			if (array_key_exists('todays', $this->request->data) && !empty($this->request->data['todays'])){
				$conditions = array(
					'start_date' => array('$gte'=> new MongoDate())
				);
			} elseif (array_key_exists('search', $this->request->data) && !empty($this->request->data['search'])) {
			    if ($this->request->data['search'] == '&' || $this->request->data['search'] == 'and') {
			        $this->request->data['search'] = '(&|and)';
			    }
				$conditions = array('name' => new MongoRegex("/" .trim($this->request->data['search']) ."/i"));
			} elseif(array_key_exists('start_date', $this->request->data) && !empty($this->request->data['start_date'])) {
				$conditions = array(
					'start_date' => array('$gte'=> new MongoDate(strtotime($this->request->data['start_date'])))
				);
			}else {
				$conditions = array(
					'end_date' => array('$gte' => new MongoDate(strtotime($this->request->data['end_date'])))
				);
			}
			$events = Event::find('all',array('conditions' => $conditions,
				'fields' => array('name' => 1,
				'start_date' => 1,
				'end_date' => 1,
				'blurb' => 1,
				'enabled' => 1,
				'_id' => 1
				) ));
		}
		return compact('events', 'type', 'environment');
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
        preg_match('#(\*)\s[a-zA-Z0-9_-]*(.)*#', $out, $parse);
        $pos = stripos($parse[0], " ");
        return trim(substr($parse[0], $pos));
	}
}
