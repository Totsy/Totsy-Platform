<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace admin\extensions\command;

use li3_silverpop\extensions\Silverpop;
//require('/Users/Mythos/Sites/totsy_local/admin/models/Order.php');
use admin\models\Order;

/**
 * The `EventStatus` class finds all the events that have just closed
 * and sends an email to 
 */
class EventStatus extends \lithium\console\Command {

	public function run() {

		$this->header('Test');
		$this->out('Processing Order');
		
		$order = Order::find('first', array('conditions' => array('_id' => '4c991893ce64e5c10fce0500')));
		$data = array(
			'email' => 'fagard@totsy.com',
			'order' => $order
		);
		var_dump(class_exists("admin\models\Order"));
//		Silverpop::send('orderConfirmation', $data);
		$this->out('Sent Email');
	}

}
