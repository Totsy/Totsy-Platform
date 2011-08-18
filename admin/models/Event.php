<?php

namespace admin\models;
use MongoId;
use MongoDate;
use admin\extensions\util\String;
use admin\models\EventImage;

class Event extends \lithium\data\Model {

	public $validates = array();

	public static $tags = array(
		'holiday' => 'holiday',
		'special' => 'special',
		'toys' => 'toys'
	);

	/**
	 * Query for all the events within the next 24 hours.
	 *
	 * @return Object
	 */
	public static function open($params = null, array $options = array()) {
		$fields = $params['fields'];
		return Event::all(compact('fields') + array(
			'conditions' => array(
				'enabled' => true,
				'start_date' => array('$lte' => new MongoDate()),
				'end_date' => array('$gt' => new MongoDate())
			),
			'order' => array('start_date' => 'DESC')
		));
	}

	public static function collection() {
		return static::_connection()->connection->events;
	}

	protected $_booleans = array(
		'enabled',
		'tangible'
		);

	public static function castData($event, array $options = array()) {

		foreach ($event as $key => $value) {
			if (in_array($key, static::_object()->_booleans)) {
				$event[$key] = (boolean) $value;
			}
		}
		return $event;
	}

	public static function removeItems($event) {
		return static::collection()->update(
			array('_id' => new MongoId($event)),
			array('$unset' => array('items' => true)
		));
	}

	/**
	 * Query for all events that are enabled and have a start date
	 * that is greater than "now".
	 *
	 * @return Object
	 */
	public static function pending() {
		return Event::all(array(
			'conditions' => array(
				'enabled' => true,
				'start_date' => array(
					'$gt' => new MongoDate())),
			'order' => array('start_date' => 'ASC')
		));
	}

	public static function poNumber($event) {
		$vendorName = preg_replace('/[^(\x20-\x7F)]*/','', substr(String::asciiClean($event->name), 0, 3));
		$time = date('ymdis', $event->_id->getTimestamp());
		return 'TOT'.'-'.$vendorName.$time;
	}

	/* Handling of attached images. */

	public function attachImage($entity, $name, $id) {
		$id = (string) $id;
		$type = EventImage::$types[$name];

		$images = $entity->images ? $entity->images->data() : array();
		$images[$type['field']] = $id;

		$entity->images = $images;

		return $entity;
	}

	public function detachImage($entity, $name, $id) {
		$id = (string) $id;
		$type = EventImage::$types[$name];

		$images = $entity->images->data();
		$images[$type['field']] = null;

		$entity->images = $images;

		return $entity;
	}

	public function images($entity) {
		$results = array();

		foreach ($entity->images as $name => $id) {
			$results[$name] = EventImage::first(array(
				'conditions' => array('_id' => $id)
			));
		}
		return $results;
	}

	public function uploadNames($entity) {
		$results = array();

		foreach (EventImage::$types as $name => $type) {
			$results['form'][$name] = String::insert($type['uploadName']['form'], array(
				'url' => $entity->url,
				'name' => $name
			));
			$results['dav'][$name] = String::insert($type['uploadName']['dav'], array(
				'event' => $entity->url,
				'name' => $name,
				'file' => 'example',
				'month' => date('n', $entity->start_date->sec),
				'year' => date('Y', $entity->start_date->sec)
			));
		}
		return $results;
	}
}

?>