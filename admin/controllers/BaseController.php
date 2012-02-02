<?php

namespace admin\controllers;
use admin\models\Event;
use lithium\util\Inflector;
use \lithium\core\Environment;
use li3_flash_message\extensions\storage\FlashMessage;
use lithium\storage\Session as Session;
use MongoDate;
use MongoRegex;

class BaseController extends \lithium\action\Controller {

	public $_mapCategories = array (
		'category' =>  array(
			'girls-apparel' => "Girls Apparel",
			'boys-apparel' => "Boys Apparel",
			'shoes' => "Shoes",
			'accessories' =>"Accessories",
			'toys-books' => "Toys and Books",
			'gear' => "Gear",
			'home' => "Home",
			'moms-dads' => "Moms and Dads"
		),
		'age' => array(
			'newborn' => 'Newborn 0-6M',
			'infant' => 'Infant 6-24M',
			'toddler' => 'Toddler 1-3 Y',
			'preschool' => 'Preschool 3-4Y',
			'school' => 'School Age 5+',
			'adult' => 'Adult'
		)
	);

	public function __construct(array $config = array()) {
		/* Merge $_classes of parent. */
		$vars = get_class_vars('\lithium\action\Controller');
		$this->_classes += $vars['_classes'];

		parent::__construct($config);
	}

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
					'$where' => "(this.start_date >= new Date()) && (this.start_date != new Date('1969'))"
				);
			} elseif (array_key_exists('search', $this->request->data) && !empty($this->request->data['search'])) {
			    if ($this->request->data['search'] == '&' || $this->request->data['search'] == 'and') {
			        $this->request->data['search'] = '(&|and)';
			    }
				$conditions = array('name' => new MongoRegex("/" .trim($this->request->data['search']) ."/i"));
			} elseif(array_key_exists('start_date', $this->request->data) && !empty($this->request->data['start_date'])) {
				$conditions = array(
					'$where' => "(this.start_date >= new Date('" . $this->request->data['start_date'] . "')) && (this.start_date != new Date('1969'))"
				);
			}else {
				$conditions = array(
					'end_date' => array('$gte' => new MongoDate(strtotime($this->request->data['end_date'])))
				);
			}
			$events = Event::find('all',array(
				'conditions' => $conditions,
				'fields' => array(
					'name' => 1,
					'start_date' => 1,
					'end_date' => 1,
					'blurb' => 1,
					'enabled' => 1,
					'_id' => 1),
				'order' => 'start_date'));
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
		if (!is_dir($git = dirname(LITHIUM_APP_PATH) . '/.git')) {
			return;
		}
		$head = trim(file_get_contents("{$git}/HEAD"));
		$head = explode('/', $head);

		return array_pop($head);
	}

}
