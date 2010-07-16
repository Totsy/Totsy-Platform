<?php

namespace app\models;
use \MongoDate;

class Event extends \lithium\data\Model {

	public $validates = array();
	
	
	protected $_dates = array(
		'now' => 0
	);

	public static function dates($name) { 
	     return new MongoDate(time() + static::_object()->_dates[$name]); 
	}
	/**
	 * Query for all the events within the next 24 hours
	 */
	public static function open($params = null, array $options = array()) {
		$fields = $params['fields'];
		return Event::all(array(
			'conditions' => array(
				'enabled' => true,
				'start_date' => array(
					'$lte' => static::dates('now')),
				'end_date' => array(
					'$gt' => static::dates('now'))),
			'fields' => $fields,
			'order' => array('end_date' => 'ASC') 
		));
	}
	/**
	 * Query for all events that occur after two weeks
	 */
	public static function pending($params = null, array $options = array()) {
		
		return Event::all(array(
			'conditions' => array(
				'enabled' => true,
				'start_date' => array(
					'$gt' => static::dates('now'))),
			'order' => array('start_date' => 'ASC')
		));
	}

}

?>