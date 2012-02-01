<?php

namespace admin\controllers;

use lithium\core\Environment;
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
use li3_payments\payments\Processor;
use admin\extensions\Mailer;

/**
 * The Orders Controller
 *
 **/
class OrdersController extends BaseController {

	protected $_classes = array(
		'tax'   => 'admin\extensions\AvaTax',
		'order' => 'admin\models\Order',
		'processedorder'  => 'admin\models\ProcessedOrder'
	);

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
		'Estimated Delivery Date',
		'Errors/Message',
		'Customer Profile'
	);

	/**
	 * The # of business days to be added to an event to determine the estimated
	 * ship by date. The default is 18 business days.
	 *
	 * @var int
	 **/
	protected $_shipBuffer = 15;

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
		$orderClass = $this->_classes['order'];

		$headings = $this->_headings;
		$collection = $orderClass::collection();

		if ($this->request->data) {
			$search = $this->request->data['search'];
			$searchType = $this->request->data['type'];
			$date = array(
				'date_created' => array(
					'$gt' => new MongoDate(strtotime('August 3, 2010'))
				)
			);

			if (!empty($search)) {
				switch ($searchType) {
					case 'order':
						$order = new MongoRegex("/$search/i");
						$rawOrders = $collection->find(array('order_id' => $order) + $date);
						break;
					case 'address':
						$rawOrders = $orderClass::orderSearch($search, 'address');
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
						$rawOrders = $orderClass::orderSearch($search, 'name');
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
						FlashMessage::write(
							"Results found for $searchType search of $search",
							array('class' => 'pass')
						);
						$orders[] = $this->sortArrayByArray($order, $headings);
						$shipDate["$order[_id]"] = $this->shipDate($order);
					}
				}
				if (empty($order)) {
					FlashMessage::write(
						"No results found for $searchType search of $search",
						array('class' => 'warning')
					);
				}
			}
		}
		return compact('orders', 'headings', 'shipDate');
	}

	/**
	* The cancel method close an order or unclose it
	* by modfiying the calling the Order cancel method
	*/
	public function cancel($credits_recorded = false) {
		$orderClass = $this->_classes['order'];

		$current_user = Session::read('userLogin');
		$orderCollection = $orderClass::collection();
		$datas = $this->request->data;

		if ($datas["id"]) {
			$status = $orderClass::cancel(
				$datas["id"],
				$current_user["email"],
				$datas["comment"],
				$credits_recorded
			);
			$selected_order = $orderCollection->findOne(array("_id" => new MongoId($datas["id"])));
		}
	}

	public function cancelMultipleItems() {
		$orderClass = $this->_classes['order'];
		$current_user = Session::read('userLogin');

		$order       = $this->request->data['order'];
		$line_number = $this->request->data['line_number'];
		$item_id     = $this->request->data['id'];
		$sku         = $this->request->data['sku'];

		foreach ($order as $key => $value) {
			$line_num = $line_number[$key];

			if (strlen($value) > 2) {
				$order_a = $orderClass::first(array(
					'conditions' => array('_id' => new MongoId($value))
				));

				$order_data = $order_a->data();

				$order_data['id'] = $order_data['_id'];
				$order_data['items'][$line_num]['initial_quantity'] = $order_data['items'][$line_num]['quantity'];
				$order_data['items'][$line_num]['cancel'] = "true";
				$order_data['save'] = 'true';
				$order_data['comment'] = 'Bulk Cancel of Item';

				$this->request->data = $order_data;

				$order_m_i = $this->manage_items();

//				$this->updateShipping($order_data[id]);
			}
			$i++;
		}

		$this->redirect('/items/bulkCancel/'.$sku);
	}

	public function cancelOneItem() {
		$orderClass = $this->_classes['order'];
		$current_user = Session::read('userLogin');

		$order_id    = $this->request->query['order_id'];
		$sku         = $this->request->query['sku'];
		$item_id     = $this->request->query['item_id'];
		$line_number = $this->request->query['line_number'];

		$order_a = $orderClass::first(array(
			'conditions' => array('_id' => new MongoId($order_id))
		));

		$order_data = $order_a->data();
		$order_data['id'] = $order_data['_id'];
		$order_data['items'][$line_number]['initial_quantity'] = $order_data['items'][$line_number]['quantity'];
		$order_data['items'][$line_number]['cancel'] = "true";
		$order_data['comment'] = 'Bulk Cancel of Item';

		$this->request->data = $order_data;

		$order_temp = $this->manage_items();

		#SAVING DATAS
		$order_data_to_be_saved = $order_temp->data();
		$order_data_to_be_saved[id] = $order_data[_id];
		$order_data_to_be_saved[items][$line_number][initial_quantity] = $order_data[items][$line_number][quantity];
		$order_data_to_be_saved[items][$line_number][cancel] = "true";
		$order_data_to_be_saved[save] = true;
		$order_data_to_be_saved[comment] = 'Bulk Cancel of Item';
		$this->request->data = $order_data_to_be_saved;
		$order = $this->manage_items();
		
		$this->redirect('/items/bulkCancel/' . $sku);

	}

	/**
	* The manage_items method update the temporary order.
	* If the variable save is set to true, it apply the changes.
	*
	* @fixme The corresponding test for this action or the action itself needs
	*        another review. The test expectations aren't met possibly because they're incorrect.
	* @see admin\tests\cases\controllers\OrdersControllerTest::testManageItemsUnsaved()
	* @see admin\models\Order::saveCurrentOrder()
	* @see admin\models\Order::refreshTempOrder()
	* @see admin\models\Order:::checkOrderCancel()
	*/
	public function manage_items() {
		$orderClass = $this->_classes['order'];
		$current_user = Session::read('userLogin');

		$orderCollection = $orderClass::collection();
		$userCollection = User::collection();

		if($this->request->data) {

			$datas = $this->request->data;
			$selected_order = $orderCollection->findOne(array("_id" => new MongoId($datas["id"])));
			/**
			* This sets the field original credit used, in order to keep a record in the order
			* before any changes are made to the credit
			*/
			if (!$orderClass::checkForCancellations($selected_order['order_id'])) {
				$selected_order["original_credit_used"] = $selected_order["credit_used"];
				$datas["original_credit_used"] = $selected_order["credit_used"];
			}
			$datas["user_id"] = $selected_order["user_id"];
			$datas["order_id"] = $selected_order["order_id"];
			$items = $selected_order["items"];

			foreach($datas["items"] as $key => $item) {
				//Quantity
				$items[$key]["initial_quantity"] = $item["initial_quantity"];
				$items[$key]["quantity"] = $item["quantity"];

				//Cancel Status
				if (empty($item["cancel"])){
					$cancelCheck = false;
					$cancelEmptyCheck = false;
				} else {
					$cancelCheck = ($item["cancel"] == "true" ||  $item["cancel"] == 1) ? true : false;
					$cancelEmptyCheck = (!empty($cancelCheck) || $item["cancel"] == "") ? true : false;
				}

				if (((!empty($cancelCheck)) && $item["quantity"] > 0) || ($item["quantity"] == 0 && ($item["cancel"] == false || $item["cancel"] == 1))) {
					$items[$key]["cancel"] = true;
				} elseif (empty($cancelCheck)) {
					$items[$key]["cancel"] = false;

					if ($items[$key]["quantity"] == 0) {
						$items[$key]["quantity"] = $item["initial_quantity"];
					}
				}
			}
			if ($datas["save"] == 'true') {
				extract($orderClass::saveCurrentOrder($datas, $items, $current_user["email"]));

				if ($result == true) {
					FlashMessage::write("Order items has been updated.", array('class' => 'pass'));
				}

				#Get Last Saved Order
				$order_temp = $orderClass::find('first', array('conditions' => array(
					'_id' => new MongoId($datas["id"]
				))));
			} else {
				$order_temp = $orderClass::refreshTempOrder($datas, $items);
			}
			$test_order = $orderCollection->findOne(array("_id" => new MongoId($datas["id"])));

			//Fill Logs Informations - Tracking Informations
			if (!empty($test_order["modifications"])) {
				$order_temp["modifications"] = $test_order["modifications"];
			}
			if (!empty($test_order["tracking_numbers"])) {
				$order_temp["tracking_numbers"] = $test_order["tracking_numbers"];
			}

			//Check if all items are closed, close the order.
			$cancel_order = true;
			foreach($test_order["items"] as $item){
				if (empty($item["cancel"])) {
					$cancel_order = false;
				} else {
				 	if($item["cancel"] != 'false') {
						$cancel_order = false;
					}
				}
			}
			if (!empty($cancel_order)){
				$this->cancel($credits_recorded);
				$order_temp = $orderClass::first(array(
					'conditions' => array('_id' => new MongoId($datas["id"]))
				));

				// If the order is canceled, send an email
				if (strlen($order_temp["user_id"]) > 10) {
					$user = $userCollection->findOne(array("_id" => new MongoId($order_temp->user_id)));
				} else {
					$user = $userCollection->findOne(array("_id" => $order_temp->user_id));
				}
				if (!is_int($order_temp->ship_date)){
					$shipDate = $order_temp->ship_date->sec;
				} else {
					$shipDate = $order_temp->ship_date;
				}
				$data = array(
					'order' => $order_temp->data(),
					'shipDate' => date('M d, Y', $shipDate)
				);
				if (Environment::is('production')) {
					Mailer::send('Cancel_Order', $user["email"], $data);
				}
			}

			// If order is updated without cancel, send email
			if (($datas["save"] == 'true') && empty($cancel_order)) {
				$order = $orderClass::find('first', array('conditions' => array('_id' => new MongoId($datas["id"]))));
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
					'order' => $order->data(),
					'shipDate' => date('M d, Y', $shipDate)
				);
				if (Environment::is('production')) {
					Mailer::send('Order_Update', $user["email"], $data);
				}
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
		$orderClass = $this->_classes['order'];
		$current_user = Session::read('userLogin');

		$orderCollection = $orderClass::collection();

		// Get datas from the shipping form.
		$datas = $this->request->data;
		$update = true;
		$count = 0;
		$missing = false;

		// Check if form is well completed.
		foreach ($datas as $data) {
			if ($data == null){
				$missing = true;
			} else {
				$count++;
			}
		}

		// If yes, we prepare the array of modification datas.
		if ((!$missing) && ($count > 6)) {
			$order = $orderClass::find('first', array(
				'conditions' => array('_id' => new MongoId($id))
			));
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

			// We push the modifications datas with the old shipping.
			$orderCollection->update(
				array("_id" => new MongoId($id)),
				array('$push' => array('modifications' => $modification_datas)),
				array('upsert' => true)
			);
			$orderCollection->update(
				array("_id" => new MongoId($id)),
				array('$set' => array('shipping' => $datas))
			);
			FlashMessage::write("Shipping details has been updated.", array('class' => 'pass'));
		} else {
			FlashMessage::write(
				"Some informations for the new shipping are missing",
				array('class' => 'warning')
			);
		}
	}
	
	/**
	* The updatePayment method push the old value of payment with details about author,type and date
	* After that it updates the billing/cc values by the new datas from the form
	* @param string $id The _id of the order
	*/
	public function updatePayment($id){
		$orderClass = $this->_classes['order'];
		$current_user = Session::read('userLogin');

		$orderCollection = $orderClass::collection();

		// Get datas from the shipping form.
		$datas = $this->request->data;
		$update = true;
		$count = 0;
		$missing = false;

		// Check if all billing informations are present
		foreach ($datas['billing'] as $data) {
			if ($data == null){
				$missing = true;
			} else {
				$count++;
			}
		}
		// Check if all credit card informations are present
		foreach ($datas['creditcard'] as $data) {
			if ($data == null){
				$missing = true;
			} else {
				$count++;
			}
		}
		
		// If yes, we prepare the array of modification datas.
		if ((!$missing) && ($count > 11)) {
			#Create new authorization
			$errors = $this->authorize($datas, $id);
			if(empty($errors)) {
				$order = $orderClass::find('first', array(
					'conditions' => array('_id' => new MongoId($id))
				));
				$modification_datas["author"] = $current_user["email"];
				$modification_datas["date"] = new MongoDate(strtotime('now'));
				$modification_datas["type"] = "billing";
				$modification_datas["old_datas"] = array(
					"firstname" => $order["billing"]["firstname"],
					"lastname" => $order["billing"]["lastname"],
					"address" => $order["billing"]["address"],
					"city" => $order["billing"]["city"],
					"state" => $order["billing"]["state"],
					"zip" => $order["billing"]["zip"],
					"phone" => $order["billing"]["phone"]
				);
				// We push the modifications datas with the old shipping.
				$orderCollection->update(
					array("_id" => new MongoId($id)),
					array('$push' => array('modifications' => $modification_datas)),
					array('upsert' => true)
				);
				$orderCollection->update(
					array("_id" => new MongoId($id)),
					array('$set' => array('billing' => $datas['billing'], 
										'card_type' => $datas['creditcard']['type'],
										'card_number' => substr($datas['creditcard']['number'], -4) 
					))
				);
				FlashMessage::write("Payment details has been updated.", array('class' => 'pass'));	
			} else {
				FlashMessage::write(
					$errors,
					array('class' => 'warning')
				);
			}

		} else {
			FlashMessage::write(
				"Some informations for the new payments are missing",
				array('class' => 'warning')
			);
		}
	}
	
	public function authorize($datas, $id) {
		$orderClass = $this->_classes['order'];
		$ordersCollection = $orderClass::Collection();
		$order = $ordersCollection->findOne(array("_id" => new MongoId($id)));
		#Save Old AuthKey with Date
		$newRecord = array('authKey' => $order['authKey'], 'date_saved' => new MongoDate());
		#Cancel Previous Transaction	
		if($order['card_type'] != 'amex' && !empty($order['authTotal'])) {
			$auth = Processor::void('default', $order['auth'], array(
				'processor' => isset($order['processor']) ? $order['processor'] : null,
				'orderID' => $order['order_id']
			));
		}
		$userInfos = User::lookup($order['user_id']);
		#Create Card and Check Billing Infos
		$card = Processor::create('default', 'creditCard', $datas['creditcard'] + array(
			'billing' => Processor::create('default', 'address', array(
				'firstName' => $datas['billing']['firstname'],
				'lastName'  => $datas['billing']['lastname'],
				'address'   => trim($datas['billing']['address'] . ' ' . $datas['billing']['address2']),
				'city'      => $datas['billing']['city'],
				'state'     => $datas['billing']['state'],
				'zip'       => $datas['billing']['zip'],
				'country'   => $datas['billing']['country'] ?: 'US',
				'email'     => $userInfos['email']

		))));
		#Create a new Transaction and Get a new Authorization Key
		$auth = Processor::authorize('default', $order['total'], $card, array('orderID' => $order['order_id']));
		if($auth->success()) {
			$customer = Processor::create('default', 'customer', array(
				'firstName' => $userInfos['firstname'],
				'lastName' => $userInfos['lastname'],
				'email' => $userInfos['email'],
				'payment' => $card
			));
			$result = $customer->save();
			$profileID = $result->response->paySubscriptionCreateReply->subscriptionID;
			$update = $ordersCollection->update(
				array('_id' => $order['_id']),
				array('$set' => array(
					'cyberSourceProfileId' => $profileID
							)), array( 'upsert' => true)
			);
			#Setup new AuthKey
			$update = $ordersCollection->update(
					array('_id' => $order['_id']),
					array('$set' => array(
						'authKey' => $auth->key,
						'auth' => $auth->export(),
						'processor' => $auth->adapter,
						'authTotal' => $order['total']
					), '$unset' => array(
						'auth_error' => 1
					)), array( 'upsert' => true)
			);
			#Add to Auth Records Array
			$update = $ordersCollection->update(
					array('_id' => $order['_id']),
					array('$push' => array('auth_records' => $newRecord)), array( 'upsert' => true)
			);
		} else {
			$message  = "Authorize failed for order id `{$order['order_id']}`:";
			$message .= $error = implode('; ', $auth->errors);
		}
		return $message;
	}
	
	public function capture($id) {
		$orderClass = $this->_classes['order'];
		$ordersCollection = $orderClass::Collection();
		$order = $ordersCollection->findOne(array("_id" => new MongoId($id)));
		if($order['auth'] && $order['processor']) {
			$auth_capture = Processor::capture(
				'default',
				$order['auth'],
				floor($order['total'] * 100) / 100,
				array(
					'processor' => isset($order['processor']) ? $order['processor'] : null,
					'orderID' => $order['order_id']
				)
			);
			if ($auth_capture->success()) {
				$update = $ordersCollection->update(
					array('_id' => $order['_id']),
					array('$set' => array('authKey' => $auth_capture->key,
										  'auth' => $auth_capture->export(),
										  'authTotal' => $order['total'],
										  'processor' => $auth_capture->adapter,
										  'payment_date' => new MongoDate(),
       									  'auth_confirmation' => $auth_capture->key
					)), array( 'upsert' => true)
				);
				#Unset Old Errors fields
				$update = $ordersCollection->update(
					array('_id' => $order['_id']),
					array('$unset' => array('error_date' => 1,
											'auth_error' => 1)
					)
				);
				$message  = "Capture Successfully Processed for order id `{$order['order_id']}`:";
			} else {
				$message  = "Capture failed for order id `{$order['order_id']}`:";
				$message .= $error = implode('; ', $auth_capture->errors);
			}
		}
		return $message;
	}
			
	/**
	* The view method renders the order confirmation page that is sent to the customer
	* after they have placed their order
	* The view method render two buttons to manage add new shipping details and cancel order.
	* A shipping details form will be render if you click on the button
	* A pop-up will be called if you click on cancel button to confirm the action
	* Confirm the shipping form will update the "shipping" array and push the old datas on "old_shippings"
	* @param string $id The _id of the order
	* @see admin\controllers\OrdersController::manage_items()
	* @see admin\controllers\OrdersController::shipDate()
	* @see admin\controllers\OrdersController::updateShipping()
	* @see admin\controllers\OrdersController::cancel()
	*/
	public function view($id = null) {
		$orderClass = $this->_classes['order'];
		$processedOrderClass = $this->_classes['processedorder'];

		$userCollection = User::collection();
		$ordersCollection = $orderClass::Collection();
		$processedOrderColl = $processedOrderClass::Collection();

		// update the shipping address by adding the new one and pushing the old one.
		if ($this->request->data) {
		 	$datas = $this->request->data;
		}
		if (!empty($datas["cancel_action"])){
			$this->cancel();
			//If the order is canceled, send an email
			$order_temp = $orderClass::find('first', array('conditions' => array('_id' => new MongoId($datas["id"]))));
			
			if(strlen($order_temp["user_id"]) > 10){
				$user = $userCollection->findOne(array("_id" => new MongoId($order_temp->user_id)));
			} else {
				$user = $userCollection->findOne(array("_id" => $order_temp->user_id));
			}
			if (!is_int($order_temp->ship_date)){
				$shipDate = $order_temp->ship_date->sec;
			} else {
				$shipDate = $order_temp->ship_date;
			}
			$data = array(
				'order' => $order_temp->data(),
				'shipDate' => date('M d, Y', $shipDate)
			);
			if (Environment::is('production')) {
				Mailer::send('Cancel_Order', $user["email"], $data);
			}
		}
		if(!empty($datas["process-as-an-exception"])){
			$update = $ordersCollection->update(
					array('_id' => new MongoId($id)),
					array('$set' => array('process-as-an-exception' => true)), array( 'upsert' => true)
			);
			FlashMessage::write("This Order is on the queue as Dotcom Exception", array('class' => 'pass'));	
		}
		if (!empty($datas["save"])){	
			$order = $this->manage_items();
		} else {
			$order = null;
		}
		if ($id && empty($datas["save"]) && empty($datas["cancel_action"]) && !empty($datas["phone"])) {
			$this->updateShipping($id);
		}
		if ($id && empty($datas["save"]) && !empty($datas["capture_action"])) {
			$result = $this->capture($id);
		}
		if ($id && empty($datas["save"]) && empty($datas["cancel_action"]) && !empty($datas["billing"])) {
			$this->updatePayment($id);
		}
		$this->_render['layout'] = 'base';

		if ($id) {
			$itemscanceled = true;
			$order_current = $orderClass::find('first', array('conditions' => array('_id' => $id)));
			//Check if the order was processed and sent to Dotcom
			$processed_count = $processedOrderColl->count(array('OrderNum' => $order_current['order_id']));


			if (empty($order)) {
				$order = $order_current;
			}
			$orderData = $order_current->data();

			$orderItems = $orderData['items'];

			if (!empty($orderItems)){
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

		// Check if order has been canceled
		if (!empty($order->cancel)) {
			$itemscanceled = false;
		}

		#Get Services
		if(is_object($order->service)) {
			$service = $order->service->data();
		} else {
			$service = $order->service;
		}

		$shipDate = $this->shipDate($order);
		return compact('order', 'shipDate', 'sku', 'itemscanceled', 'service','processed_count');
	}

	/**
	 * The update method captures payment and updates the order with tracking info.
	 */
	public function update() {
		$orderClass = $this->_classes['order'];

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
					$order = $orderClass::lookup(substr($shipRecord['OrderNum'], 0, 8));

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
						$trackingNum = $orderClass::find('first', array(
							'conditions' => array(
								'tracking_numbers' => $shipRecord['Tracking #']
						)));
						$user = User::find('first', array('condition' => array('_id' => $order->user_id)));

						if (empty($trackingNum) && $sendEmail) {
							$data = array(
								'order' => $order->data(),
								'email' => $shipRecord['Email'],
								'details' => $details
							);
							Mailer::send('Order_Shipped', $shipRecord['Email'], $data);
						}
						if ($orderClass::setTrackingNumber($order->order_id, $shipRecord['Tracking #'])){
							if (empty($order->auth_confirmation)) {
								if ($orderClass::process($order) && $user->purchase_count == 1) {
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

	public function taxreturn(){
		$taxClass   = $this->_classes['tax'];
		$orderClass = $this->_classes['order'];

		if ($this->request->data) {

			//get order details
			$order = $orderClass::collection()->findOne(array('_id' => new MongoId($this->request->data['id']) ));

			// show the form for partial order tax return
			if(array_key_exists('partordertaxreturn_action',$this->request->data)){
				$orderItems = $order['items'];
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
				return array('order'=>$order, 'sku' => $sku, 'itemscanceled' => false, 'edit_mode' => false);
			}

			// FULL order tax return
			if(array_key_exists('fullordertaxreturn_action',$this->request->data)){
				$data['order'] = $order;
				$data['items'] = $order['items'];
				$data['shippingCost'] = ($data['order']['handling'] - $data['order']['handlingDiscount']);
				$data['overShippingCost'] = ($data['order']['overSizeHandling'] - $data['order']['overSizeHandlingDiscount']);
				$data['order']['date'] = date('Y-m-d',$data['order']['date_created']->sec);
				$data['order']['order_id'] = $data['order']['order_id'].'.1';
				$data['order']['return'] = 'full';
				$order['return'] = 'full';
				$this->_shipping($data);
				$orderClass::save($order);
			}

			// Partial order tax return
			if (isset($this->request->data['return_check']) && is_array($this->request->data['return_check']) && count($this->request->data['return_check'])>0){
				$data['order'] = $order;
				$data['order']['date'] = date('Y-m-d',$order['date_created']->sec);
				if (array_key_exists('return',$data['order'])) {
					$ord_ver = $data['order']['return'] +1;
				} else {
					$ord_ver = 1;
				}
				$data['items'] = array();
				$sub = 0;
				foreach ($data['order']['items'] as $k=>$item){
					if (in_array($item['item_id'],$this->request->data['return_check'])){
						$rq = $this->request->data['return_quantity'][$item['item_id']];
						$quantity = $item['quantity'] - $rq;
						$item['return'][$ord_ver] = $rq;
						$sub = $sub + $rq * $item['sale_retail'];
						$item['quantity'] = $rq;
						$data['items'][] = $item;
						$item['quantity'] = $quantity;
						$order['items'][$k]=$item;
					}
				}
				$order['return'] = $ord_ver;
				$data['order']['order_id'] = $data['order']['order_id'].'.'.$ord_ver;

				$tax = $taxClass::getTax($data);
				$order['tax'] = $order['tax'] - $tax;
				$order['subTotal'] = $order['subTotal'] - $sub;
				$order['total'] = $order['total'] - ( $sub + $tax);
				$orderClass::collection()->save($order);
			}

			// remember to set for all returned items to negative amount
			foreach ($data['items'] as $k=>$v){
				$v['sale_retail'] = '-'.$v['sale_retail'];
				$data['items'][$k] = $v;
			}

			$taxClass::returnTax($data);
		}

		$this->redirect(array('Orders::view::'.$this->request->data['id']));
		exit(0);
	}

	protected function _shipping(&$data){
		if (array_key_exists('shippingCost', $data) && $data['shippingCost']>0 ){
			$data['items'][] = array(
				'_id' => 'Shipping',
				'item_id' => 'Shipping',
				'category' => 'Shipping',
				'description' => 'shipping',
				'quantity' => 1,
				'sale_retail' => $data['shippingCost'],
				'taxIncluded' => true
			);
		}

		if (array_key_exists('overShippingCost', $data) && $data['overShippingCost']>0 ){
			$data['items'][] = array(
				'_id' => 'OverShipping',
				'item_id' => 'OverShipping',
				'category' => 'Shipping',
				'description' => 'Over shipping',
				'quantity' => 1,
				'sale_retail' => $data['overShippingCost'],
				'taxIncluded' => true
			);
		}
	}

	public function payments(){
		$orderClass = $this->_classes['order'];

		$data = $this->request->data;
		extract($orderClass::orderPaymentRequests($data));

		if ($payments && $payments->hasNext()) {
            if (!empty($message)) {
                $class = 'notice';
                $style = 'font-color:#fff';
            } else {
                $class = 'pass';
                 $style = 'font-color:#000';
            }
            FlashMessage::write("Results found." . $message ,	array('class' => $class));
        } else {
            FlashMessage::write("No results found." . $message ,	array('class' => 'fail'));
        }
		return compact('payments','type');
	}
}

?>
