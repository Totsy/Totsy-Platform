<?php

namespace app\models;
use MongoDate;
use MongoId;
use app\models\Base;
use app\models\Item;

/**
* The Event Class
*
* Controls all the model methods needed to interact with an Event object.
* An event is an element that groups items together and represents a "sale"
* on the website.
* Each event has its own images that is displayed on the main page. Each event is open for
* a short duration specified by the start_date and end_date.
*
* Events have the following document structure in Mongo:
* {{{
*    "_id" : ObjectId("4c3fd068ce64e5e475270000"),
*    "blurb" : "<p>Event Blurb</p>,
*    "created_date" : ISODate("2010-07-16T03:22:16.120Z"),
*    "enable_items" : "0",
*    "enabled" : false,
*    "end_date" : ISODate("2010-08-03T10:00:23Z"),
*    "event_image" : "4c409564ce64e5e275310100",
*    "images" : {
*        "logo_image" : "4c8f867bce64e53150db0500"
*    },
*    "items" : [
*        "4c409c6cce64e5e175470100",
*        "4c409c6cce64e5e175480100",
*        "4c409c6cce64e5e1754a0100",
*    ],
*    "logo_image" : "4c8f867bce64e53150db0500",
*    "name" : "CachCach",
*    "splash_big_image" : "4c409563ce64e5e175e30000",
*    "splash_small_image" : "4c409564ce64e5e475630000",
*    "start_date" : ISODate("2010-07-19T21:00:00Z"),
*    "url" : "cachcach"
* }}}"
*
*    * blurb - Any copy that will be displayed on the event index page
*    * event_image - ObjectId of the gridfs image.
*    * items - An array of all the products that are tied to this single event.
*    * name - Text that is used as a reference for the event.
*    * splash_big_image - ObjectId of the gridfs image.
*    * splash_small_image - ObjectId of the gridfs image.
*    * splash_small_image - ObjectId of the gridfs image.
*    * url - The url of the event that is used as a lookup field.
*/
class Event extends Base {

	protected $_meta = array('source' => 'events');
	public $validates = array();

	private static $_mapCategories = array (
		'category' =>  array(
			'all' => "All",
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
			'all' => 'All',
			'newborn' => 'Newborn 0-6M',
			'infant' => 'Infant 6-24M',
			'toddler' => 'Toddler 1-3 Y',
			'preschool' => 'Preschool 3-4Y',
			'school' => 'School Age 5+',
			'adult' => 'Adult'
		)
	);
	
	
	/**
	 * Query for all the events within the next 24 hours.
	 *
	 * @return Object
	 */

	public static function open($params = null, array $options = array(), $departments = null, $categories = null, $ages = null) {
		$fields = $params['fields'];
		$events = Event::all(compact('fields') + array(
			'conditions' => array(
				'enabled' => true,
				'start_date' => array('$lte' => new MongoDate()),
				'end_date' => array('$gt' => new MongoDate())
			),
			'order' => array('start_date' => 'DESC')
		));
		//Filter events results if settled
		if(!empty($departments)||!empty($categories)||!empty($ages)){
			$itemsCollection = Item::collection();
			foreach($events as $key_event =>$event) {
				$events_id[] = (string) $event["_id"];
			}

			$items = Item::filter($events_id, $departments, $categories, $ages);
			$events_id_filtered = array();
			if(!empty($items)) {
				foreach($items as $item) {
					foreach($item["event"] as $event_id) {
						$events_id_filtered[] = $event_id;
					}
				}
			}
			$events_id_filtered = array_unique($events_id_filtered);
			//if(!empty($events_id_filtered)) {
				$events = Event::all(compact('fields') + array(
					'conditions' => array(
						'_id' => array('$in' => $events_id_filtered),
						'enabled' => true,
						'start_date' => array('$lte' => new MongoDate()),
						'end_date' => array('$gt' => new MongoDate())
					),
					'order' => array('start_date' => 'DESC')
				));
			//}
		}
		return $events;
	}

	/**
	 * Query for all events that are enabled and have a start date
	 * that is greater than "now".
	 *
	 * @return Object
	 */
	public static function pending($params = null, array $options = array(), $departments = null) {
		$events = Event::all(array(
			'conditions' => array(
				'enabled' => true,
				'start_date' => array(
					'$gt' => new MongoDate())),
			'order' => array('start_date' => 'ASC')
		));
		//Filter events results if settled
		if(!empty($departments)){
			$itemsCollection = Item::collection();
			foreach($events as $key_event =>$event) {
				$events_id[] = (string) $event["_id"];
			}
			$events_id_filtered = array();
			if(!empty($events_id)) {
				$items = $itemsCollection->find(array('event' => array('$in' => $events_id), 'departments' => array('$in' => array($departments))), array('event' => 1));
				foreach($items as $item) {
					foreach($item["event"] as $event_id) {
						$events_id_filtered[] = $event_id;
					}
				}
			}
			$events_id_filtered = array_unique($events_id_filtered);
			$events = Event::all(array(
				'conditions' => array(
					'_id' => array('$in' => $events_id_filtered),
					'enabled' => true,
					'start_date' => array(
						'$gt' => new MongoDate())),
				'order' => array('start_date' => 'ASC')
			));
		}
		return $events;
	}

	public static function directQuery(array $args = array()){
		$connection = self::connection()->connection->events;

		$cursor = $connection->find($args);
		$return = array();
		foreach ($cursor as $data){
			if (array_key_exists('_id',$data)){
				$data['_id'] = (string) $data['_id'];
			}
			if (array_key_exists('start_date',$data) ){
				$data['start_date'] = array(
					'sec' => $data['start_date']->sec,
					'usec' => $data['start_date']->usec
				);
			}
			if (array_key_exists('end_date',$data)){
				$data['end_date'] = array(
					'sec' => $data['end_date']->sec,
					'usec' => $data['end_date']->usec
				);
			}
			$return[] = $data;
		}
		unset($cursor,$data,$connection);

		return $return;
	}
	
	public static function mapCat2Url($category,$name){
		$map = array_flip(self::$_mapCategories[$category]);
		return $map[$name];
	}
	
	public static function mapUrl2Cat($category,$name){
		return self::$_mapCategories[$category][$name];
	}
	
	public static function mapCat($category){
		return self::$_mapCategories[$category];
	}
}

?>