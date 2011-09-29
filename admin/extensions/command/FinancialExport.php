<?php

namespace admin\extensions\command;

use admin\models\User;
use admin\models\Order;
use admin\models\Event;
use admin\models\Item;
use admin\models\Credit;
use admin\models\Promocode;
use admin\models\ProcessedOrder;
use admin\models\OrderShipped;
use lithium\core\Environment;
use Mongo;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;
use MongoCursor;
use lithium\data\Model;
use lithium\util\String;
use SimpleXMLElement;

/**
 * Simple export script for financial data needed by CFO.
 *
 * The data being used here is being changed into a more columnized fashion so it
 * can be uploaded into another database.
 */
class FinancialExport extends \lithium\console\Command  {

	/**
	 * The environment to use when running the command. db='development' is the default.
	 * Set to 'production' if running live when using a cronjob.
	 */
	public $env = 'development';

	/**
	 * Directory that holds temporary files.
	 *
	 * @var string
	 */
	public $tmp = '/resources/totsy/finance/';

	/**
	 * Will generate xml of orders going back to Nov 1 - one day before the present
	 *
	 * @var string
	 */
	public $historical = 'false';

	/**
	 * The summary header to be used in the summary CSV export file.
	 */
	protected $summaryHeader = array(
		'_id',
		'name',
		'address',
		'credit_used',
		'handling',
		'order_id',
		'overSizeHandling',
		'promo_discount',
		'promo_code',
		'promo_type',
		'promo-code_amt',
		'service',
		'subTotal',
		'tax',
		'total',
		'gross_shipping_amt',
		'net_shipping_amt',
		'user_id',
		'city',
		'state',
		'zip',
		'order_date',
		'authKey',
		'auth_confirmation',
		'auth_error',
		'payment_type',
		'payment_date',
		'estimated_ship_date',
		'actual_ship_date',
		'ship_records'
	);

	/**
	 * The detailed header to be used in the detailed CSV file.
	 */
	protected $detailHeader = array(
		"_id",
		"category",
		"sub_category",
		"vendor",
		"color",
		"size",
		"description",
		"item_id",
		"quantity",
		"sale_retail",
		"sale_wholesale",
		"event_id",
		"event_start_date",
		"event_end_date",
		"order_id_short",
		"order_id_fk"
	);
	protected $creditHeader = array(
	    '_id',
	    'customer_id',
	    'credit_type',
	    'reason',
	    'description',
	    'credit_amount',
	    'issued_date'
	);

	protected $creditUnsetKey = array(
		'date_created',
		'type',
		'credit_amount',
		'created',
		'error',
		'user_id'
	);

	/**
	 * Some standard order data fields to be unset.
	 */
	protected $orderUnsetKey = array(
		'billing',
		'date_created',
		'items',
		'card_type',
		'ship_date'
	);

	/**
	 * Fields in the item array that should be unset.
	 */
	protected $itemUnsetKey = array(
		'expires',
		'primary_image',
		'product_weight',
		'event_name',
		'status',
		'url',
		'cancel',
		'line_number',
		'discount_exempt'
	);

	/**
	 * Find all the orders that haven't been shipped which have stock status.
	 */
	public function run() {
		Environment::set($this->env);
		$this->tmp = LITHIUM_APP_PATH . $this->tmp;
		/**
		 * The query was timing out without an index running locally on a MBP. Although this won't be an
		 * issue running on production just note that a new index will be created.
		 */
		Order::collection()->ensureIndex(array('date_created' => -1));
		Promocode::collection()->ensureIndex(array('code' => -1));
		OrderShipped::collection()->ensureIndex(array('OrderNum' => 1));
		/**
		 * Going for all the orders that were created after Nov 1, 2010. This may need to be dynamically
		 * setup for future queries via cron.
		 */
		$orderConditions = array(
			'date_created' => array('$gte' => new MongoDate(strtotime('Jan 1, 2011')), '$lte' => new MongoDate(strtotime("now")))
		//	'payment_date' => array('$exists' => true),
		//	'payment_date' => array('$gte' => new MongoDate(strtotime('May 28, 2011')), '$lte' => new MongoDate(strtotime('June 7, 2011')))
		);

		$fields = array(
			'billing',
			'authKey',
			'card_type',
			'auth_confirmation',
			'auth_error',
			'handling',
			'items',
			'order_id',
			'overSizeHandling',
			'subTotal',
			'total',
			'date_created',
			'promo_code',
			'promo_discount',
			'credit_used',
			'user_id',
			'ship_date',
			'ship_records',
			'tax',
			'payment_date',
			'service',
			'shipping'
		);

		$orders = Order::collection()->find($orderConditions, $fields)->sort(array('date_created' => -1));

		$ordersShipped = OrderShipped::collection();
		$orderSummary = $orderDetails = $creditDetails = array();
		/**
		 * Setup filenames for the order summary and epxort functionality.
		 */
		$this->time = date('m-d-Y');
		$orderSummaryFile = $this->tmp . 'OrdSummary_' . $this->time . '.xml';
		$orderDetailFile = $this->tmp . 'OrdDetail_' . $this->time . '.xml';
		$creditDetailFile = $this->tmp . 'CredDetail_' . $this->time . '.xml';
//		$fpSummary = fopen($orderSummaryFile, 'w');
//		$fpDetail = fopen($orderDetailFile, 'w');
//		$cpDetail = fopen($creditDetailFile, 'w');
		/**
		 * Setup the file headers
		 */
//		fputcsv($fpSummary, $this->summaryHeader,',',chr(34));
//		fputcsv($fpDetail, $this->detailHeader,',',chr(34));
//		fputcsv($cpDetail, $this->creditHeader,',',chr(34));
		/**
		 * Build the files
		 */
		foreach ($orders as $order) {
			$orderItems = $order['items'];
			if (array_key_exists('authKey', $order)) {
			   $order['authKey'] = $order['authKey'];
			} else {
			   $order['authKey'] = "none";
			}

			if (array_key_exists('card_type', $order)) {
			   $order['payment_type'] = $order['card_type'];
			} else {
			    $order['payment_type'] = "none";
			}
			if (array_key_exists('city', $order['billing'])) {
			    $order['city'] = $order['billing']['city'];
			} else {
			    $order['city'] = 'N/A';
			}
			if (array_key_exists('state', $order['billing'])) {
			    $order['state'] = $order['billing']['state'];
			} else {
			    $order['state'] = 'N/A';
			}
			if (array_key_exists('zip', $order['billing'])) {
			    $order['zip'] = $order['billing']['zip'];
			} else {
			    $order['zip'] = 'N/A';
			}
			$order['order_date'] = date('m/d/Y', $order['date_created']->sec);
			if (!empty($order['payment_date'])) {
				$order['payment_date'] = date('m/d/Y',$order['payment_date']->sec);
			} else {
				$order['payment_date'] = 0;
			}
			if (array_key_exists('ship_date', $order)) {
				$order['estimated_ship_date'] =
				    (is_int($order['ship_date'])) ? date('m/d/Y', $order['ship_date']) : date('m/d/Y', $order['ship_date']->sec);
			} else {
				$order['estimated_ship_date'] = 0;
			}
			$order['net_shipping_amt'] = (float) $order['subTotal'] + (float) $order['handling'] + (float) $order['overSizeHandling'];
			$order['gross_shipping_amt'] = $order['net_shipping_amt'];
			if (array_key_exists('service', $order)) {
				if (in_array('freeshipping', $order['service'])){
				    $order['service'] = 'freeshipping';
				    $order['gross_shipping_amt'] -= 7.95;
				} else if(in_array('10off50', $order)) {
				    $order['service'] = '10off50';
				     $order['gross_shipping_amt'] -= 10;
				}else {
				    $order['service'] = "none";
				}
			} else {
				$order['service'] = "none";
			}
            if (array_key_exists('promo_code', $order)) {
                $promocode = Promocode::find('first', array('conditions' => array('code' => new MongoRegex("/" . $order['promo_code'] . "/i"))));
                $order['promo_type'] = $promocode['type'];
                $order['promo-code_amt'] = $promocode['discount_amount'];
			}
			/*
			* Grab credit information
			*/
			if (array_key_exists('credit_used', $order)){
				    $creditCollection = Credit::collection()->find(array(
				    '$or' => array(
				        array('customer_id' => $order['user_id']),
				        array('user_id' => $order['user_id'])
				    )));
				    foreach ($creditCollection as $credit) {
				        if (array_key_exists('date_created', $credit)) {
				            $credit['issue_date'] = date('m/d/Y', $credit['date_created']->sec);
				        } else {
				            $credit['issue_date'] = date('m/d/Y', $credit['created']->sec);
				        }
				        if (array_key_exists('type', $credit)) {
				            $credit['credit_type'] = $credit['type'];
				        }

				        if (array_key_exists('user_id', $credit)) {
				            $credit['customer_id'] = $credit['user_id'];
				        }

				        if (array_key_exists('amount', $credit)) {
				            $credit['credit_amount'] = $credit['amount'];
				        }
				        if (!array_key_exists('description', $credit)) {
				            $credit['description'] = "none";
				        }
				        if (!array_key_exists('reason', $credit))  {
				            $credit['reason'] = "none";
				        }
				        $userCredit = $this->sortArrayByArray($credit, $this->creditHeader);
				        foreach($this->creditUnsetKey as $key) {
				            unset($userCredit[$key]);
				        }

                        $creditDetails[] = $userCredit;
                        //@todo don't this part any more - credit details
                        /*
                        if (!empty($userCredit)){
				            fputcsv($cpDetail, $userCredit,',',chr(34));
				        }
				        */
				    }
				    $order['gross_shipping_amt'] += (array_key_exists('credit_used', $order)) ? $order['credit_used']:0;
				    $order['gross_shipping_amt'] += (array_key_exists('promo_discount', $order)) ? $order['promo_discount']:0;
				}
			/*
			* Get credit, promocodes,  and oversize handling information
			*/
			foreach ($order as $key => $value) {
				$checkList = array('credit_used', 'promo_code', 'promo_discount', 'overSizeHandling');
				foreach ($checkList as $value) {
					if (empty($order["$value"])) {
						$order["$value"] = 0;
					}
				}
				if (is_array($value) || in_array($key, $this->orderUnsetKey)) {
                    unset($order[$key]);
                }
			}
			// Check if this order has a 'shipped' record
			$shipRecord = $ordersShipped->findOne(array('$or' => array(
                array('OrderNum' => $order['order_id'])
            )));
			if (array_key_exists('ship_records', $order)) {
			    $order['ship_records'] = "Yes";
			} else {
			    $order['ship_records'] = "No";
			}

			$order['name'] = $order['shipping']['lastname'] . ', ' . $order['shipping']['firstname'];

			$order['address'] = $order['shipping']['address'] . ' ' . $order['shipping']['address_2'] .
			    ', ' . $order['shipping']['city'] . ', ' . $order['shipping']['state'] . ' ' .
			    $order['shipping']['zip'];
			unset($order['shipping']);

			if ($shipRecord) {
				$order['actual_ship_date'] = date("m/d/Y", $shipRecord['ShipDate']->sec);
			} else {
				$order['actual_ship_date'] = 0;
			}
			if (array_key_exists('auth_confirmation', $order)) {
			    $order['auth_confirmation'] = $order['auth_confirmation'];
			} else {
			    $order['auth_confirmation'] = "none";
			}
			if (array_key_exists('auth_error', $order) ) {
			    if (is_array($order['auth_error'])){
			        $order['auth_error'] = implode("/", $order['auth_error']);
			    } else {
			        $order['auth_error'] = $order['auth_error'];
			    }
			} else {
			    $order['auth_error'] = "none";
			}
			$order = $this->sortArrayByArray($order, $this->summaryHeader);
			//@todo don't need this anymore - order summary
			$orderSummary[] = $order;
		//	fputcsv($fpSummary, $order,',',chr(34));

			//Get detailed information about the items sold
			foreach ($orderItems as $item) {
				$itemRecord = Item::collection()->findOne(array('_id' => new MongoId($item['item_id'])));
				foreach ($this->itemUnsetKey as $key) {
					unset($item[$key]);
				}

				if (empty($item['color'])) {
					$item['color'] = "none";
				}
				if (empty($item['category'])) {
					$item['category'] = "none";
				}
				if (empty($item['size'])) {
					$item['size'] = "none";
				}

				$event = Event::collection()->findOne(array('_id' => new MongoId($item['event_id'])));

				if (array_key_exists('vendor', $item)) {
				    $item['vendor'] = $item['vendor'];
				} else {
				    $item['vendor'] = $event['name'];
				}
				if (!empty($itemRecord) && array_key_exists('sub_category', $itemRecord)) {
				    $item['sub_category'] = $itemRecord['sub_category'];
				} else {
				    $item['sub_category'] = "none";
				}
				$item['event_start_date'] = $start_date;
				$item['event_end_date'] = $end_date;
				$item['order_id_fk'] = $order['_id'];
				$item['order_id_short'] = $order['order_id'];
				$item['sale_wholesale'] = $itemRecord['sale_whol'];
				$item = $this->sortArrayByArray($item, $this->detailHeader);

				$orderDetails[] = $item;
				// @todo don't need this any more - order details - unless csv is requested again
				//fputcsv($fpDetail, $item,',',chr(34));
			}
		}
		$this->createXMLDoc('order_summary', $orderSummary, $orderSummaryFile);
		$this->createXMLDoc('order_detail', $orderDetails, $orderDetailFile);
		$this->createXMLDoc('credit_detail', $creditDetails, $creditDetailFile);
		//don't need this anymore
		//fclose($fpSummary);
		//fclose($fpDetail);
	}

	/**
	 * Sort an array by a hearder
	 */
	public function sortArrayByArray($array, $orderArray) {
		$ordered = array();
		foreach($orderArray as $key) {
			if(array_key_exists($key,$array)) {
				$ordered[$key] = (string) $array[$key];
				unset($array[$key]);
			}
		}
		return $ordered + $array;
	}
	/**
	* Creates XML from data passed in
	* @param string $type type of information: used for the root tags of the xml file
	* @param array $records multidimensional array holding the records to converted to xml
	* @param string $filename name of the xm file
	**/
	public function createXMLDoc($type, $records, $filename) {
	    $xml = new SimpleXMLElement("<$type></$type>");
	    foreach($records as $record) {
	        $recordTag = $xml->addChild('record');
	        foreach($record as $key => $value) {
	            //SimpleXMLElement doesn't like ampersand for some reason so I am replacing it with 'and'
	           $record[$key] = preg_replace('/&/','and',$record[$key]);
	            $recordTag->addChild($key, $record[$key]);
	        }
	    }
	    $xml->asXML($filename);
	}
}