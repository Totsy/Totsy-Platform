<?php

namespace admin\controllers;
use admin\models\Cart;
use admin\models\User;
use admin\models\Order;
use admin\models\Event;
use admin\models\Item;
use admin\models\Base;
use MongoCode;
use MongoDate;


class ReportsController extends \lithium\action\Controller {

	protected $_purchaseHeading = array(
		'SKU',
		'Product Name',
		'Product Color',
		'Quantity',
		'Size',
		'Unit',
		'Total'
	);
	public function index() {

	}

	public function cart() {
		$this->_render['layout'] = false;
		$y = Cart::count();
		$x = time() * 1000;
		echo "[$x, $y]";
	}

	public function affiliate() {
		$orderTotals = null;
		if ($this->request->data) {
			$affiliate = $this->request->data['affiliate'];
			$min = new MongoDate(strtotime($this->request->data['min_date']));
			$max = new MongoDate(strtotime($this->request->data['max_date']));
			$total = 0;
			$date = array(
				'date_created' => array(
					'$gt' => $min,
					'$lt' => $max)
			);
			switch ($affiliate) {
				case 'trendytogs':
					$conditions = array(
						'trendytogs_signup' => array('$exists' => true),
						"purchase_count" => array('$gte' => 1)
					);
					break;
				default:
					$conditions = array(
						'invited_by' => $affiliate,
						'purchase_count' => array('$gte' => 1)
					);
					break;
			}
			$users = User::find('all', array('conditions' => $conditions));
			$userId = array();
			foreach ($users as $user) {
				$userId[] = (string) $user->_id;
			}
			$keys = new MongoCode('function(doc){
				return {
					Date: doc.date_created.toDateString()
					}
			}');
			$inital = array('total' => 0);
			$reduce = new MongoCode('function(doc, prev){prev.total += doc.total;}');
			$condition = $date + array('user_id' => array('$in' => $userId));

			$collection = Order::collection();
			$retvals = $collection->group($keys, $inital, $reduce, $condition);
			$sum = null;
			$orderTotals = $retvals['retval'];
			if ($orderTotals) {
				foreach ($orderTotals as $totals) {
					$sum += $totals['total'];
				}
			}
		}

		return compact('orderTotals', 'sum');
	}

	public function logistics($event = null) {
		$events = Event::all();
		return compact('events', 'items');
	}
	
	public function purchases($eventId = null) {
		if ($eventId) {
			$purchaseHeading = $this->_purchaseHeading;
			$total = array('sum' => 0, 'quantity' => 0);
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			$items = Item::find('all', array(
				'conditions' => array(
					'event' => array('$in' => array($eventId)
			))));
			$eventItems = $items->data();
			$inc = 0;
			foreach ($eventItems as $eventItem) {
				$orders = Order::find('all', array(
					'conditions' => array(
						'items.item_id' => (string) $eventItem['_id']
				)));
				if ($orders) {
					$orderData = $orders->data();
					if (!empty($orderData)) {
						$purchaseOrder[$inc]['SKU'] = $eventItem['vendor_style'];
						$purchaseOrder[$inc]['Product Name'] = $eventItem['description'];
						$purchaseOrder[$inc]['Product Color'] = $eventItem['color'];
						$purchaseOrder[$inc]['Quantity'] = 0;
						foreach ($orderData as $order) {
							$items = $order['items'];
							foreach ($items as $item) {
								if (($item['item_id'] == $eventItem['_id'])) {
									$purchaseOrder[$inc]['Quantity'] += $item['quantity'];
								}
								$purchaseOrder[$inc]['Size'] = $item['size'];
								$purchaseOrder[$inc]['Unit'] = $eventItem['sale_whol'];
							}
							$purchaseOrder[$inc]['Total'] = $purchaseOrder[$inc]['Quantity'] * $eventItem['sale_whol'];
						}
						if (!empty($purchaseOrder[$inc]['Total'])) {
							$total['sum'] += $purchaseOrder[$inc]['Total'];
							$total['quantity'] += $purchaseOrder[$inc]['Quantity'];
						}
					}
				}
				++$inc;
			}
		}
		return compact('purchaseOrder', 'event', 'total', 'purchaseHeading');
	}

}

?>