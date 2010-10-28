<?php

namespace admin\extensions\command;

use li3_silverpop\extensions\Silverpop;
use admin\models\Order;


/**
 * The `EventStatus` class finds all the events that have just closed and emails the customer.
 */
class EventStatus extends \lithium\console\Command {

	public function run() {

		$this->header('Testing Model');

		$order = Order::find('first', array('conditions' => array('_id' => '4c991893ce64e5c10fce0500')));

		$this->out('Whooohooo');
		$data = array(
			'email' => 'fagard@totsy.com',
			'order' => $order
		);
		Silverpop::send('test', $data);
		$this->out('This is working');
	}

}

?>