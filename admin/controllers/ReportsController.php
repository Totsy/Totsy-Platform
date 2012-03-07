<?php

namespace admin\controllers;
use admin\controllers\BaseController;
use admin\models\Cart;
use admin\models\User;
use admin\models\Order;
use admin\models\Event;
use admin\models\Item;
use admin\models\Base;
use admin\models\Report;
use admin\models\Service;
use admin\models\Affiliate;
use Mongo;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;
use li3_flash_message\extensions\storage\FlashMessage;
use lithium\analysis\Logger;
use admin\models\PurchaseOrder;
use lithium\data\Model;
use FusionCharts;
use admin\extensions\util\String;

/**
 * The Reports Controller is the core for all reporting functionality.
 */
class ReportsController extends BaseController {

  /**
	 * The purchase order column headings.
	 *
	 * @var array
	 */
	protected $_purchaseHeading = array(
		'Vendor Style',
		'SKU',
		'Product Name',
		'Product Color',
		'Quantity',
		'Size',
		'Unit',
		'Total'
	);

	protected $_clientId = 'TOT';

	/**
	 * The product file report column heading and default values.
	 *
	 * @var array
	 */
	protected $_productHeading = array(
		'ClientID' => 'TOT',
		'SKU' => null,
		'Description' => null,
		'WhsInsValue (Cost)' => null,
		'ShipInsValue' => null,
		'Expiration_Date' => null,
		'UPC' => null,
		'Description for Customs' => null,
		'HSC Code' => null,
		'Class for LTL' => null,
		'Country of Origin' => 'USA',
		'Velocity' => 'B',
		'Ref1' => null,
		'Ref2' => null,
		'Ref3' => null,
		'Ref4' => null,
		'Ref5' => null,
		'UOM1' => 'EA',
		'UOM1_Qty' => 1,
		'UOM1_Weight' => '1.00',
		'UOM1_Length' => '1.00',
		'UOM1_Width' => '1.00',
		'UOM1_Height' => '1.00',
		'UOM1_Cube' => '1.00'
	);

	protected $_orderHeading = array(
		'Select',
		'OrderNum',
		'SKU',
		'Qty',
		'CompanyOrName',
		'Email',
		'Note'
	);

	/**
	 * The order file column heading.
	 *
	 * @var	array
	 */
	protected $_fileHeading = array(
		'Date' => null,
		'ClientId' => 'TOT',
		'DC' => 'ALN',
		'ShipMethod' => null,
		'RushOrder (Y/N)' => null,
		'OrderNum' => null,
		'OldSKU' => null,
		'SKU' => null,
		'Qty' => null,
		'CompanyOrName' => null,
		'ContactName' => null,
		'Address1' => null,
		'Address2' => null,
		'City' => null,
		'StateOrProvince' => null,
		'Zip' => null,
		'Country' => null,
		'Email' => null,
		'Tel' => null,
		'Customer PO #' => null,
		'Pack Slip Comment' => null,
		'Special Packing Instructions' => null,
		'Ref1' => null,
		'Ref2' => null,
		'Ref3' => null,
		'Ref4' => null,
		'Ref5' => null,
		'Ref6' => null,
		'Ref7' => null,
		'Ref8' => null,
		'Ref9' => null,
		'Ref10' => null,
		'BillType (R/3P)' => null,
		'R3PAccountNum' => null,
		'Billing CompanyName' => null,
		'Billing Address1' => null,
		'Billing Address2' => null,
		'Billing City' => null,
		'Billing State' => null,
		'Billing Country' => null,
		'Billing Telephone' => null,
		'COD (Y/N)' => null,
		'Order COD Value' => null,
		'COD: Require Payment By Cashier\'s Check/Money Order (Y/N)' => null,
		'COD: Add Shipping Costs to COD Amount (Y/N)' => null
	);


	public function index() {

	}

	public function affiliate() {
		$search = Report::create($this->request->data);
		$criteria = null;
		if ($this->request->data) {
			$criteria = $this->request->data;
			$name = $this->request->data['affiliate'];
			$subaff = $this->request->data['subaffiliate'];
			$min_date = $this->request->data['min_date'];
			$max_date = $this->request->data['max_date'];
			$result = array();
			if (empty($name)) {
			    FlashMessage::write("Affiliate Code Required" ,	array('class' => 'fail'));
			    return compact('search', 'results', 'searchType', 'criteria');
			}
			if((bool)$subaff){
				$affiliate = new MongoRegex('/^' . $name . '/i');
			}else{
				$affiliate = $name;
			}

			if (empty($min_date) || empty($max_date)) {
			    FlashMessage::write("Missing Min and/or max date" ,	array('class' => 'fail'));
			    return compact('search', 'results', 'searchType', 'criteria');
			}
            //Conditions with date converted to the right timezone
            $min = new MongoDate(strtotime($this->request->data['min_date']));
            $max = new MongoDate(strtotime($this->request->data['max_date']));
            $date = array(
                'created_date' => array(
                    '$gte' => $min,
                    '$lte' => $max)
            );
            $searchType = $this->request->data['search_type'];
            $total = 0;
            switch ($searchType) {
                case 'Revenue':
                    $results = Affiliate::revenueCount($name,$affiliate,$min,$max);
                break;
                case 'Registrations':
                    $results = Affiliate::registrationCount($name, $date, $affiliate);
                    break;
                case 'Bounces':
                    $results = Affiliate::bounceReport($name, $date, $affiliate);
                    break;
                case 'Effective':
                    $results = Affiliate::effectiveCoReg($name, $date, $affiliate);
                    break;
			}
		}
		return compact('search', 'results', 'searchType', 'criteria');
	}

	/**
	 * The purchases method generates the PO report for the logistics team. This report returns an associative array
	 * which lists all the sales of each item of a sale.
	 *
	 * The order of operation is as follows:
	 *
	 * 1) Find all the event that is being requested via the URL.
	 * 2) Find all the times that are a part of the event requested.
	 * 3) For each item get all the orders that have been placed with that item in it.
	 * 4) Build the array of cumulative purchases for each item of the event.
	 * @return mixed
	 */
	public function purchases($eventId = null) {
		if ($eventId) {
			$purchaseHeading = $this->_purchaseHeading;
			$total = array('sum' => 0, 'quantity' => 0);
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId),
				'fields' => array(
				    '_id' => 1,
				    'name' => 1
				)));
			$vendorName = preg_replace('/[^(\x20-\x7F)]*/','', substr(String::asciiClean($event->name), 0, 3));
			$time = date('ymdis', $event->_id->getTimestamp());
			$poNumber = 'TOT'.'-'.$vendorName.$time;
			$eventItems = Event::getItems($eventId);
			$itemIds = array();
			$temp = array();
			foreach ($eventItems as $key => $eventItem) {
			    $eventItem = get_object_vars($eventItem);
			    $eventItem = $eventItem['_config']['data'];
			    $id = (string)$eventItem['_id'];
				$temp[$id] = $eventItem;
				$itemIds[] = $id;
			}
			$eventItems = $temp;
			$orders = Order::find('all', array(
				'conditions' => array(
					'items.item_id' => array('$in' => $itemIds),
					'cancel' => array('$ne' => true))));

			if ($orders) {
				foreach ($orders as $order) {
					$order = $order->data();
					$items = $order['items'];
					foreach ($items as $item) {
						$active = (empty($item['cancel']) || $item['cancel'] != true) ? true : false;
						if( array_key_exists($item['item_id'], $eventItems) ) {
							$eventItem = $eventItems[$item['item_id']];
							if (array_key_exists($item['size'], $eventItem['details']) && $active) {
								$sku = $eventItem['sku_details'][$item['size']];
								$purchaseOrder[$sku]['Product Name'] = $eventItem['description'];
								$purchaseOrder[$sku]['Product Color'] = $eventItem['color'];
								$purchaseOrder[$sku]['Vendor Style'] = $eventItem['vendor_style'];
								$purchaseOrder[$sku]['SKU'] = $eventItem['sku_details'][$item['size']];
								$purchaseOrder[$sku]['Unit'] = $eventItem['sale_whol'];
								if (empty($purchaseOrder[$sku]['Quantity'])) {
									$purchaseOrder[$sku]['Quantity'] = $item['quantity'];
								} else {
									$purchaseOrder[$sku]['Quantity'] += $item['quantity'];
								}
								$total['quantity'] += $item['quantity'];
								$purchaseOrder[$sku]['Total'] = $purchaseOrder[$sku]['Quantity'] * $eventItem['sale_whol'];
								$total['sum'] += $item['quantity'] * $eventItem['sale_whol'];
								$purchaseOrder[$sku]['Size'] = $item['size'];
								$purchaseOrder[$sku] = $this->sortArrayByArray($purchaseOrder[$sku], $purchaseHeading);
							}
						}
					}
				}

				// Sloppy code to make sure that the results are sorted by Vendor Style then Size
				foreach ($purchaseOrder as $key => $row) {
				    $vendor_style[$key]  = $row['Vendor Style'];
				    $size[$key] = $row['Size'];
				}

				reset($purchaseOrder);
				$po = current($purchaseOrder);

				// Size can be numeric (1,2,3), non-numeric (S,M,L,XL) or mixed (7 kids, 9 kids, 11 kids)
				// so we check the first character of the size to decide how to sort
				if (is_numeric(substr($po['Size'],0,1)))
					array_multisort($vendor_style, SORT_DESC, SORT_STRING, $size, SORT_ASC, SORT_NUMERIC, $purchaseOrder);
				else
					array_multisort($vendor_style, SORT_DESC, SORT_STRING, $size, SORT_ASC, SORT_STRING, $purchaseOrder);

			}
		}
		return compact('poNumber', 'purchaseOrder', 'event', 'total', 'purchaseHeading');
	}

	public function orders($eventId = null) {
		if ($eventId) {
			$orderHeading = $this->_orderHeading;
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			$eventItems = Event::getItems($eventId);
			$inc = 0;
			foreach ($eventItems as $eventItem) {
				$orders = Order::find('all', array(
					'conditions' => array(
						'items.item_id' => (string) $eventItem['_id']
				)));
				if ($orders) {
					$orderData = $orders->data();
					if (!empty($orderData)) {
						foreach ($orderData as $order) {
							$items = $order['items'];
							$user = User::find('first', array('conditions' => array('_id' => $order['user_id'])));
							$others['Closed'] = 0;
							$others['Open'] = 0;
							foreach ($items as $item) {
								$orderEvent = Event::find('first', array(
									'conditions' => array(
										'_id' => $item['event_id']
								)));
								if (!empty($orderEvent)) {
									$others['Closed'] += ($orderEvent->end_date->sec < time()) ? 1 : 0;
									$others['Open'] += ($orderEvent->end_date->sec > time()) ? 1 : 0;
								}
								if (($item['item_id'] == $eventItem['_id']) && (empty($item['cancel']) || $item['cancel'] != true)){
									$orderList[$inc]['Select'] = ($others['Open'] != 0) ? '' : 'Checked';
									$orderList[$inc]['Item'] = $eventItem['_id'];
									$orderList[$inc]['Cart'] = $item['_id'];
									$orderList[$inc]['OrderNum'] = $order['order_id'];
									$orderList[$inc]['id'] = $order['_id'];
									$itemRecord = Item::collection()->findOne(array('_id' => new MongoId($item['item_id'])));
									$orderList[$inc]['SKU'] = $itemRecord['sku_details'][$item['size']];
									$orderList[$inc]['Qty'] = $item['quantity'];
									$orderList[$inc]['CompanyOrName'] = $order['shipping']['firstname'].' '.$order['shipping']['lastname'];
									$orderList[$inc]['Email'] = (!empty($user->email)) ? $user->email : '';
									$orderList[$inc]['Note'] = $others;
									$orderList[$inc] = $this->sortArrayByArray($orderList[$inc], $orderHeading);
								}
								++$inc;
							}
						}
					}
				}
			}
		}
		return compact('orderList', 'event', 'total', 'orderHeading');
	}

	public function oldsku($vendor_style, $size) {
		$size = ($size == 'no size') ? null : '-'.$size;
		$sku = strtoupper(str_replace(' ', '', trim($vendor_style.$size)));
		if (strlen($sku) > 19) {
			$sku = str_replace('-', '', $sku);
		}
		return $sku;
	}

	/**
	 * Generates the order file.
	 *
	 * @return array
	 *     orderFile - This is the array of all the orders that are being processed for shipment.
	 *     heading - The column headings for the orderFile.
	 *     event - The event object.
	 */
	public function orderfile() {
		$heading = $this->_fileHeading;
		if ($this->request->data) {
			$eventId = $this->request->data['event_id'];
			unset($this->request->data['event_id']);
			$orders = $this->request->data;
			$inc = 0;
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			foreach ($orders as $orderId => $cartId) {
				$conditions = array(
					'_id' => new MongoId(substr($orderId, 0, 24)),
					'$or' => array(
						array('items._id' => new MongoId($cartId)),
						array('items._id' =>$cartId)
					)
				);
				$order = $this->getOrders('first', $conditions);
				$user = User::find('first', array('conditions' => array('_id' => $order['user_id'])));
				$items = $order['items'];
				foreach ($items as $item) {
					if (($item['_id'] == $cartId)){
						$orderItem = Item::find('first', array(
							'conditions' => array(
								'_id' => $item['item_id']
						)));
						$orderFile[$inc]['ContactName'] = '';
						$orderFile[$inc]['Date'] = date('m/d/Y');
						if ($order['shippingMethod'] == 'ups') {
						     $orderFile[$inc]['ShipMethod'] = 'UPSGROUND';
						} else {
						     $orderFile[$inc]['ShipMethod'] = $order['shippingMethod'];
						}
						$orderFile[$inc]['RushOrder (Y/N)'] = '';
						$orderFile[$inc]['Tel'] = $order['shipping']['telephone'];
						$orderFile[$inc]['Country'] = '';
						$orderFile[$inc]['OrderNum'] = $order['order_id'];
						$orderFile[$inc]['OldSKU'] = $this->oldsku($orderItem->vendor_style, $item['size']);
						$orderFile[$inc]['SKU'] = $orderItem->sku_details[$item['size']];
						$orderFile[$inc]['Qty'] = $item['quantity']; 
						$orderFile[$inc]['CompanyOrName'] = $order['shipping']['firstname'].' '.$order['shipping']['lastname'];
						$orderFile[$inc]['Email'] = (!empty($user->email)) ? $user->email : '';
						$orderFile[$inc]['Customer PO #'] = '';
						$orderFile[$inc]['Pack Slip Comment'] = '';
						$orderFile[$inc]['Special Packing Instructions'] = '';
						$orderFile[$inc]['Address1'] =  str_replace(',', ' ', $order['shipping']['address']);
						$orderFile[$inc]['Address2'] = str_replace(',', ' ', $order['shipping']['address_2']);
						$orderFile[$inc]['City'] = $order['shipping']['city'];
						$orderFile[$inc]['StateOrProvince'] = $order['shipping']['state'];
						$orderFile[$inc]['Zip'] = $order['shipping']['zip'];
						$orderFile[$inc]['Ref1'] = $item['item_id'];
						$orderFile[$inc]['Ref2'] = $item['size'];
						$orderFile[$inc]['Ref3'] = $item['color'];
						$orderFile[$inc]['Ref4'] = $item['description'];
						$orderFile[$inc]['Customer PO #'] = $order['_id'];
						$orderFile[$inc] = array_merge($heading, $orderFile[$inc]);
						$orderFile[$inc] = $this->sortArrayByArray($orderFile[$inc], $heading);
					}
				}
				++$inc;
			}
		}
		return compact('orderFile', 'heading', 'event');
	}

	protected function getOrders($search = 'all', $conditions = array()) {
		$orders = Order::find($search, array(
			'conditions' => $conditions
		));
		return $orders;
	}

    public function googleAnalytics() {

	}

	public function productfile($eventId = null) {
		if ($eventId) {
			$productHeading = $this->_productHeading;
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
			$eventItems = Event::getItems($eventId);
			$inc = 0;
			foreach ($eventItems as $eventItem) {
				foreach ($eventItem['details'] as $key => $value) {
					$conditions = array(
						'items.item_id' => (string) $eventItem['_id'],
						'items.size' => (string) $key,
						'items.status' => array('$ne' => 'Order Canceled')
					);
					$orders = $this->getOrders('all', $conditions);
					if ($orders) {
						foreach ($orders as $order) {
							$items = $order['items'];
							foreach ($items as $item) {
								$active = (empty($item['cancel']) || $item['cancel'] != true) ? true : false;
								if (($item['item_id'] == $eventItem['_id']) && ($key == $item['size']) && $active){
									$fields[$inc]['SKU'] = $this->sku($eventItem['vendor_style'], $item['size']);
									$fields[$inc]['Description'] = strtoupper(substr($eventItem['description'], 0, 40));
									$fields[$inc]['WhsInsValue (Cost)'] = number_format($eventItem['sale_whol'], 2);
									$fields[$inc]['Description for Customs'] = $eventItem['category'];
									$fields[$inc]['ShipInsValue'] = number_format($eventItem['orig_whol'], 2);
									$fields[$inc]['Ref1'] = $item['item_id'];
									$fields[$inc]['Ref2'] = $item['size'];
									$fields[$inc]['Ref3'] = $item['color'];
									if ((int) $item['product_weight'] > 0) {
										$fields[$inc]['UOM1_Weight'] = number_format($item['product_weight'],2);
									}
									$fields[$inc] = array_merge($productHeading, $fields[$inc]);
									$productFile[$inc] = $this->sortArrayByArray($fields[$inc], $productHeading);
								}
							}
						}
						++$inc;
					}
				}
			}
		}
		return compact('productFile', 'event', 'productHeading');
	}

	public function sales() {
		FlashMessage::clear();
		if ($this->request->data) {
			$dates = $this->request->data;
			if (!empty($dates['min_date']) && !empty($dates['max_date'])) {
				//Conditions with date converted to the right timezone
				$conditions = array(
					'date_created' => array(
						'$gt' => new MongoDate(strtotime($this->request->data['min_date'])),
						'$lte' => new MongoDate(strtotime($this->request->data['max_date']))
				));
				$orderCollection = Order::collection();
				$orders = $orderCollection->find($conditions);
				$reportId = substr(md5(uniqid(rand(),1)), 1, 15);
				$collection = Report::collection();
				if ($orders) {
					foreach ($orders as $order) {
						$orderSummary = array();
						$items = $order['items'];
						$itemQuantity = 0;
						foreach ($items as $item) {
							$itemQuantity += (int) $item['quantity'];
						}
							$orderSummary['tax'] = (float) $order['tax'];
							$orderSummary['total'] = (float) $order['total'];
							switch($order['shipping']['state']){
								case 'NY':
									$state = 'NY';
									break;
								case 'PA':
									$state = 'PA';
									break;
								default:
									$state = 'Other';
							}
							$orderSummary['state'] = $state;
							$orderSummary['handling'] = (float) $order['handling'];
							if (!empty($order['overSizeHandling'])) {
								$orderSummary['overSizeHandling'] = (float) $order['overSizeHandling'];
							} else {
								$orderSummary['overSizeHandling'] = 0.00;
							}
							if (!empty($order['handlingDiscount'])) {
								$orderSummary['handlingDiscount'] = (float) $order['handlingDiscount'];
							} else {
								$orderSummary['handlingDiscount'] = 0.00;
							}
							if (!empty($order['overSizeHandlingDiscount'])) {
								$orderSummary['overSizeHandlingDiscount'] = (float) $order['overSizeHandlingDiscount'];
							} else {
								$orderSummary['overSizeHandlingDiscount'] = 0.00;
							}
							$orderSummary['quantity'] = (int) $itemQuantity;
							$orderSummary['date'] = $order['date_created'];
							$orderSummary['report_id'] = $reportId;
						$collection->save($orderSummary);
					}
				}
				$keys = new MongoCode("
					function(doc){
						return {
							'date': doc.date.toDateString(),
							'state' : doc.state
						}
					}");
				$inital = array(
					'total' => 0,
					'tax' => 0,
					'handling' => 0,
					'quantity' => 0,
					'count' => 0
				);
				$reduce = new MongoCode('function(doc, prev){
					prev.total += doc.total,
					prev.tax += doc.tax,
					prev.handling += (doc.handling + doc.overSizeHandling - doc.handlingDiscount - doc.overSizeHandlingDiscount),
					prev.quantity += doc.quantity
					prev.count += 1
					}'
				);
				$conditions = array('report_id' => $reportId);
				$results = $collection->group($keys, $inital, $reduce, $conditions);
				$details = $results['retval'];
				$keys = new MongoCode("
					function(doc){
						return {
							'date': doc.date.toDateString()
						}
					}");
				$results = $collection->group($keys, $inital, $reduce, $conditions);
				$summary = $results['retval'];
				$keys = new MongoCode("function(doc){return {}}");
				$total = $collection->group($keys, $inital, $reduce, $conditions);
				$total = $total['retval'][0];
				$collection->remove($conditions);
				if (!empty($summary)) {
					FlashMessage::write('Results Found', array('class' => 'pass'));
				} else {
					FlashMessage::write('No Results Found', array('class' => 'warning'));
				}
			} else {
				FlashMessage::write('Please enter in a valid search date', array('class' => 'warning'));
			}
		}
		return compact('details', 'summary', 'dates', 'total');
	}

	/**
	 * Generates a report of sales grouped by event for a specified date range.
	 *
	 * @return array
	 *    $results
	 *    $dates
	 */
	public function eventSales() {
		FlashMessage::clear();
		if ($this->request->data) {
			$dates = $this->request->data;
			if (!empty($dates['min_date']) && !empty($dates['max_date'])) {
				//Conditions with date converted to the right timezone
				$conditions = array(
					'date_created' => array(
						'$gt' => new MongoDate(strtotime($this->request->data['min_date'])),
						'$lte' => new MongoDate(strtotime($this->request->data['max_date']))
				));
				$orderCollection = Order::collection();
				$orders = $orderCollection->find($conditions);
				$reportId = substr(md5(uniqid(rand(),1)), 1, 15);
				$collection = Report::collection();
				if ($orders) {
					foreach ($orders as $order) {
						$orderSummary = array();
						$items = $order['items'];
						foreach ($items as $item) {
							$orderItem = array();
							$orderItem['date'] = $order['date_created'];
							$orderItem['quantity'] = (int) $item['quantity'];
							$orderItem['total'] = $item['sale_retail'] * (int) $item['quantity'];
							$orderItem['event_name'] = $item['event_name'];
							$orderItem['event_id'] = $item['event_id'];
							$orderItem['report_id'] = $reportId;
							$collection->save($orderItem);
						}
					}
				}
				$keys = new MongoCode("
					function(doc){
						return {
							'event' : doc.event_name,
							'id' : doc.event_id
						}
					}");
				$inital = array(
					'total' => 0,
					'quantity' => 0
				);
				$reduce = new MongoCode('function(doc, prev){
					prev.total += doc.total,
					prev.quantity += doc.quantity
					}'
				);
				$conditions = array('report_id' => $reportId);
				$results = $collection->group($keys, $inital, $reduce, $conditions);
				$results = $results['retval'];
				$keys = new MongoCode("function(doc){return {}}");
				$total = $collection->group($keys, $inital, $reduce, $conditions);
				$total = $total['retval'][0];
				$collection->remove($conditions);
				if (!empty($results)) {
					FlashMessage::write('Results Found', array('class' => 'pass'));
				} else {
					FlashMessage::write('No Results Found', array('class' => 'warning'));
				}
			} else {
				FlashMessage::write('Please enter in a valid search date', array('class' => 'warning'));
			}
		}
		return compact('results', 'dates', 'total');
	}

	public function saledetail() {
		FlashMessage::clear();
		$data = Model::create($this->request->data);
		if ($this->request->data) {
			$search = $this->request->data;
			$data = Model::create($search);
			if (!empty($search['min_date']) && !empty($search['max_date'])) {
				$amount = ($search['amount'] == '') ? 0 : $search['amount'];
				$dollarLimit = array("$search[range_type]" => (float) $amount);
				//Conditions with date converted to the right timezone
				$conditions = array(
					'total' => $dollarLimit,
					'date_created' => array(
						'$gt' => new MongoDate(strtotime($search['min_date'])),
						'$lte' => new MongoDate(strtotime($search['max_date']))
				));
				$shippingLimit = ($search['state'] != 'All') ? array('shipping.state' => $search['state']) : array();
				$categoryFields = array('items.category' => array('$nin' => array('accessories','apparel')));
				$categoryLimit = ($search['include_category'] == false) ? $categoryFields : array();
				$conditions = $conditions + $shippingLimit + $categoryLimit;
				$orderCollection = Order::collection();
				$orders = $orderCollection->find($conditions);
				$reportId = substr(md5(uniqid(rand(),1)), 1, 15);
				$collection = Report::collection();
				if ($orders) {
					foreach ($orders as $order) {
						$orderSummary = array();
						$items = $order['items'];
						$itemQuantity = 0;
						foreach ($items as $item) {
							$itemQuantity += $item['quantity'];
						}
						$orderSummary['gross'] = ($order['tax'] + $order['subTotal'] + $order['handling'] + $order['overSizeHandling']
							- $order['handlingDiscount'] - $order['overSizeHandlingDiscount']);
						$orderSummary['tax'] = $order['tax'];
						$orderSummary['sub_total'] = $order['subTotal'];
						$orderSummary['total'] = $order['total'];
						$orderSummary['state'] = $order['shipping']['state'];
						$orderSummary['handling'] = ($order['handling'] + $order['overSizeHandling']
							- $order['handlingDiscount'] - $order['overSizeHandlingDiscount']);
						$orderSummary['quantity'] = $itemQuantity;
						$orderSummary['date'] = $order['date_created'];
						$orderSummary['report_id'] = $reportId;
						$orderSummary['credit_used'] = (!empty($order['credit_used'])) ? $order['credit_used'] : null;
						$collection->save($orderSummary);
					}
				}
				$keys = new MongoCode("
					function(doc){
						return {
							'date': doc.date.getMonth(),
							'state' : doc.state
						}
					}");
				$inital = array(
					'credit_used' => 0,
					'gross' => 0,
					'total' => 0,
					'tax' => 0,
					'handling' => 0,
					'quantity' => 0,
					'count' => 0,
					'sub_total' =>0,
					'credit_used' =>0
				);
				$reduce = new MongoCode('function(doc, prev){
					prev.gross += doc.gross,
					prev.total += doc.total,
					prev.tax += doc.tax,
					prev.handling += doc.handling,
					prev.quantity += doc.quantity,
					prev.sub_total += doc.sub_total,
					prev.credit_used += doc.credit_used,
					prev.count += 1
					}'
				);
				$conditions = array('report_id' => $reportId);
				$results = $collection->group($keys, $inital, $reduce, $conditions);
				$details = $results['retval'];
				$keys = new MongoCode("
					function(doc){
						return {
							'date': doc.date.getMonth()
						}
					}");
				$results = $collection->group($keys, $inital, $reduce, $conditions);
				$summary = $results['retval'];
				$keys = new MongoCode("function(doc){return {}}");
				$total = $collection->group($keys, $inital, $reduce, $conditions);
				$total = (!empty($total['retval'][0])) ? $total['retval'][0] : null;
				$collection->remove($conditions);
				if (!empty($summary)) {
					FlashMessage::write('Results Found', array('class' => 'pass'));
				} else {
					FlashMessage::write('No Results Found', array('class' => 'warning'));
				}
			} else {
				FlashMessage::write('Please enter in a valid search date', array('class' => 'warning'));
			}
		}
		return compact('details', 'summary', 'dates', 'total', 'data');
	}

	/**
	* Generates a csv file with the number of registered person for a time fixed
	*/
	public function	registeredUsers(){
		if ($this->request->data) {
			$search = $this->request->data;
			if (!empty($search['min_date']) && !empty($search['max_date'])) {

				$conditions = array('created_date' => array(
					'$gt' => new MongoDate(strtotime($search['min_date'])),
					'$lte' => new MongoDate(strtotime($search['max_date']))));
				$userCollection = User::collection();
				$userCollection->ensureIndex(array('_id' => 1));
				$userCollection->ensureIndex(array('email' => 1));
				$userCollection->ensureIndex(array('firstname' => 1));
				$userCollection->ensureIndex(array('lastname' => 1));
				$userCollection->ensureIndex(array('created_date' => 1));

				$reg_usrs = $userCollection->find($conditions);

				//Create the array that will simulate a CSV file
				$users[0]["firstname"] = "firstname" . ",";
				$users[0]["lastname"] = "lastname" . ",";
				$users[0]["email"] = "email";
				$i = 1;
				foreach($reg_usrs as $reg_usr){
					$users[$i]["firstname"] = $reg_usr['firstname'] . ",";
					$users[$i]["lastname"] = $reg_usr['lastname'] . ",";
					$users[$i]["email"] = $reg_usr['email'];
					$i++;
				}
				if ($i > 2) {
					$this->render(array('layout' => false, 'data' => $users));
				}
			}
		}
	}

	/**
	* Generates a table which print the total of sales for One event by hours
	* It will also give the total by days, and the average sales by hours
	*/
	public function event() {
		if ($this->request->args) {
			/**** CONFIGURATION ****/
			$ordersCollection = Order::collection();
			$eventsCollection = Event::collection();
			$data = $this->request->args;
			/**** OPTIMISATION ****/
			$eventsCollection->ensureIndex(array('start_date' => 1));
			$eventsCollection->ensureIndex(array('end_date' => 1));
			$ordersCollection->ensureIndex(array('date_created' => 1));
			//Check if we have start and end date range
			if (!empty($data[1]) && !empty($data[2])) {
				/**** CONFIGURATION ***/
				$valid_1st_hour = false;
				$idx = 0;
				$total = 0;
				$quantity = 0;
				$event_id = $data[0];
				$hour = 0;
				//start date
				$start_year = date("Y",$data[1]);
				$start_month = date("n",$data[1]);
				$start_day = date("j",$data[1]);;
				$start_hour = 0;
				$start_date = $data[1];
				//end date
				$end_year = date("Y",$data[2]);
				$end_month = date("n",$data[2]);
				$end_day = date("j",$data[2]);;
				$end_hour = 0;
				$end_date = $data[2];
				/*********************************SCRIPT******************************************/
				$event = $eventsCollection->findOne(array("_id" => new MongoId($event_id)));
				$event_name = $event["name"];
				do {
					$start_for_selected_event = mktime(($start_hour + $hour), 0, 0, $start_month, $start_day, $start_year);
					$end_for_selected_event = mktime(($start_hour + $hour + 1), 0, 0, $start_month, $start_day, $start_year);
					if((date("Y",$event["start_date"]->sec) == date("Y",$start_for_selected_event))
						&& (date("n",$event["start_date"]->sec) == date("n",$start_for_selected_event))
						&& (date("j",$event["start_date"]->sec) == date("j",$start_for_selected_event))
						&& (date("H",$event["start_date"]->sec) == date("H",$start_for_selected_event))) {
							$valid_1st_hour = true;
						} else {
							$valid_1st_hour = false;
					}
					if((($event["start_date"]->sec <=  $start_for_selected_event) && ($event["end_date"]->sec >=  $start_for_selected_event)) || ($valid_1st_hour)) {
						/**** Query Events **/
						$conditions_order = array(
								'date_created' => array(
									'$gt' => new MongoDate($start_for_selected_event),
									'$lte' => new MongoDate($end_for_selected_event)),
								'items.event_id' => $event_id
						);
						/**** QUERY **/
						$result_order = $ordersCollection->find($conditions_order, array('items' => 1));
						/***LOOP***/
						foreach ($result_order as $order) {
							foreach ($order["items"] as $item) {
								if ($item["event_id"] == $event_id) {
									$total += ($item["sale_retail"] * (integer) $item["quantity"] );
									$quantity += (integer) $item["quantity"];
								}
							}
						}
						$datas = array(
							"event_id" => $event_id,
							"total" => $total,
							"date" => $start_for_selected_event,
							"quantity" => $quantity
						);
						$result[] = $datas;
						$total = 0;
						$quantity = 0;
					}
					$hour ++;
				} while ($end_date != $end_for_selected_event);
				$idx = count($result);
				/**** RESULT **/
				foreach($result as $res) {
					$hr = date( "H", $res["date"]);
					$dd = date( "d", $res["date"]);
					$dm = date( "m", $res["date"]);
					//Set
					$jm = $dd . "/" . $dm;
					if(empty($stat[$jm][$hr])) {
						$stat[$jm][$hr]['number'] = 0;
						$stat[$jm][$hr]['total'] = 0;
						$stat[$jm][$hr]['quantity'] = 0;
					}
					if($res["total"] != 0) {
						$stat[$jm][$hr]['total'] += $res["total"];
						$stat[$jm][$hr]['quantity'] += $res["quantity"];
						$stat[$jm][$hr]['number']++;
					}
					if(empty($total_days[$jm])) {
						$total_days[$jm]['total'] = 0;
						$total_days[$jm]['quantity'] = 0;
					}
					if(empty($total)) {
						$total = 0;
						$total_quantity = 0;
					}
					//TOTAL DAYS
					$total_days[$jm]['total'] += $res["total"];
					$total_days[$jm]['quantity'] += $res["quantity"];
					$total += $res["total"];
					$total_quantity += $res["quantity"];
					if(empty($total_hours[$hr])) {
						$total_hours[$hr]["total"] = 0;
						$total_hours[$hr]["quantity"] = 0;
						$total_hours[$hr]["days"] = 0;
					}
					//TOTAL HOURS
					$total_hours[$hr]["total"] += $res["total"];
					$total_hours[$hr]["quantity"] += $res["quantity"];
					$total_hours[$hr]["days"]++;
				}
				foreach($total_hours as $key => $value) {
					$total_hours[$key]["average"] = round(($value["total"] / $value["days"]), 2);
				}
				//PRINT SETUP
				$hours_setup = array(
					"00","01","02","03","04","05","06","07","08","09","10","11",
					"12","13","14","15","16","17","18","19","20","21","22","23"
				);
			}
		}
		return compact('stat', 'total_days', 'total_hours', 'hours_setup','end_date','start_date', 'total', 'total_quantity', 'event_name');
	}

	/**
	* Generates graphics that shows the evolution of event sales.
	* It started from the days of launching to 4 days later.
	*/
	public function salesDays() {
		if ($this->request->data) {
			$eventsCollection = Event::collection();
			$ordersCollection = Order::collection();
			/**** OPTIMISATION ****/
			$eventsCollection->ensureIndex(array('start_date' => 1));
			$eventsCollection->ensureIndex(array('end_date' => 1));
			$ordersCollection->ensureIndex(array('date_created' => 1));
			$data = $this->request->data;
			if (!empty($data['min_date']) && !empty($data['max_date'])) {
				//start date
				$start_day = date("j",strtotime($data['min_date']));
				$start_month = date("n",strtotime($data['min_date']));
				$start_year = date("Y",strtotime($data['min_date']));
				$start_date = mktime(0, 0, 0, $start_month, $start_day, $start_year);
				//end date
				$end_year = date("Y",strtotime($data['max_date']));
				$end_month = date("n",strtotime($data['max_date']));
				$end_day = date("j",strtotime($data['max_date']));
				$end_date = mktime(0, 0, 0, $end_month, $end_day, $end_year);
				$day = 0;
				$conditions_event = array(
					'start_date' => array(
					'$gt' => new MongoDate($start_date),
					'$lte' => new MongoDate($end_date)
				));
				$events = $eventsCollection->find($conditions_event, array('start_date' => 1));
				foreach($events as $event) {
					$day = 0;
					$weekday = date('l',$event['start_date']->sec);
					$selected_day = date('j', $event['start_date']->sec);
					$selected_month = date('n', $event['start_date']->sec);
					$selected_year = date('Y', $event['start_date']->sec);
				 	do {
						$start_for_selected_order = mktime(0, 0, 0, $selected_month, ($selected_day + $day), $selected_year);
						$end_for_selected_order = mktime(0, 0, 0, $selected_month, ($selected_day + $day + 1), $selected_year);
						if($day == 0) {
							$weekday = date('l',$start_for_selected_order);
						}
						$conditions_order = array(
							'date_created' => array(
								'$gt' => new MongoDate($start_for_selected_order),
								'$lte' => new MongoDate($end_for_selected_order)
							),
							'items.event_id' => (string) $event["_id"]
						);
						$orders = $ordersCollection->find($conditions_order);
						if(empty($graphic_datas[$weekday][5])) {
							$graphic_datas[$weekday][5] = 0;
						}
						if(empty($graphic_datas[$weekday][$day])) {
							$graphic_datas[$weekday][$day] = 0;
						}
						foreach($orders as $order) {
							foreach($order['items'] as $item) {
								if($item["event_id"] == $event["_id"]) {
									$graphic_datas[$weekday][5] += ($item["sale_retail"] * $item["quantity"]);
									$graphic_datas[$weekday][$day] += ($item["sale_retail"] * $item["quantity"]);
								}
							}
						}
						$day++;
					} while ($day != 5);
				}
			}
			foreach($graphic_datas as $day => $data) {
					$chart_datas[$day][0][0] = '$' . round($graphic_datas[$day][0],2);
					$chart_datas[$day][1][0] = '$' . round($graphic_datas[$day][1],2);
					$chart_datas[$day][2][0] = '$' . round($graphic_datas[$day][2],2);
					$chart_datas[$day][3][0] = '$' . round($graphic_datas[$day][3],2);
					if (!empty($graphic_datas[$day][4])) {
						$chart_datas[$day][4][0] = '$' . round($graphic_datas[$day][4],2);
						$chart_datas[$day][4][1] = ($graphic_datas[$day][4] / $graphic_datas[$day][5]) * 100;
					}
					$chart_datas[$day][0][1] = ($graphic_datas[$day][0] / $graphic_datas[$day][5]) * 100;
					$chart_datas[$day][1][1] = ($graphic_datas[$day][1] / $graphic_datas[$day][5]) * 100;
					$chart_datas[$day][2][1] = ($graphic_datas[$day][2] / $graphic_datas[$day][5]) * 100;
					$chart_datas[$day][3][1] = ($graphic_datas[$day][3] / $graphic_datas[$day][5]) * 100;
			}
			foreach($chart_datas as $key => $value) {
				# Create Column3D chart Object
				$DailyCharts[$key] = new FusionCharts("Column3D","700","350");
				#  Set chart attributes
				$strParam = "caption=Sales - Total Revenues:  $" . round($graphic_datas[$key][5],2) . ";xAxisName=Days;yAxisName=Percentage;numberSuffix=%";
				$DailyCharts[$key]->setChartParams($strParam);
				# add chart values and  category names
				$DailyCharts[$key]->addChartDataFromArray($value);
			}
		}
		$days = array(
			'0' => 'Sunday',
			'1' => 'Monday',
			'2'=> 'Tuesday',
			'3' => 'Wednesday',
			'4' => 'Thursday',
			'5' => 'Friday',
			'6' => 'Saturday'
		);
		return compact('DailyCharts','days','start_date','end_date');
	}

	/**
	* Generates graphics that shows behaviour of user with :
	* Free shipping discount for the first order
	* $10 off for the 2nd purchase of $50+
	*/
	public function services() {
		$usersCollection = User::connection()->connection->users;
		$ordersCollection = Order::collection();
		$usersCollection->ensureIndex(array('created_date' => 1));
		$usersCollection->ensureIndex(array('purchase_count' => 1));
		$ordersCollection->ensureIndex(array('date_created' => 1));
		$ordersCollection->ensureIndex(array('total' => 1));
		#RUNNING
		#GET DATES
		$month_plus = 0;
		$start_month = 01;
		$start_year = 2011;
		$now = getdate();
		$actual_month = mktime(0, 0, 0, ($start_month + $month_plus), 1, $start_year);
		$next_month = mktime(0, 0, 0, ($start_month + 1 + $month_plus), 1, $start_year);
		do {
			$month = date("F", $actual_month);
			$year = date("Y", $actual_month);
			$statistics[$year][$month]["registered_user_purch_30"] = 0;
			$statistics[$year][$month]["registered_user_purch_23_30"] = 0;
			$statistics[$year][$month]["registered_user_purch_0_23"] = 0;
			$statistics[$year][$month]["registered_user_no_2purch"] = 0;
			$statistics[$year][$month]["registered_user_2purch_50_0_15"] = 0;
			$statistics[$year][$month]["registered_user_2purch_0_15"] = 0;
			$statistics[$year][$month]["registered_user_2purch_50_15_30"] = 0;
			$statistics[$year][$month]["registered_user_2purch_15_30"] = 0;
			$statistics[$year][$month]["registered_user_2purch_30"] = 0;
			#REGISTERED USERS
			$conditions_A = array('created_date' => array(
									'$gt' => new MongoDate($actual_month),
									'$lte' => new MongoDate($next_month)
			));
			$statistics[$year][$month]["registered_user"] = $usersCollection->count($conditions_A);
			#REGISTERED USERS / No Purchases
			$conditions_B = array('purchase_count' => array('$exists' => false),
									'created_date' => array(
										'$gt' => new MongoDate($actual_month),
										'$lte' => new MongoDate($next_month)
			));
			$statistics[$year][$month]["registered_user_no_purch"] = $usersCollection->count($conditions_B);
			$conditions_C = array('purchase_count' => array('$exists' => true),
									'created_date' => array(
										'$gt' => new MongoDate($actual_month),
										'$lte' => new MongoDate($next_month)
			));
			$users_C = $usersCollection->find($conditions_C);
			foreach ($users_C as $user) {
				$key = 0;
				$key_2nd = 0;
				# 1st Purchase between 0 to 23 days
				$day_target_23 = mktime(
									date("G", $user['created_date']->sec),
									date("i", $user['created_date']->sec),
									date("s", $user['created_date']->sec),
									date("m", $user['created_date']->sec),
									date("d", $user['created_date']->sec) + 23,
									date("Y", $user['created_date']->sec)
							);
				$day_target_30 = mktime(
									date("G", $user['created_date']->sec),
									date("i", $user['created_date']->sec),
									date("s", $user['created_date']->sec),
									date("m", $user['created_date']->sec),
									date("d", $user['created_date']->sec) + 30,
									date("Y", $user['created_date']->sec)
							);
				$conditions_order = array("user_id" => (string) $user["_id"]);
				$orders = $ordersCollection->find($conditions_order,array('date_created' => 1, 'total' => 1));
				foreach($orders as $order) {
					if($key == 0) {
						$day_2_target_15 = mktime(
											date("G", $order['date_created']->sec),
											date("i", $order['date_created']->sec),
											date("s", $order['date_created']->sec),
											date("m", $order['date_created']->sec),
											date("d", $order['date_created']->sec) + 15,
											date("Y", $order['date_created']->sec)
									);
						$day_2_target_30 = mktime(
											date("G", $order['date_created']->sec),
											date("i", $order['date_created']->sec),
											date("s", $order['date_created']->sec),
											date("m", $order['date_created']->sec),
											date("d", $order['date_created']->sec) + 30,
											date("Y", $order['date_created']->sec)
									);
						if($order['date_created']->sec > $day_target_30) {
							$statistics[$year][$month]["registered_user_purch_30"]++;
						} else if (($order['date_created']->sec < $day_target_30) && ($order['date_created']->sec > $day_target_23)) {
							$statistics[$year][$month]["registered_user_purch_23_30"]++;
							$key_2nd++;
						} else if (($order['date_created']->sec < $day_target_23)) {
							$statistics[$year][$month]["registered_user_purch_0_23"]++;
							$key_2nd++;
						}
					}
					//2nd purchase
					if(($key == 1) && ($key_2nd == 1)) {
						if($order['total'] < 50) {
							if($order['date_created']->sec > $day_2_target_30) {
								$statistics[$year][$month]["registered_user_2purch_30"]++;
							} else if (($order['date_created']->sec <= $day_2_target_30) && ($order['date_created']->sec > $day_2_target_15)) {
								$statistics[$year][$month]["registered_user_2purch_15_30"]++;
							} else if (($order['date_created']->sec <= $day_2_target_15)) {
								$statistics[$year][$month]["registered_user_2purch_0_15"]++;
							}
						} else {
							if($order['date_created']->sec > $day_2_target_30) {
								$statistics[$year][$month]["registered_user_2purch_30"]++;
							} else if (($order['date_created']->sec <= $day_2_target_30) && ($order['date_created']->sec > $day_2_target_15)) {
								$statistics[$year][$month]["registered_user_2purch_50_15_30"]++;
							} else if (($order['date_created']->sec <= $day_2_target_15)) {
								$statistics[$year][$month]["registered_user_2purch_50_0_15"]++;
							}
						}
					}
					$key++;
				}
				if(($key == 1) && ($key_2nd == 1)) {
					$statistics[$year][$month]["registered_user_no_2purch"]++;
				}
			}
			$month_plus++;
			$actual_month = mktime(0, 0, 0, ($start_month + $month_plus), 1, $start_year);
			$next_month = mktime(0, 0, 0, ($start_month + 1 + $month_plus), 1, $start_year);
		} while ($actual_month < $now[0]);
		/**** 1ST Charts ****/
		$i = 0;
		foreach($statistics as $year => $stats) {
			foreach($stats as $key => $statistic) {
			//Categories
			$arrCatNames[0 + $i] = $key.' '.$year;
			//Series
			$arrData[0][0] = 'w/ no Purchases';
			$arrData[0][1] = "color=69D2E7";
			$arrData[1][0] = '1st purchase 0-23d';
			$arrData[1][1] = "color=A7DBD8";
			$arrData[2][0] = '1st purchase 23-30d';
			$arrData[2][1] = "color=E0E4CC";
			$arrData[3][0] = '1st purchase > 30d';
			$arrData[3][1] = 'color=F38630';
			//datas
			$arrData[0][2 + $i] = 100 * ($statistic["registered_user_no_purch"] / $statistic["registered_user"]);
			$arrData[1][2 + $i] = 100 * ($statistic["registered_user_purch_0_23"] / $statistic["registered_user"]);
			$arrData[2][2 + $i] = 100 * ($statistic["registered_user_purch_23_30"] / $statistic["registered_user"]);
			$arrData[3][2 + $i] = 100 * ($statistic["registered_user_purch_30"] / $statistic["registered_user"]);
			# Create Column3D chart Object
			$ServiceCharts = new FusionCharts("StackedColumn3D","700","350");
			#  Set chart attributes
			$strParam = "yAxisName=Users;numberSuffix=%";
			$ServiceCharts->setChartParams($strParam);
			# add chart values and  category names
			$ServiceCharts->addChartDataFromArray($arrData,$arrCatNames);
			/**** 2ND Charts ****/
			//Categories
			$arrCatNames_2[0 + $i] =  $key.' '.$year;
			//titles
			$arrData_2[0][0] = 'No 2nd purchase';
			$arrData_2[0][1] = "color=CFF09E";
			$arrData_2[1][0] = 'Discount 0-15d';
			$arrData_2[1][1] = "color=A8DBA8";
			$arrData_2[2][0] = '0-15d';
			$arrData_2[2][1] = "color=79BD9A";
			$arrData_2[3][0] = 'Discount 15-30d';
			$arrData_2[3][1] = "color=3B8686";
			$arrData_2[4][0] = '15-30d';
			$arrData_2[4][1] = "color=0B486B";
			$arrData_2[5][0] = '> 30d';
			$arrData_2[5][1] = "color=FF9900";
			//datas
			$free_shipping_user = $statistic["registered_user_purch_0_23"] + $statistic["registered_user_purch_23_30"];
			$arrData_2[0][2 + $i] = 100 * ($statistic["registered_user_no_2purch"] / $free_shipping_user);
			$arrData_2[1][2 + $i] = 100 * ($statistic["registered_user_2purch_50_0_15"] / $free_shipping_user);
			$arrData_2[2][2 + $i] = 100 * ($statistic["registered_user_2purch_0_15"] / $free_shipping_user);
			$arrData_2[3][2 + $i] = 100 * ($statistic["registered_user_2purch_50_15_30"] / $free_shipping_user);
			$arrData_2[4][2 + $i] = 100 * ($statistic["registered_user_2purch_15_30"] / $free_shipping_user);
			$arrData_2[5][2 + $i] = 100 * ($statistic["registered_user_2purch_30"] / $free_shipping_user);
			# Create Column3D chart Object
			$Service2ndCharts = new FusionCharts("StackedColumn3D","700","350");
			#  Set chart attributes
			$strParam2 = "yAxisName=Users;numberSuffix=%";
			$Service2ndCharts->setChartParams($strParam2);
			# add chart values and  category names
			$Service2ndCharts->addChartDataFromArray($arrData_2,$arrCatNames_2);
			$i++;
			}
		}
		return compact('ServiceCharts','Service2ndCharts');
	}

	private function generateConditions(array $data = array()){
		extract($data);
		$conditions = array();
		$dateField = 'date_created';
		switch ($name) {
			case 'trendytogs':
				$conditions = array(
					'trendytogs_signup' => array('$exists' => true)
				);
				$dateField = 'date_created';
			break;
			case 'keyade':
				$conditions = array(
					'$or' => array(
							array(
								'keyade_referral_user_id' => array('$ne' => NULL )
							),
							array(
								'keyade_user_id' => array('$ne' => NULL )
							)
					)
				);
				$dateField = 'created_date';
				if (!empty($date)) {
					$conditions = $conditions + $date;
				}
			break;
			default:
				$conditions = array(
					'invited_by' => $affiliate,
				);
				$dateField = 'created_date';
				if (!empty($date)) {
					$conditions = $conditions + $date;
				}
			break;
		}
		return compact('conditions','dateField');
	}
}

?>