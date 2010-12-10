<?php

namespace admin\controllers;

use admin\models\Order;
use admin\models\User;
use admin\models\Event;
use admin\models\Item;
use admin\models\Credit;
use admin\controllers\BaseController;
use MongoDate;
use MongoRegex;
use MongoId;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use li3_flash_message\extensions\storage\FlashMessage;
use li3_silverpop\extensions\Silverpop;

/**
 * The Orders Controller
 *
 **/
class OrdersController extends BaseController {

	/**
	 * These headings are used in the datatable index view.
	 * @var array
	 */
	protected $_headings = array(
		'Date Created',
		'Order ID',
		'AuthKey',
		'Event Name',
		'Billing Info',
		'Shipping Info',
		'Order Cost',
		'Tracking Info',
		'Estimated Ship Date',
		'Customer Profile'
	);

	/**
	 * The # of business days to be added to an event to determine the estimated
	 * ship by date. The default is 18 business days.
	 *
	 * @var int
	 **/
	protected $_shipBuffer = 18;

	/**
	 * Any holidays that need to be factored into the estimated ship date calculation.
	 *
	 * @var array
	 */
	protected $_holidays = array('2010-11-25', '2010-11-26');

	/**
	 * Main view to query for orders in the admin screen.
	 *
	 * @return object of orders and array of headings for view.
	 */
	public function index() {
		$headings = $this->_headings;
		FlashMessage::clear();
		$collection = Order::collection();
		if ($this->request->data) {
			$search = $this->request->data['search'];
			$searchType = $this->request->data['type'];
			$date = array('date_created' => array('$gt' => new MongoDate(strtotime('August 3, 2010'))));
			if (!empty($search)) {
				switch ($searchType) {
					case 'order':
						$order = new MongoRegex("/$search/i");
						$rawOrders = $collection->find(array('order_id' => $order) + $date);
						break;
					case 'address':
							$rawOrders = Order::orderSearch($search, 'address');
						break;
					case 'event':
						$eventName = new MongoRegex("/$search/i");
						$rawOrders = $collection->find(array('items.event_name' => $eventName) + $date);
						break;
					case 'authKey':
						$authKey = new MongoRegex("/$search/");
						$rawOrders = $collection->find(array('authKey' => $authKey) + $date);
						break;
					case 'item':
							$item = new MongoRegex("/$search/i");
							$rawOrders = $collection->find(array('items.description' => $item) + $date);
						break;
					case 'name':
							$rawOrders = Order::orderSearch($search, 'name');
						break;
					case 'email':
						$users = User::find('all', array(
							'conditions' => array('email' => new MongoRegex("/$search/i")
						)));
						if (!empty($users)) {
							$users = $users->data();
							foreach ($users as $user) {
								$_id[] = $user['_id'];
							}
							$rawOrders = $collection->find(array('user_id' => array('$in' => $_id)) + $date);
						}
						break;
				}
			}
			if (!empty($rawOrders)) {
				if (get_class($rawOrders) == 'MongoCursor') {
					foreach ($rawOrders as $order) {
						FlashMessage::set("Results found for $searchType search of $search", array('class' => 'pass'));
						$orders[] = $this->sortArrayByArray($order, $headings);
						$shipDate["$order[_id]"] = $this->shipDate($order);
					}
				}
				if (empty($order)) {
					FlashMessage::set("No results found for $searchType search of $search", array('class' => 'warning'));
				}
			}
		}

		return compact('orders', 'headings', 'shipDate');
	}

	/**
	 * The view method renders the order confirmation page that is sent to the customer
	 * after they have placed their order
	 *
	 * @param string $id The _id of the order
	 */
	public function view($id = null) {
		$this->_render['layout'] = 'base';
		$order = null;
		if ($id) {
			$order = Order::find('first', array('conditions' => array('_id' => $id)));
				$orderData = $order->data();
				$orderItems = $orderData['items'];
				foreach ($orderItems as $orderItem) {
					$item = Item::find('first', array(
						'conditions' => array('_id' => $orderItem['item_id']
					)));
					$sku["$orderItem[item_id]"] = $item->vendor_style;
				}
		}
		$shipDate = $this->shipDate($order);
		return compact('order', 'shipDate', 'sku');
	}

	/**
	 * The update method captures payment and updates the order with tracking info.
	 */
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
			$sendEmail = (boolean) $this->request->data['send_email'];
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
					$order = Order::lookup(substr($shipRecord['OrderNum'], 0, 8));
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
						$user = User::find('first', array('condition' => array('_id' => $order->user_id)));
						if (empty($trackingNum) && $sendEmail) {
							$data = array(
								'order' => $order,
								'email' => $shipRecord['Email'],
								'details' => $details
							);
							Silverpop::send('orderShipped', $data);
						}
						if (Order::setTrackingNumber($order->order_id, $shipRecord['Tracking #'])){
							if (empty($order->auth_confirmation)) {
								if ($order->process() && $user->purchase_count == 1) {
									if ($user->invited_by) {
										$inviter = User::find('first', array(
											'conditions' => array(
												'invitation_code' => $user->invited_by
										)));
										if ($inviter) {
											$credit = Credit::create();
											$data = array(
												'user_id' => (string) $inviter->_id,
												'sign' => '+',
												'amount' => Credit::INVITE_CREDIT
											);
											User::applyCredit($data);
											$data = array(
												'reason' => "Invitation Credit",
												'sign' => "+",
												'amount' => Credit::INVITE_CREDIT,
												'description' => null,
												'user_id' => $inviter->_id
											);
											Credit::add($credit, $data);
										}
									}
								}
							} else {
								$order->save();
							}
						}
						$details['Confirmation Number'] = $order->auth_confirmation;
						$details['Errors'] = $order->auth_error;
						$updated[] = $details;
					}
				}
			}
		}
		return compact('updated');
	}

	/**
	 * Calculated estimated ship by date for an order.
	 *
	 * The estimated ship-by-date is calculated based on the last event that closes.
	 * @param object $order
	 * @return string
	 */
	public function shipDate($order) {
		$i = 1;
		$shipDate = null;
		$items = (is_object($order)) ? $order->items->data() : $order['items'];
		if (!empty($items)) {
			foreach ($items as $item) {
				if (!empty($item['event_id'])) {
					$ids[] = new MongoId($item['event_id']);
				}
			}
			if (!empty($ids)) {
				$event = Event::find('first', array(
					'conditions' => array('_id' => $ids),
					'order' => array('date_created' => 'DESC')
				));
				$shipDate = $event->end_date->sec;
				while($i < $this->_shipBuffer) {
					$day = date('N', $shipDate);
					$date = date('Y-m-d', $shipDate);
					if ($day < 6 && !in_array($date, $this->_holidays)){
						$i++;
					}
					$shipDate = strtotime($date.' +1 day');
				}
			}
		}

		return $shipDate;
	}
}
?>