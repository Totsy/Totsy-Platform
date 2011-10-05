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
use lithium\analysis\Logger;
use Mongo;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;
use MongoCursor;
use lithium\data\Model;
use lithium\util\String;
use SimpleXMLElement;

ini_set('display_errors', 1);
/**
 * Simple export script for financial data needed by CFO.
 *
 * The data being used here is being changed into a more columnized fashion so it
 * can be uploaded into another database.
 */
class FinancialExport extends Base  {

	/**
	 * The environment to use when running the command. db='development' is the default.
	 * Set to 'production' if running live when using a cronjob.
	 */
	public $env = 'development';

	public $verbose = 'true';
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
    protected $orders = null;
    protected $orderSummaryFile = "";
	protected $orderDetailFile = "";
	protected $creditDetailFile = "";
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
		$this->log("Starting Financial Export");
		Order::collection()->ensureIndex(array('date_created' => -1));
		Promocode::collection()->ensureIndex(array('code' => -1));
		OrderShipped::collection()->ensureIndex(array('OrderNum' => 1));
		Credit::collection()->ensureIndex(array('user_id' => 1, 'customer_id' => 1));
		/**
		 * Going for all the orders that were created after Nov 1, 2010. This may need to be dynamically
		 * setup for future queries via cron.
		 */
		 if ($this->historical == 'true') {
		    $yesterday_max = mktime(23,59,59,date('m'),date('d') - 1,date('Y'));
            $orderConditions = array(
                'date_created' => array(
                    '$gte' => new MongoDate(strtotime('Nov 1, 2010')),
                  //  '$lte' => new MongoDate(strtotime('Nov 30, 2010'))
                    '$lte' => new MongoDate($yesterday_max)
                ));
            /**
             * Setup filenames for the order summary and epxort functionality.
            */
		    $this->orderSummaryFile = $this->tmp . 'OrdSummary.xml';
		    $this->orderDetailFile = $this->tmp . 'OrdDetail.xml';
		    $this->creditDetailFile = $this->tmp . 'CredDetail.xml';
		    $this->log("Retrieving Historical Data");
        } else {
            $yesterday_min = mktime(0,0,0,date('m'),date('d') - 1,date('Y'));
            $yesterday_max = mktime(23,59,59,date('m'),date('d') - 1,date('Y'));
            $orderConditions = array(
                'date_created' => array(
                    '$gte' => new MongoDate($yesterday_min),
                    '$lte' => new MongoDate($yesterday_max)
                ));
            /**
             * Setup filenames for the order summary and epxort functionality.
             */
            $this->time = date('m-d-Y');
            $this->orderSummaryFile = $this->tmp . 'OrdSummary_' . $this->time . '.xml';
            $this->orderDetailFile = $this->tmp . 'OrdDetail_' . $this->time . '.xml';
            $this->creditDetailFile = $this->tmp . 'CredDetail_' . $this->time . '.xml';
            $this->log("Retrieving Daily Data");
        }

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

		$this->orders = Order::collection()->find($orderConditions, $fields);
		$this->_orderSummaryReport();
		$this->_orderDetailReport();
		$this->_orderCreditReport();
	}

	public function _orderSummaryReport(){
	   $ordersShipped = OrderShipped::collection();
	   $orderSummary = array();
	    while ($this->orders->hasNext()){
	           $order = $this->orders->getNext();
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
                $order['net_shipping_amt'] = (float) $order['subTotal'] + (float) $order['handling'];
                if (array_key_exists('overSizeHandling', $order)) {
                    $order['net_shipping_amt'] += (float) $order['overSizeHandling'];
                }
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
                $address2 = (array_key_exists('address_2',$order['shipping']))?$order['shipping']['address_2'] :'';
                $order['address'] = $order['shipping']['address'] . ' ' . $address2 .
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
            //	$orderSummary[] = $order;
                $this->log("Adding $order[order_id] to order summary");
                $this->createXMLDoc('order_summary', $order, $this->orderSummaryFile);
              //  var_dump($this->orders->dead());
	    }
	    $this->orders->rewind();
	}

	public function _orderDetailReport(){
	    $orderDetails = array();
	    while ($this->orders->hasNext()) {
            $order = $this->orders->getNext();
            $orderItems = $order['items'];
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
                if (array_key_exists('sku_details', $itemRecord)) {
                    $item['sku'] = $itemRecord['sku_details'][$item['size']];
                } else {
                     $item['sku'] = "none";
                }
                $item['description'] = preg_replace('/"/',"'", $item['description']);
                if (array_key_exists('event_id' , $item)) {
                    $event = Event::collection()->findOne(array('_id' => new MongoId($item['event_id'])));
                    $start_date = (is_object($event['start_date'])) ? date('m/d/Y', $event['start_date']->sec) : $event['start_date'];
                    $end_date = (is_object($event['end_date'])) ? date('m/d/Y', $event['end_date']->sec) : $event['end_date']['sec'];
                } else {
                    $event = Event::collection()->findOne(array('_id' => new MongoId($itemRecord['event'][0])));
                    if ($event) {
                        $item['event_id'] = (string) $event['_id'];
                        $start_date = (is_object($event['start_date'])) ? date('m/d/Y', $event['start_date']->sec) : $event['start_date'];
                        $end_date = (is_object($event['end_date'])) ? date('m/d/Y', $event['end_date']->sec) : $event['end_date']['sec'];
                    } else {
                        $item['event_id'] = "none";
                        $start_date = "none";
                        $end_date = "none";
                        $event["name"] = "none";
                    }
                }
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

                //$orderDetails[] = $item;
                $this->log("Adding order details to $order[order_id]");
                $this->createXMLDoc('order_detail', $item, $this->orderDetailFile);
                // @todo don't need this any more - order details - unless csv is requested again
                //fputcsv($fpDetail, $item,',',chr(34));
            }
		}
	}

	public function _orderCreditReport(){
	    $creditDetails = array();
	    $creditCollection = Credit::collection()->find();
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

              //  $creditDetails[] = $userCredit;
                $this->log("Adding credit details for $order[user_id]");
                $this->createXMLDoc('credit_detail', $userCredit, $this->creditDetailFile);
                //@todo don't this part any more - credit details
                /*
                if (!empty($userCredit)){
                    fputcsv($cpDetail, $userCredit,',',chr(34));
                }
                */
        }
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
	public function createXMLDoc($type, $record, $filename) {
	    if (file_exists($filename) && (filesize($filename))) {
            $xml = simplexml_load_file($filename);
        } else {
            $xml = new SimpleXMLElement("<$type></$type>");
        }
	   // foreach($records as $record) {
	        $recordTag = $xml->addChild('record');
	        foreach($record as $key => $value) {
	            //SimpleXMLElement doesn't like ampersand for some reason so I am replacing it with 'and'
	           $record[$key] = preg_replace('/&/','and',$record[$key]);
	            $recordTag->addChild($key, $record[$key]);
	        }
	  //  }
	    $xml->asXML($filename);
	    clearstatcache();
	}
}