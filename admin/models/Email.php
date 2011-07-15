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

	protected $_meta = array('source' => 'log.emails');

	public static $templates = array(
		'orderStatusNormal' => 'Order Enroute Warehouse - Normal',
		'orderStatusLong' => 'Order Enroute Warehouse - Long',
		'orderStatusDelayed' => 'Order Enroute Warehouse - Delayed',
		'orderWarehouse' => 'Order At Warehouse',
		'delayedEmail' => 'Delayed Email (general)',
		'orderCredit' => 'Apply Credit',
		'orderIssue' => 'Order Issue',
		'Account_Notification' => 'Account Notification'
	);
}

?>