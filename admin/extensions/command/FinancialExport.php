<?php

namespace admin\extensions\command;

use admin\models\User;
use admin\models\Order;
use admin\models\Event;
use admin\models\Item;
use admin\models\ProcessedOrder;
use lithium\core\Environment;
use Mongo;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;
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
		'subTotal',
		'tax',
		'total',
		'user_id',
		'city',
		'state',
		'zip',
		'order_date',
		'payment_date',
		'ship_date'
	);

	/**
	 * The detailed header to be used in the detailed CSV file.
	 */
	protected $detailHeader = array(
		'_id',
		'category',
		'color',
		'description',
		'item_id',
		'quantity',
		'sale_retail',
		'sale_wholesale',
		'size',
		'event_id',
		'order_id_short',
		'order_id_fk'
	);

	/**
	 * Some standard order data fields to be unset.
	 */
	protected $orderUnsetKey = array(
		'billing',
		'date_created',
		'items',
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
		ProcessedOrder::connection()->connection->{'orders.processed'}->ensureIndex(array('Customer PO #' => 1));
		/**
		 * Going for all the orders that were created after Nov 1, 2010. This may need to be dynamically
		 * setup for future queries via cron.
		 */
		$orderConditions = array(
			'date_created' => array('$gt' => new MongoDate(strtotime('Nov 1, 2010')))
		);
		$fields = array(
			'billing',
			'handling',
			'items',
			'order_id',
			'overSizeHandling',
			'subTotal',
			'total',
			'date_created',
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
		$orderSummaryFile = $this->tmp.'OrdSummary'.$this->time.'.csv';
		$orderDetailFile = $this->tmp.'OrdDetail'.$this->time.'.csv';
		$fpSummary = fopen($orderSummaryFile, 'w');
		$fpDetail = fopen($orderDetailFile, 'w');
		/**
		 * Setup the file headers
		 */
		fputcsv($fpSummary, $this->summaryHeader);
		fputcsv($fpDetail, $this->detailHeader);
		/**
		 * Build the files
		 */
		foreach ($orders as $order) {
			$orderItems = $order['items'];
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
				$checkList = array('credit_used', 'promo_discount', 'overSizeHandling');
				foreach ($checkList as $value) {
					if (empty($order["$value"])) {
						$order["$value"] = 0;
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