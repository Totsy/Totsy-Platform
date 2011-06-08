<?php

namespace admin\extensions\command;

use admin\models\User;
use admin\models\Order;
use admin\models\Event;
use admin\models\Item;
use admin\models\Credit;
use admin\models\ProcessedOrder;
use lithium\core\Environment;
use Mongo;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;
use MongoCursor;
use lithium\data\Model;
use lithium\util\String;

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
	public $tmp = '/resources/totsy/';

	/**
	 * The summary header to be used in the summary CSV export file.
	 */
	protected $summaryHeader = array(
		'_id',
		'credit_used',
		'handling',
		'order_id',
		'overSizeHandling',
		'promo_discount',
		'promo_code',
		'subTotal',
		'tax',
		'total',
		'user_id',
		'city',
		'state',
		'zip',
		'order_date',
		'authKey',
		'payment_type',
		'payment_date',
		'ship_date'
	);

	/**
	 * The detailed header to be used in the detailed CSV file.
	 */
	protected $detailHeader = array(
		'_id',
		'category',
		'sub_category',
		'color',
		'description',
		'item_id',
		'quantity',
		'sale_retail',
		'sale_wholesale',
		'size',
		'event_id',
		'vendor',
		'event_start_date',
		'event_end_date',
		'order_id_short',
		'order_id_fk'
	);
	protected $creditHeader = array(
	    'customer_id',
	    'credit_type',
	    'description',
	    'amount',
	    'issued_date'
	);

	protected $creditUnsetKey = array(
		'date_created',
		'type'
	);
	/**
	 * Some standard order data fields to be unset.
	 */
	protected $orderUnsetKey = array(
		'billing',
		'date_created',
		'items',
		'card_type'
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
		MongoCursor::$timeout = -1;
		/**
		 * The query was timing out without an index running locally on a MBP. Although this won't be an
		 * issue running on production just note that a new index will be created.
		 */
		Order::collection()->ensureIndex(array('date_created' => -1));
		ProcessedOrder::connection()->connection->{'orders.processed'}->ensureIndex(array('Customer PO #' => 1));
		/**
		 * Going for all the orders that were created after Nov 1, 2010. This may need to be dynamically
		 * setup for future queries via cron.
		 */
		$orderConditions = array(
		//	'date_created' => array('$gte' => new MongoDate(strtotime('June 1, 2011')), '$lte' => new MongoDate(strtotime('June 7, 2011'))),
			'payment_date' => array('$exists' => true),
			'payment_date' => array('$gte' => new MongoDate(strtotime('June 1, 2011')), '$lte' => new MongoDate(strtotime('June 7, 2011')))
		);
		$fields = array(
			'billing',
			'authKey',
			'payment_type',
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
			'tax',
			'payment_date'
		);
		$orders = Order::collection()->find($orderConditions, $fields)->sort(array('date_created' => -1));
		$processedOrders = ProcessedOrder::connection()->connection->{'orders.processed'};
		$orderSummary = $orderDetails = array();
		/**
		 * Setup filenames for the order summary and epxort functionality.
		 */
		$this->time = date('ymdHis');
		$orderSummaryFile = $this->tmp . 'OrdSummary'.$this->time . '.csv';
		$orderDetailFile = $this->tmp . 'OrdDetail'.$this->time . '.csv';
		$creditDetailFile = $this->tmp . 'CredDetail' . $this->time . '.csv';
		$fpSummary = fopen($orderSummaryFile, 'w');
		$fpDetail = fopen($orderDetailFile, 'w');
		$cpDetail = fopen($creditDetailFile, 'w');
		/**
		 * Setup the file headers
		 */
		fputcsv($fpSummary, $this->summaryHeader);
		fputcsv($fpDetail, $this->detailHeader);
		fputcsv($cpDetail, $this->creditHeader);
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

			$order['city'] = $order['billing']['city'];
			$order['state'] = $order['billing']['state'];
			$order['zip'] = $order['billing']['zip'];
			$order['order_date'] = date('m/d/Y', $order['date_created']->sec);
			if (!empty($order['payment_date'])) {
				$order['payment_date'] = date('m/d/Y',$order['payment_date']->sec);
			} else {
				$order['payment_date'] = 0;
			}
			foreach ($order as $key => $value) {
				$checkList = array('credit_used', 'promo_code', 'promo_discount', 'overSizeHandling');
				foreach ($checkList as $value) {
					if (empty($order["$value"])) {
						$order["$value"] = 0;
					}
				}
				if (array_key_exists('credit_used', $order)){
				    $creditCollection = Credit::collection()->find(array('customer_id' => $order['user_id']));

				    foreach ($creditCollection as $credit) {
				        $credit['issue_date'] = date('m/d/Y', $credit['date_created']['sec']);
				        $credit['credit_type'] = $credit['type'];
				        $userCredit = $this->sortArrayByArray($credit, $this->creditHeader);
				        if (in_array('date_created', $this->creditUnsetKey)) {
                            unset($credit['date_created']);
                        }
                        if (in_array('type', $this->creditUnsetKey)) {
                            unset($credit['type']);
                        }
                        if (!empty($userCredit)){
                            var_dump($userCredit);
                            die();
				            fputcsv($cpDetail, $userCredit);
				        }
				    }
				}
				if (is_array($value) || in_array($key, $this->orderUnsetKey)) {
                    unset($order[$key]);
                }
			}
			$shipRecord = $processedOrders->findOne(array('Customer PO #' => $order['_id']));
			if ($shipRecord) {
				$order['ship_date'] = date('m/d/Y', $shipRecord['_id']->getTimestamp());
			} else {
				$order['ship_date'] = 0;
			}
			$order = $this->sortArrayByArray($order, $this->summaryHeader);
			fputcsv($fpSummary, $order);
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
				$event = Event::find('first', array('_id' => $item['event_id']));
				if (array_key_exists('vendor', $item)) {
				    $item['vendor'] = $item['vendor'];
				} else {
				    $item['vendor'] = $event['name'];
				}
				if (array_key_exists('sub_category', $item)) {
				    $item['sub_category'] = $item['sub_category'];
				} else {
				    $item['sub_category'] = "none";
				}
				$item['event_start_date'] = date('m/d/Y', $event['start_date']->sec);
				$item['event_end_date'] = date('m/d/Y', $event['end_date']->sec);
				$item['order_id_fk'] = $order['_id'];
				$item['order_id_short'] = $order['order_id'];
				$item['sale_wholesale'] = $itemRecord['sale_whol'];
				$item = $this->sortArrayByArray($item, $this->detailHeader);
				fputcsv($fpDetail, $item);
			}
		}
		fclose($fpSummary);
		fclose($fpDetail);
	}

	/**
	 * Sort an array by a hearder
	 */
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
}