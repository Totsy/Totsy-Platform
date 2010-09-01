<?php

namespace admin\controllers;
use admin\models\Order;
use admin\models\User;
use admin\models\Event;
use admin\models\Item;
use admin\models\Credit;
use MongoDate;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use admin\extensions\Mailer;


class OrdersController extends \lithium\action\Controller {

	public function index() {

		if ($this->request->data) {
			$minDate = new MongoDate(strtotime($this->request->data['min_date']));
			$maxDate = new MongoDate(strtotime($this->request->data['max_date']));
			$rawOrders = Order::find('all',array(
				'conditions' => array(
					'date_created' => array('$lte' => $maxDate, '$gte' => $minDate))
			));
			$headings = array('date_created','order_id', 'billing', 'shipping','total');
			$details = $rawOrders->data();
			foreach ($details as $order) {
				$orders[] = $this->sortArrayByArray($order, $headings);
			}
		}

		return compact('orders', 'headings');
	}
	
	public function sortArrayByArray($array, $orderArray) {
	    $ordered = array();
	    foreach($orderArray as $key) {
	        if(array_key_exists($key,$array)) {
	                $ordered[$key] = $array[$key];
	                unset($array[$key]);
	        }
	    }
	    return $ordered + $array;
	}

	public function view($order_id = null) {
		$order = null;
		if ($order_id) {
			$order = Order::lookup($order_id);
		}
		if ($this->request->data) {
			$order =  Order::lookup($this->request->data['order_id']);
		}
		return compact('order');
	}

	public function update() {
		$_shipToHeaders = array(
			'ShipDate',
			'OrderNum',
			'ShipMethod',
			'Tracking #',
			'Cost',
			'SKU',
			'Email'
		);
		if ($this->request->data) {
			if ($_FILES['upload']['error'] == 0) {
				$file = $_FILES['upload']['tmp_name'];
				$objReader = PHPExcel_IOFactory::createReaderForFile("$file");
				$objPHPExcel = $objReader->load("$file");
				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					for ($row = 1; $row <= $highestRow; ++ $row) {
						for ($col = 0; $col < $highestColumnIndex; ++ $col) {
							$cell = $worksheet->getCellByColumnAndRow($col, $row);
							$val = $cell->getValue();
							if ($row == 1) {
								$heading[] = $val;
							} else {
								if (in_array($heading[$col], $_shipToHeaders) && ($val != null)) {
									$shipRecords[$row - 1][$heading[$col]] = $val;
								}
							}
 						}
 					}
				}
			}
			if ($shipRecords) {
				$updated = array();
				foreach ($shipRecords as $shipRecord) {
					$checkedItems = array();
					$order = Order::lookup($shipRecord['OrderNum']);
					if ($order) {
						$user = User::find('first', array(
							'conditions' => array(
								'_id' => $order->user_id
						)));
						$item = Item::find('first', array(
							'conditions' => array(
								'vendor_style' => $shipRecord['SKU']
						)));
						if ($item) {
							$itemId = (string) $item->_id;
							$orderData = $order->data();
							foreach ($orderData['items'] as $orderItem) {
								if ($orderItem['item_id'] == $itemId) {
									$orderItem['status'] = "Order Shipped";
									$orderItem['tracking_number'] = $shipRecord['Tracking #'];
								}
								$checkedItems[] = $orderItem;
							}
							$order->items = $checkedItems;
							$order->ship_method = $shipRecord['ShipMethod'];
						}
						$details = array(
							'Order' => $order->order_id,
							'SKU' => $shipRecord['SKU'],
							'First Name' => $order->shipping->firstname,
							'Last Name' => $order->shipping->lastname,
							'Ship Method' => $order->ship_method,
							'Tracking Number' => $shipRecord['Tracking #']
						);
						$trackingNum = Order::find('first', array(
							'conditions' => array(
								'tracking_numbers' => $shipRecord['Tracking #']
						)));
						if (empty($trackingNum)) {
							Mailer::send(
								'shipped',
								"Totsy - Shipping Notification - $order->order_id",
								array('name' => $order->firstname, 'email' => $shipRecord['Email']),
								compact('order', 'details')
							);
						}
						if(Order::setTrackingNumber($order->order_id, $shipRecord['Tracking #'])){
							if (empty($order->auth_confirmation)) {
								if ($order->process() && $user->purchase_count == 1) {
									if ($user->invited_by) {
										$credit = Credit::create();
										User::applyCredit($user->invited_by, Credit::INVITE_CREDIT);
										Credit::add($credit, $user->invited_by, Credit::INVITE_CREDIT, "Invitation");
									}
								}
							} else {
								$order->save();
							}
						}
						$details['Confirmation Number'] = $order->auth_confirmation;
						$updated[] = $details;
					}
				}
			}
		}

		return compact('updated');
	}
}
?>