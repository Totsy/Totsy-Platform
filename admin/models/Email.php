<?php

namespace admin\models;

use admin\models\Base;

/**
 * The Email Model links directly with the emails MongoDB Collection.
 * 
 * The emails collection contains documents that have all the general transactional
 * emails that are sent manually by an admin. These are to serve only as a template.
 * Here is an example of the schema:
 * 
 * {{{
 * 
 * {
 * 		"name": "order_placed",
 * 		"created_date" : MongoDate,
 * 		"subject": "Subject"
 * 		"body" : "HTML Contents",
 * 		"updated_date" : MongoDate,
 * 		"enabled": Boolean
 * }
 * 
 * }}}
 */
class Email extends Base {

	public $validates = array();

	protected $_meta = array('source' => 'emails');

	public static $templates = array(
		'781191' => 'Order Enroute Warehouse - Normal',
		'781192' => 'Order Enroute Warehouse - Long',
		'781193' => 'Order Enroute Warehouse - Delayed',
		'781203' => 'Order At Warehouse',
		'781194' => 'Apply Credit',
		'781195' => 'Order Issue'
	);
}

?>