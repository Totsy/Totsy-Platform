<?php

namespace app\models;
use \MongoDate;

class Event extends \lithium\data\Model {

	public $validates = array();
	
	
	protected $_dates = array(
		'now' => 0,
		'tomorrow' => 86400,
		'twodays' => 172800,
		'twoweeks' => 604800
	);

	public static function dates($name) { 
	     return new MongoDate(time() + static::_object()->_dates[$name]); 
	}
	/**
	 * Query for all the events within the next 24 hours
	 */
	public static function today($params = null, array $options = array()) {
		$fields = $params['fields'];
		return Event::all(array(
			'conditions' => array(
				'enabled' => '1',
				'end_date' => array(
					'$gt' => static::dates('now'),
					'$lt' => static::dates('tomorrow'))),
			'fields' => $fields 
			));
	}
	/**
	 * Query for events that occur between tomorrow and in two weeks
	 */
	public static function current($params, array $options = array()) {
		$fields = $params['fields'];
		
		return Event::all(array(
			'conditions' => array(
				'enabled' => '1',
				'end_date' => array(
					'$gt' => static::dates('tomorrow'),
					'$lt' => static::dates('twoweeks')),
			'fields' => $fields
		)));
	}
	/**
	 * Query for all events that occur after two weeks
	 */
	public static function future($params, array $options = array()) {
		$fields = $params['fields'];
		
		return Event::all(array(
			'conditions' => array(
				'enabled' => '1',
				'end_date' => array(
					'$gt' => static::dates('twoweeks')),
			'fields' => $fields
		)));
	}

}

?>