<?php

namespace admin\controllers;

use admin\models\Order;
use admin\models\User;
use admin\models\Event;
use admin\models\Item;
use admin\models\Credit;
use admin\models\Promocode;
use admin\controllers\BaseController;
use lithium\storage\Session;
use MongoCode;
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
		$collection = Order::collection();
		if ($this->request->data) {
			$search = $this->request->data['search'];
			$searchType = $this->request->data['type'];
			$date = array('date_created' => array('$gt' =>
				new MongoDate(strtotime('August 3, 2010'))));
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
						$rawOrders = $collection->find(array('items.event_name' => $eventName)
						+ $date);
						break;
					case 'authKey':
						$authKey = new MongoRegex("/$search/");
						$rawOrders = $collection->find(array('authKey' => $authKey) + $date);
						break;
					case 'item':
						$item = new MongoRegex("/$search/i");
						$rawOrders = $collection->find(array('items.description' => $item)
						+ $date);
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
							$rawOrders = $collection->find(array('user_id' => array('$in' => $_id))
							+ $date);
						}
						break;
				}
			}
			if (!empty($rawOrders)) {
				if (get_class($rawOrders) == 'MongoCursor') {
					foreach ($rawOrders as $order) {
						FlashMessage::set("Results found for $searchType search of $search",
							array('class' => 'pass'));
						$orders[] = $this->sortArrayByArray($order, $headings);
						$shipDate["$order[_id]"] = $this->shipDate($order);
					}
				}
				if (empty($order)) {
					FlashMessage::set("No results found for $searchType search of $search",
						array('class' => 'warning'));
				}
			}
		}

		return compact('orders', 'headings', 'shipDate');
	}

	/**
	* The cancel method close an order or unclose it
	* by modfiying the calling the Order cancel method
	*/
	public function cancel() {
		$current_user = Session::read('userLogin');
		$orderCollection = Order::collection();
		$datas = $this->request->data;
		if ($datas["id"]) {
			$status = Order::cancel($datas["id"], $current_user["email"], $datas["comment"]);
		}
	}

	/**
	* The manage_items method update the temporary order.
	* If the variable save is set to true, it apply the changes.
	*/
	public function manage_items() {
		$current_user = Session::read('userLogin');
		$orderCollection = Order::collection();
		$userCollection = User::collection();
		if($this->request->data){
			$datas = $this->request->data;
			$selected_order = $orderCollection->findOne(array("_id" => new MongoId($datas["id"])));
			$datas["user_id"] = $selected_order["user_id"];
			$items = $selected_order["items"];
			foreach($datas["items"] as $key => $item) {
				//Quantity
				$items[$key]["initial_quantity"] = $item["initial_quantity"];
				$items[$key]["quantity"] = $item["quantity"];
				//Cancel Status
				if(empty($item["cancel"])){
					$cancelCheck = false;
					$cancelEmptyCheck = false;
				} else {
					$cancelCheck = ($item["cancel"] == "true" ||  $item["cancel"] == 1) ? true : false;
					$cancelEmptyCheck = (!empty($cancelCheck) || $item["cancel"] == "") ? true : false;
				}
				if(((!empty($cancelCheck)) && $item["quantity"] > 0) || ($item["quantity"] == 0 && ($item["cancel"] == false || $item["cancel"] == 1))) {
					$items[$key]["cancel"] = true;
				} else if(empty($cancelCheck)) {
					$items[$key]["cancel"] = false;
					if($items[$key]["quantity"] == 0) {
						$items[$key]["quantity"] = $item["initial_quantity"];
					}
				}
			}
			if($datas["save"] == 'true') {
				$result = Order::saveCurrentOrder($datas, $items, $current_user["email"]);
				if($result == true) {
					FlashMessage::set("Order items has been updated.", array('class' => 'pass'));
				}
				//Recreate Temporary Order
				$datas['items'] = $items;
				$selected_order = array_merge($selected_order, $datas);
				$order_temp = Order::Create($selected_order);
			} else {
				$order_temp = Order::refreshTempOrder($datas, $items);
			}
			$test_order = $orderCollection->findOne(array("_id" => new MongoId($datas["id"])));
			//Fill Logs Informations - Tracking Informations
			if(!empty($test_order["modifications"])) {
				$order_temp["modifications"] = $test_order["modifications"];
			}
			if(!empty($test_order["tracking_numbers"])) {
				$order_temp["tracking_numbers"] = $test_order["tracking_numbers"];
			}
			//Check if all items are closed, close the order.
			$cancel_order = true;
			foreach($test_order["items"] as $item){
				if(empty($item["cancel"])) {
					$cancel_order = false;
				} else {
				 	if($item["cancel"] != 'false') {
						$cancel_order = false;
					}
				}
			}
			if(!empty($cancel_order)){
				$this->cancel();
				$order_temp = Order::find('first', array('conditions' => array('_id' => new MongoId($datas["id"]))));
				//If the order is canceled, send an email
				if(strlen($order_temp["user_id"]) > 10){
					$user = $userCollection->findOne(array("_id" => new MongoId($order_temp->user_id)));
				} else {
					$user = $userCollection->findOne(array("_id" => $order_temp->user_id));
				}
				if(!is_int($order_temp->ship_date)){
					$shipDate = $order_temp->ship_date->sec;
				} else {
					$shipDate = $order_temp->ship_date;
				}
				$data = array(
					'order' => $order_temp,
					'email' => $user["email"],
					'shipDate' => $shipDate
				);
				Silverpop::send('orderCancel', $data);
			}
			//If order is updated without cancel, send email
			if(($datas["save"] == 'true') && empty($cancel_order)) {
				$order = Order::find('first', array('conditions' => array('_id' => new MongoId($datas["id"]))));
				if(strlen($order_temp["user_id"]) > 10){
					$user = $userCollection->findOne(array("_id" => new MongoId($order->user_id)));
				} else {
					$user = $userCollection->findOne(array("_id" => $order->user_id));
				}
				if(!is_int($order->ship_date)){
					$shipDate = $order->ship_date->sec;
				} else {
					$shipDate = $order->ship_date;
				}
				$data = array(
					'order' => $order,
					'email' => $user["email"],
					'shipDate' => $shipDate
				);
				Silverpop::send('orderUpdate', $data);
			}
		}
		return $order_temp;
	}

	/**
	* The updateShipping method push the old value of shipping with details about author,type and date
	* After that it updates the shipping values by the new datas from the form
	* @param string $id The _id of the order
	*/
	public function updateShipping($id){
		$current_user = Session::read('userLogin');
		$orderCollection = Order::collection();
		//Get datas from the shipping form
		$datas = $this->request->data;
		$update = true;
		$count = 0;
		//check if form is well completed
		foreach($datas as $data){
			if($data == null){
				$missing = true;
			}else {
				$count++;
			}
		}
		//If yes, we prepare the array of modification datas
		if((!$missing) && ($count > 6)){
			$order = Order::find('first', array('conditions' => array('_id' => new MongoId($id))));
			$modification_datas["author"] = $current_user["email"];
			$modification_datas["date"] = new MongoDate(strtotime('now'));
			$modification_datas["type"] = "shipping";
			$modification_datas["old_datas"] = array(
				"firstname" => $order["shipping"]["firstname"],
				"lastname" => $order["shipping"]["lastname"],
				"address" => $order["shipping"]["address"],
				"city" => $order["shipping"]["city"],
				"state" => $order["shipping"]["state"],
				"zip" => $order["shipping"]["zip"],
				"phone" => $order["shipping"]["phone"]
				);
			//We push the modifications datas with the old shipping
			$orderCollection->update(array("_id" => new MongoId($id)), array('$push' => array('modifications' => $modification_datas)), array('upsert' => true));
			$orderCollection->update(array("_id" => new MongoId($id)), array('$set' => array('shipping' => $datas)));
			FlashMessage::set("Shipping details has been updated.", array('class' => 'pass'));
		}else FlashMessage::set("Some informations for the new shipping are missing", array('class' => 'warning'));
	}

	/**
	* The view method renders the order confirmation page that is sent to the customer
	* after they have placed their order
	* The view method render two buttons to manage add new shipping details and cancel order.
	* A shipping details form will be render if you click on the button
	* A pop-up will be called if you click on cancel button to confirm the action
	* Confirm the shipping form will update the "shipping" array and push the old datas on "old_shippings"
	* @param string $id The _id of the order
	*/
	public function view($id = null) {
		$userCollection = User::collection();
		//Only view
		$edit_mode = false;
		//update the shipping address by adding the new one and pushing the old one.
		if($this->request->data){
		 	$datas = $this->request->data;
		}
		if(!empty($datas["cancel_action"])){
			$this->cancel();
			//If the order is canceled, send an email
			$order_temp = Order::find('first', array('conditions' => array('_id' => new MongoId($datas["id"]))));
			if(strlen($order_temp["user_id"]) > 10){
				$user = $userCollection->findOne(array("_id" => new MongoId($order_temp->user_id)));
			} else {
				$user = $userCollection->findOne(array("_id" => $order_temp->user_id));
			}
			if(!is_int($order_temp->ship_date)){
				$shipDate = $order_temp->ship_date->sec;
			} else {
				$shipDate = $order_temp->ship_date;
			}
			$data = array(
				'order' => $order_temp,
				'email' => $user["email"],
				'shipDate' => $shipDate
			);
			Silverpop::send('orderCancel', $data);
		}
		if(!empty($datas["save"])){
			$order = $this->manage_items();
		}else {
			$order = null;
		}
		if ($id && empty($datas["save"]) && empty($datas["cancel_action"]) && !empty($datas["phone"])) {
			if($this->request->data){
				$this->updateShipping($id);
			}
		}
		$this->_render['layout'] = 'base';
		if ($id) {
			$itemscanceled = true;
			$order_current = Order::find('first', array('conditions' => array('_id' => $id)));
			if(empty($order)){
				$order = $order_current;
			}
			$orderData = $order_current->data();
			//Check if order has been authorize.net confirmed
			if(empty($orderData["void_confirm"]) && empty($orderData["auth_confirmation"])) {
				$edit_mode = true;
			}
			$orderItems = $orderData['items'];
			if(!empty($orderItems)){
				foreach ($orderItems as $key => $orderItem) {
					$item = Item::find('first', array(
						'conditions' => array('_id' => $orderItem['item_id']
					)));
					$sku["$orderItem[item_id]"] = $item->vendor_style;
					//Check if items are all canceled
					if(empty($orderItem["cancel"])) {
						$itemscanceled = false;
					} else {
						if($orderItem["cancel"] == false) {
							$itemscanceled = false;
						}
					}
				}
			}
		}
		//Check if order has been canceled
		if(!empty($order->cancel)) {
			$edit_mode = false;
			$itemscanceled = false;
		}
		$shipDate = $this->shipDate($order);
		return compact('order', 'shipDate', 'sku', 'itemscanceled','edit_mode');
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
					$shipDate = strtotime($date . ' +1 day');
				}
			}
		}
		return $shipDate;
	}

	protected function shipping (&$data){
		if (array_key_exists('shippingCost', $data) && $data['shippingCost']>0 ){
			$data['items'][] = array(
				'_id' => 'Shipping',
				'item_id' => 'Shipping',
				'category' => 'Shipping',
				'description' => 'shipping',
				'quantity' => 1,
				'sale_retail' => $data['shippingCost']
			);	
		}

		if (array_key_exists('overShippingCost', $data) && $data['overShippingCost']>0 ){
			$data['items'][] = array(
				'_id' => 'OverShipping',
				'item_id' => 'OverShipping',
				'category' => 'Shipping',
				'description' => 'Over shipping',
				'quantity' => 1,
				'sale_retail' => $data['overShippingCost']
			);	
		}
	}
}

?>