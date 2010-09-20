<?php

namespace admin\controllers;
use admin\models\Order;
use admin\models\User;
use admin\models\Event;
use admin\models\Item;
use admin\models\Credit;
use MongoDate;
use MongoRegex;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use admin\extensions\Mailer;
use \li3_flash_message\extensions\storage\FlashMessage;


class OrdersController extends \lithium\action\Controller {

	public function index() {
		FlashMessage::clear();
		if ($this->request->data) {
			switch ($this->request->data['type']) {
				case 'date':
					if (!empty($this->request->data['min_date'])) {
						$minDate = new MongoDate(strtotime($this->request->data['min_date']));
						$maxDate = new MongoDate(strtotime($this->request->data['max_date']));
						$conditions = array(
							'date_created' => array(
								'$lte' => $maxDate, 
								'$gte' => $minDate
						));
						$rawOrders = Order::find('all', array('conditions' => $conditions));
					}
					break;
				case 'order':
					if (!empty($this->request->data['order_id'])) {
						$orderid = $this->request->data['order_id'];
						$order = new MongoRegex("/$orderid/i");
						$rawOrders = Order::find('all', array('conditions' => array('order_id' => $order)));
					}
					break;
				case 'user':
						$rawOrders = Order::findUserOrder($this->request->data);
					break;
				case 'event':
					if (!empty($this->request->data['event_name'])) {
						$eventName = $this->request->data['event_name'];
						$eventName = new MongoRegex("/$eventName/i");
						$rawOrders = Order::find('all', array('conditions' => array('items.event_name' => $eventName)));
					}
					break;
			}

			if ($rawOrders) {
				$headings = array('date_created','order_id', 'Event Name', 'billing', 'shipping','total', 'Customer Profile');
				if (get_class($rawOrders) == 'MongoCursor') {
					foreach ($rawOrders as $order) {
						var_dump($order);
						FlashMessage::set('Results Found', array('class' => 'pass'));
					}
				} else {
					$details = $rawOrders->data();
					foreach ($details as $order) {
						FlashMessage::set('Results Found', array('class' => 'pass'));
						$orders[] = $this->sortArrayByArray($order, $headings);
					}
				}
				if (empty($order)) {
					FlashMessage::set('No Results Found', array('class' => 'warning'));
				}
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

	public function view($id = null) {
		$this->_render['layout'] = 'base';
		$order = null;
		if ($id) {
			$order = Order::find('first', array('conditions' => array('_id' => $id)));
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
					$user = User::find('first', array(
						'conditions' => array(
							'email' => $shipRecord['Email']
					)));
					$order = Order::lookup(substr($shipRecord['OrderNum'], 0, 8), (string) $user->_id);
					if ($order && !empty($order->items)) {
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
						}
						$order->ship_method = $shipRecord['ShipMethod'];
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