<?php

namespace admin\extensions\command;

use admin\models\Cart;
use admin\models\User;
use admin\models\Order;
use admin\models\Event;
use admin\models\Item;
use admin\models\Report;
use lithium\core\Environment;
use Mongo;
use MongoCode;
use MongoDate;
use MongoRegex;
use \lithium\data\Model;
use lithium\util\String;

/**
 * Gathers orders details based on promocode for reporting analysis.
 *
 * This script was an adhoc request to gather details of how many times customers used
 * three specific promocodes. All the orders for these three promocodes were gathered
 * and stored in the Reports collection. Using an upsert based on finding the user
 * and promocode allows a document to show repeat use. Therefore, if a customer used
 * the same promocode more than once the `promo_code` counter field would be incremented.
 * After gathering the data the following javascript group command can be executed with
 * variations for summary results:
 * 
 * {{{
 *    db.reports.group(
 *        {
 *            keyf: function(doc){
 *	              return {
 *	                  "PromoCode": doc.promo_id,
 *                    "Times Promo Used" : doc.promo_count
 *	              }
 *           },
 *           initial : {total:0}, 
 *           reduce: function(doc, prev){prev.total += 1;},
 *           cond: {
 *                    "entitled": false,
 *                    "promo_id": '15CREDIT'
 *           }
 *    }
 * )
 * }}}
 *
 * The `entitled` field is a boolean that is set if a customer was or was not entitled 
 * to use the promotion. If there is other data in the reports collection include as a
 * condition the hash that was printed on the console during runtime.
 */
class PromoAnalysis extends \lithium\console\Command  {

	/**
	 * The environment to use when running the command. db='development' is the default.
	 * Set to 'production' if running live when using a cronjob.
	 */
	public $env = 'development';

	/**
	 * Find all the orders matching the $promoIds key array and aggregate
	 * the number of times each customer used that promo when placing an order.
	 * All results are stored in the Reports Collection.
	 */
	public function run() {
		Environment::set($this->env);
		$collection = Report::collection();
		$hash = String::uuid($_SERVER);
		$this->out("Generating PromoAnalysis report with hash: $hash");
		$promoIds = array(
			'15CREDIT' => '4d6e73db12d4c9c64e0040e8',
			'TENOFF' => '4d6e73b812d4c9cd4c005831',
			'5OFF' => '4d6e738c12d4c9925000632f'
		);
		$list = array();
		foreach ($promoIds as $key => $value) {
			$conditions = array('promo_code' => new MongoRegex("/$key/i"));
			$orders = Order::collection()->find($conditions);
			foreach ($orders as $order) {
				$eventIds = array();
				$eventNames = array();
				foreach ($order['items'] as $item) {
					$eventIds[] = $item['event_id'];
					$eventNames[] = $item['event_name'];
					if (!in_array($item['event_id'], $list)) {
						$list[] = $item['event_id'];
					}
				}
				$user = User::find($order['user_id']);
				if ($user->promotions && in_array($value, $user->promotions->data())) {
					$entitled = true;
				} else {
					$entitled = false;
				}
				$find = array(
					'user_id' => (string) $user->_id,
					'promo_id' => $key
				);
				$set = array(
					'$set' => array(
						'user_id' => (string) $user->_id,
						'email' => $user->email,
						'report_id' => $hash,
						'entitled' => $entitled,
						'shipping' => $order['shipping']['address'],
						'billing' => $order['billing']['address']
						),
					'$inc' => array(
						'promo_count' => 1),
					'$pushAll' => array(
						'event_ids' => $eventIds,
						'event_names' => $eventNames
				));
				$collection->update($find, $set, array('upsert' => true));
			}
		}
	}

}