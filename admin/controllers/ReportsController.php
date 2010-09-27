<?php

namespace admin\controllers;
use admin\controllers\BaseController;
use admin\models\Cart;
use admin\models\User;
use admin\models\Order;
use admin\models\Event;
use admin\models\Item;
use admin\models\Base;
use MongoCode;
use MongoDate;


class ReportsController extends BaseController {

	protected $_purchaseHeading = array(
		'SKU',
		'Product Name',
		'Product Color',
		'Quantity',
		'Size',
		'Unit',
		'Total'
	);

	protected $_clientId = 'TOT';

	protected $_productHeading = array(
		'ClientID',
		'SKU',
		'Description',
		'WhsInsValue (Cost)',
		'ShipInsValue',
		'Expiration_Date',
		'UPC',
		'Description for Customs',
		'HSC Code',
		'Class for LTL',
		'Country of Origin',
		'Velocity',
		'Ref1',
		'Ref2',
		'Ref3',
		'Ref4',
		'Ref5',
		'UOM1',
		'UOM1_Qty',
		'UOM1_Weight',
		'UOM1_Length',
		'UOM1_Width',
		'UOM1_Height',
		'UOM1_Cube'
	);

	protected $_orderHeading = array(
		'Date',
		'ClientId',
		'DC',
		'ShipMethod',
		'RushOrder (Y/N)',
		'OrderNum',
		'SKU',
		'Qty',
		'CompanyOrName',
		'ContactName',
		'Address1',
		'Address2',
		'City',
		'StateOrProvince',
		'Zip',
		'Country',
		'Email',
		'Tel',
		'Customer PO #',
		'Pack Slip Comment',
		'Special Packing Instructions'
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
	/**
	 * The purchases method complies the PO report for the logistics team. This report returns an associative array
	 * which lists all the sales of each item of a sale.
	 *
	 * The order of operation is as follows:
	 *
	 * 1) Find all the event that is being requested via the URL.
	 * 2) Find all the times that are a part of the event requested.
	 * 3) For each item get all the orders that have been placed with that item in it.
	 * 4) Build the array of cumulative purchases for each item of the event.
	 */
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
				foreach ($eventItem['details'] as $key => $value) {
					$orders = Order::find('all', array(
						'conditions' => array(
							'items.item_id' => (string) $eventItem['_id'],
							'items.size' => $key
					)));
					if ($orders) {
						$orderData = $orders->data();
						if (!empty($orderData)) {
							foreach ($orderData as $order) {
								$items = $order['items'];
								foreach ($items as $item) {
									if (($item['item_id'] == $eventItem['_id']) && ($key == $item['size'])){
										$purchaseOrder[$inc]['Product Name'] = $eventItem['description'];
										$purchaseOrder[$inc]['Product Color'] = $eventItem['color'];
										$purchaseOrder[$inc]['SKU'] = $eventItem['vendor_style'];
										$purchaseOrder[$inc]['Unit'] = $eventItem['sale_whol'];
										if (empty($purchaseOrder[$inc]['Quantity'])) {
											$purchaseOrder[$inc]['Quantity'] = $item['quantity'];
										} else {
											$purchaseOrder[$inc]['Quantity'] += $item['quantity'];
										}
										$purchaseOrder[$inc]['Total'] = $purchaseOrder[$inc]['Quantity'] * $eventItem['sale_whol'];
										$purchaseOrder[$inc]['Size'] = $item['size'];
										$purchaseOrder[$inc] = $this->sortArrayByArray($purchaseOrder[$inc], $purchaseHeading);
									}
								}
							}
							if (!empty($purchaseOrder[$inc]['Total'])) {
								$total['sum'] += $purchaseOrder[$inc]['Total'];
								$total['quantity'] += $purchaseOrder[$inc]['Quantity'];
							}
							++$inc;
						}
					}
				}
			}
		}
		return compact('purchaseOrder', 'event', 'total', 'purchaseHeading');
	}

	public function orderFile($eventId = null) {
		if ($eventId) {
			$productHeading = $this->_purchaseHeading;

			$items = Item::find('all', array(
				'conditions' => array(
					'event' => array('$in' => array($eventId)
			))));

			$eventItems = $items->data();

		}
	}
}

?>