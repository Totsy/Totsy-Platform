<?php

namespace app\models;
use MongoDate;

/**
* The Event Class.
*
* Controls all the model methods needed to interact with an Event.
* The Event represents the main component of the platform. An event contains a list
* of events that are associated with it.
*
* Events have the following document structure in Mongo:
* {{{
*	{
*		"_id" : ObjectId("4c3fd068ce64e5e475270000"),
*		"blurb" : "<p>Event Blurb</p>
*		"created_date" : ISODate("2010-07-16T03:22:16.120Z"),
*		"enable_items" : "0",
*		"enabled" : false,
*		"end_date" : ISODate("2010-08-03T10:00:23Z"),
*		"event_image" : "4c409564ce64e5e275310100",
*		"images" : {
*			"logo_image" : "4c8f867bce64e53150db0500"
*		},
*		"itemTable_length" : "10",
*		"items" : [
*			"4c409c6cce64e5e175470100",
*			"4c409c6cce64e5e175480100",
*			"4c409c6cce64e5e1754a0100",
*			"4c409c6cce64e5e1754b0100",
*			"4c409c6cce64e5e1754c0100",
*			"4c409c6cce64e5e1754d0100",
*			"4c409c6cce64e5e1754e0100",
*			"4c409c6cce64e5e1754f0100",
*			"4c409c6cce64e5e175500100",
*			"4c409c6cce64e5e175490100"
*		],
*		"logo_image" : "4c8f867bce64e53150db0500",
*		"name" : "CachCach",
*		"splash_big_image" : "4c409563ce64e5e175e30000",
*		"splash_small_image" : "4c409564ce64e5e475630000",
*		"start_date" : ISODate("2010-07-19T21:00:00Z"),
*		"url" : "cachcach"
*	}
* }}}
*/
class Event extends \lithium\data\Model {

	public $validates = array();

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

}

?>