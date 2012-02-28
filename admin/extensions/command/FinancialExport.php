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
	*
	*
	**/
	protected $processed_dir = '/resources/totsy/finance/processed/';

    /**
	* This is the directory where the files will be sent to on the remote server
	*
	**/
	protected $directory = 'TotsyData';
    /**
    * Starting date to retrieve historical records. Default 11/01/2010
    * @var string
    **/
    public $startdate = "";
    /**
    * Ending Date to retrieve historical records. Default 'yesterday'
    * @var string
    **/
	public $enddate = "";
	/**
    * Date to retrieve a daily file
    * @var string
    **/
	public $dailydate = "";
	/**
	 * Will generate xml of orders going back to Nov 1 - one day before the present
	 * defaults to `false`
	 *
	 * @var string
	 */
	public $historical = 'false';
	/**
	 * Select what to process.  Possible choices:
	 * - 'summary' : processes order summary
	 * - 'detail' : processes order details
	 * - 'credit' : process credit details
	 * - 'update' : process order summary & detail updates
	 * - 'all' : processess all the above (default)
	 * - 'export' : export the files in the finance folder to the accounting folder
	 *
	 * @var string
	 */
	public $process = "all";
	/**
	 * The minimum date for the query
	 */
	protected $yesterday_min = "";
	/**
	 * The maximum date for the query
	 */
	protected $yesterday_max = "";

	/**
	 * The summary header to be used in the summary CSV export file.
	 */
	protected $summaryHeader = array(
		'_id',
		'billing_name',
		'billing_address',
		'billing_address2',
		'billing_city',
		'billing_state',
		'billing_zip',
		'shipping_name',
		'shipping_address',
		'shipping_address2',
		'shipping_city',
		'shipping_state',
		'shipping_zip',
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
		'order_date',
		'authKey',
		'auth_confirmation',
		'auth_error',
		'payment_type',
		'payment_date',
		'estimated_ship_date',
		'actual_ship_date',
		'ship_records',
		'cancel',
		'gross_revenue',
		'net_revenue',
		'merchantReferenceCode',
		'process_by',
		'auth_records'
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
		"order_id_fk",
		"event_url",
		"sku",
		"cancel"
	);
	protected $creditHeader = array(
	    '_id',
	    'customer_id',
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
		'ship_date',
		'modifications',
		'avatax',
		'savings',
		'auth',
		'void_records',
		'capture_records'
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
		'line_number',
		'discount_exempt'
	);
    protected $orders = null;
    protected $orderSummaryFile = "";
	protected $orderDetailFile = "";
	protected $creditDetailFile = "";
	protected $xml = null;
	protected $test = "false";
	/**
	* Order fields to return in a query
	**/
	protected $fields = array(
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
			'shipping',
			"cancel",
			'auth',
			'auth_records',
			'void_records',
			'capture_records'
		);
	/**
	 * Find all the orders that haven't been shipped which have stock status.
	 */
	public function run() {
		Environment::set($this->env);
		MongoCursor::$timeout = -1;
		$this->tmp = LITHIUM_APP_PATH . $this->tmp;
		/**
		 * The query was timing out without an index running locally on a MBP. Although this won't be an
		 * issue running on production just note that a new index will be created.
		 */
		$this->log("Starting Financial Export");
		Order::collection()->ensureIndex(array('date_created' => 1));
		Promocode::collection()->ensureIndex(array('code' => -1));
		OrderShipped::collection()->ensureIndex(array('OrderNum' => 1));
		Credit::collection()->ensureIndex(array('user_id' => 1, 'customer_id' => 1));
		/**
		 * Going for all the orders that were created after Nov 1, 2010. This may need to be dynamically
		 * setup for future queries via cron.
		 */
		$this->prepareData();
		switch(strtolower(trim($this->process))) {
            case "summary":
                $this->_orderSummaryReport();
                 break;
            case "detail":
		        $this->_orderDetailReport();
                 break;
            case "credit":
		        $this->_orderCreditReport();
                 break;
            case "update":
                $this->_updateOrderSummaryReport();
                $this->_updateOrderDetailFile();
                 break;
            case "all" :
                $this->_orderSummaryReport();
                $this->_orderDetailReport();
                $this->_orderCreditReport();
                $this->_updateOrderSummaryReport();
                $this->_updateOrderDetailFile();
                $this->exportFiles();
                 break;
            case "export":
                $this->exportFiles();
                break;
            default:
                echo "Invalid input. For help type : li3 help finanical-export\n";
                break;
		}
	}
	protected function parseDate($date,$timeOfDay = 'end') {
	    $query_date = date_parse($date);
	    if ($query_date['error_count'] > 0) {
            foreach($query_date['errors'] as $error) {
                echo $error . "\r\n";
            }
            exit(1);
        }
	    switch ($timeOfDay) {
	        case 'end' :
	            return mktime(23, 59, 59, $query_date['month'], $query_date['day'],
	             $query_date['year']);
	        case 'start' :
	            return mktime(0, 0, 0, $query_date['month'], $query_date['day'],
	             $query_date['year']);
	    }
	}
	protected function prepareData() {
	    /**
	    * prepare dates for query
	    **/

	     $this->yesterday_min = mktime(0,0,0,date('m'),date('d') - 1,date('Y'));
		 $this->yesterday_max = mktime(23,59,59,date('m'),date('d') - 1,date('Y'));
		 if ($this->historical == "true") {
           $this->yesterday_min = mktime(0,0,0,1,1,2009);
		    if (!empty($this->startdate)) {
		        $this->yesterday_min = $this->parseDate($this->startdate,'start');
		    }
		    if (!empty($this->enddate)) {
                $this->yesterday_max = $this->parseDate($this->enddate,'end');
		    }
		    echo "Generating history files for " . date('m/d/Y H:i:s', $this->yesterday_min) . " to " .
            date('m/d/Y H:i:s', $this->yesterday_max) . "\r\n";
		 } else if (!empty($this->dailydate)) {
            $this->yesterday_min = $this->parseDate($this->dailydate,'start');
            $this->yesterday_max = $this->parseDate($this->dailydate,'end');
            echo "Generating daily files for " . date('m/d/Y H:i:s', $this->yesterday_min) . " to " .
            date('m/d/Y H:i:s', $this->yesterday_max) . "\r\n";
		}
         /**
         * Setup filenames for the order summary and epxort functionality.
        */
	     if ($this->historical == 'true') {
            $this->time = date('M_d',$this->yesterday_min) . "_" . date('M_d_Y', $this->yesterday_max);
		    $this->orderSummaryFile = $this->tmp . 'OrdSummary_' . $this->time. '.xml';
		    $this->orderDetailFile = $this->tmp . 'OrdDetail_' . $this->time. '.xml';
		    $this->creditDetailFile = $this->tmp . 'CredDetail_' . $this->time. '.xml';
		    $this->orderUpdateFile = $this->tmp . 'OrdUpdate_' . $this->time. '.xml';
		    $this->orderUpdateDetailFile = $this->tmp . 'UpdateDetail_' . $this->time. '.xml';
		    $this->directory = 'TotsyHistory';
		    $this->log("Retrieving Historical Data");
        } else {
            $this->time = date('m-d-Y', $this->yesterday_min);
            $this->orderSummaryFile = $this->tmp . 'OrdSummary_' . $this->time . '.xml';
            $this->orderDetailFile = $this->tmp . 'OrdDetail_' . $this->time . '.xml';
            $this->creditDetailFile = $this->tmp . 'CredDetail_' . $this->time . '.xml';
            $this->orderUpdateFile = $this->tmp . 'OrdUpdate_' . $this->time . '.xml';
            $this->orderUpdateDetailFile = $this->tmp . 'UpdateDetail_' . $this->time . '.xml';
            $this->log("Retrieving Daily Data");
        }
        $orderConditions = array(
            'date_created' => array(
                '$gte' => new MongoDate($this->yesterday_min),
                '$lte' => new MongoDate($this->yesterday_max)
            ),
            'items' => array('$exists' => true)
            );
		$this->orders = Order::collection()->find($orderConditions, $this->fields);
	}

	public function _orderSummaryReport(){
	   $this->summaryInfo();
	   $this->saveXmlFile($this->orderSummaryFile);
	    $this->xml = null;
	    $this->orders->rewind();
	}

	public function _orderDetailReport(){
	    $this->detailInfo();
	    $this->saveXmlFile($this->orderDetailFile);
	    $this->xml = null;
	}

	public function _orderCreditReport(){
         $conditions = array( '$or' => array(
           array( 'date_created' => array(
                '$gte' => new MongoDate($this->yesterday_min),
                '$lte' => new MongoDate($this->yesterday_max))
                ),
            array(
            'created' => array(
                '$gte' => new MongoDate($this->yesterday_min),
                '$lte' => new MongoDate($this->yesterday_max))
            )
        ));
	    $creditCollection = Credit::collection()->find($conditions);
            foreach ($creditCollection as $credit) {
                if (array_key_exists('date_created', $credit)) {
                    $credit['issued_date'] = date('m/d/Y', $credit['date_created']->sec);
                } else {
                    $credit['issued_date'] = date('m/d/Y', $credit['created']->sec);
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
                } else {
                     $credit['description'] = trim($credit['description']);
                }
                if (!array_key_exists('reason', $credit))  {
                    $credit['description'] = "none";
                } else {
                    $stringtoArray =  str_word_count($credit['description'],2, "0123456789()!.?@+");
                    $description = implode(' ', $stringtoArray);
                     $credit['description'] = utf8_encode($description);
                }
                $userCredit = $this->sortArrayByArray($credit, $this->creditHeader);
                $userCredit = $this->removeStrayKeys($userCredit, $this->creditHeader);
                foreach($this->creditUnsetKey as $key) {
                    unset($userCredit[$key]);
                }

                $this->log("Adding credit details for");
                $this->createXMLDoc('credit_detail', $userCredit, $this->creditDetailFile);
        }
        $this->saveXmlFile($this->creditDetailFile);
	    $this->xml = null;

    }

    public function _updateOrderSummaryReport(){
        #this section handles any new shipped files
        $conditions = array('created_date' => array(
                '$gte' => new MongoDate($this->yesterday_min),
                '$lte' => new MongoDate($this->yesterday_max)
                ));
	   $ordersShipped = OrderShipped::collection();
	   $updates = $ordersShipped->find($conditions,array('OrderNum' => true, '_id' => false));
	   $orderids = array();
	   foreach($updates as $id) {
	        $id = $id['OrderNum'];
	        $index = strpos($id, '-');
            if ($index) {
                $orderids[] = substr($id, 0 , $index);
            } else {
                $orderids[] = $id;
            }

	   }
	    $this->orders = Order::collection()->find(array(
	        'order_id' => array('$exists' => true),
	        'order_id' => array('$in' => array_unique($orderids))
	        ),$this->fields
	    );
	    $this->summaryInfo();

	    #this section handles any modified or canceled orders

	    if ($this->historical == 'true') {
	        $this->yesterday_max = mktime(23,59,59,date('m') - 1,24,date('Y'));
	         $conditions = array('$or' => array(
	         array('modifications' => array('$elemMatch' => array(
                'date' => array(
                    '$gte' => new MongoDate(strtotime('Oct 1, 2011')),
                    '$lte' => new MongoDate($this->yesterday_max)
                )))),
              array('payment_date' => array(
                    '$gte' => new MongoDate(strtotime('Oct 1, 2011')),
                    '$lte' => new MongoDate($this->yesterday_max)
                )),
              array('auth_records' => array('$elemMatch' => array(
                'date_saved' => array(
                    '$gte' => new MongoDate(strtotime('Oct 1, 2011')),
                    '$lte' => new MongoDate($this->yesterday_max)
                )))),
              array('void_records' => array('$elemMatch' => array(
                'date_saved' => array(
                    '$gte' => new MongoDate(strtotime('Oct 1, 2011')),
                    '$lte' => new MongoDate($this->yesterday_max)
                )))),
              array('capture_records' => array('$elemMatch' => array(
                'date_captured' => array(
                    '$gte' => new MongoDate(strtotime('Oct 1, 2011')),
                    '$lte' => new MongoDate($this->yesterday_max)
                ))))
            ));
	    } else {
	        $this->yesterday_min = mktime(0,0,0,date('m'),date('d') - 1,date('Y'));
            $this->yesterday_max = mktime(23,59,59,date('m'),date('d') - 1,date('Y'));
            $conditions = array('$or' =>array(
            	array('modifications' => array('$elemMatch' => array(
	                'date' => array(
	                    '$gte' => new MongoDate($this->yesterday_min),
	                    '$lte' => new MongoDate($this->yesterday_max)
	                )))),
              array('payment_date' => array(
                    '$gte' => new MongoDate($this->yesterday_min),
	                '$lte' => new MongoDate($this->yesterday_max)
                )),
              array('auth_records' => array('$elemMatch' => array(
                'date_saved' => array(
                    '$gte' => new MongoDate($this->yesterday_min),
	                '$lte' => new MongoDate($this->yesterday_max)
                )))),
              array('void_records' => array('$elemMatch' => array(
                'date_saved' => array(
                    '$gte' => new MongoDate($this->yesterday_min),
	                '$lte' => new MongoDate($this->yesterday_max)
                )))),
              array('capture_records' => array('$elemMatch' => array(
                'date_captured' => array(
                    '$gte' => new MongoDate($this->yesterday_min),
	                '$lte' => new MongoDate($this->yesterday_max)
                ))))
            ),
             'order_id' => array('$nin' => array_unique($orderids))
            );
	    }
	    $this->orders = Order::collection()->find($conditions, $this->fields);
	    $this->summaryInfo();
	    $this->saveXmlFile($this->orderUpdateFile);
	    $this->xml = null;
	    $this->orders->rewind();
	}

	public function _updateOrderDetailFile() {
	    $this->detailInfo();
	    $this->saveXmlFile($this->orderUpdateDetailFile);
	    $this->xml = null;
	}

	/**
	* The function prepares the order detail file data
	**/
	private function detailInfo(){
	        //Holds the which line items are already in the file
	    $alreadyIn = array();
	    while ($this->orders->hasNext()) {
            $order = $this->orders->getNext();
            $orderItems = $order['items'];
            foreach ($orderItems as $item) {
                $itemRecord = Item::collection()->findOne(array('_id' => new MongoId($item['item_id'])));
                foreach ($this->itemUnsetKey as $key) {
                    unset($item[$key]);
                }
                if (!in_array((string)$item['_id'], $alreadyIn)) {
                    $alreadyIn[] = (string)$item['_id'];
                } else {
                    continue;
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
                if (!empty($itemRecord) && array_key_exists('sku_details', $itemRecord)) {
                    if (strpos($item['size'], "\n")) {
                        $item['sku'] = $itemRecord['sku_details'][$item['size']];
                    } else {
                        $size = preg_replace("/\s(or)\s/"," or\n", $item['size']);
                        if(!array_key_exists($size,$itemRecord['sku_details'])) {
                           $size = preg_replace("/\s(or)\s/"," or\r\n", $item['size']);
                        }
                        $item['sku'] = $itemRecord['sku_details'][$size];
                    }
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
                if (!array_key_exists("cancel", $item)) {
                    $item["cancel"] = "false";
                } else {
                    if ($item["cancel"]) {
                        $item["cancel"] = "true";
                    } else {
                        $item["cancel"] = "false";
                    }
                }
                $item = $this->sortArrayByArray($item, $this->detailHeader);
                $item = $this->removeStraykeys($item, $this->detailHeader);
                $this->log("Adding order details to $order[order_id]");
                $this->createXMLDoc('order_detail', $item);
            }
		}
	}

	/**
	* The function prepares the order summary file data
	**/
	private function summaryInfo() {
	    $ordersShipped = OrderShipped::collection();
	    while ($this->orders->hasNext()){
	           $order = $this->orders->getNext();
                $orderItems = $order['items'];

                if (array_key_exists('tax', $order) ){
					$order['tax'] = (float) $order['tax'];
                }

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
                if (array_key_exists('firstname', $order['billing'])) {
                    $order['billing_name'] = $order['billing']['firstname'] . " " . $order['billing']['lastname'];
                } else {
                    $order['billing_name'] = '';
                }
                if (array_key_exists('address', $order['billing'])) {
                    $order['billing_address'] = $order['billing']['address'];
                } else {
                    $order['billing_address'] = '';
                }
                if (array_key_exists('address_2', $order['billing'])) {
                    $order['billing_address2'] = $order['billing']['address_2'];
                } else {
                    $order['billing_address2'] = '';
                }
                if (array_key_exists('city', $order['billing'])) {
                    $order['billing_city'] = $order['billing']['city'];
                } else {
                    $order['billing_city'] = '';
                }
                if (array_key_exists('state', $order['billing'])) {
                    $order['billing_state'] = $order['billing']['state'];
                } else {
                    $order['billing_state'] = '';
                }
                if (array_key_exists('zip', $order['billing'])) {
                    $order['billing_zip'] = $order['billing']['zip'];
                } else {
                    $order['billing_zip'] = '';
                }
                if (!array_key_exists('overSizeHandling', $order)) {
                    $order['overSizeHandling'] = 0;
                }
                $order['order_date'] = date('m/d/Y', $order['date_created']->sec);
                
                if (array_key_exists('ship_date', $order)) {
                    $order['estimated_ship_date'] =
                        (is_int($order['ship_date'])) ? date('m/d/Y', $order['ship_date']) : date('m/d/Y', $order['ship_date']->sec);
                } else {
                    $order['estimated_ship_date'] = 0;
                }

                $order['gross_shipping_amt'] = (float) $order['subTotal'] + (float) $order['handling'];
                if (array_key_exists('overSizeHandling', $order)) {
                    $order['gross_shipping_amt'] += (float) $order['overSizeHandling'];
                }
                $order['gross_shipping_amt'] = number_format($order['gross_shipping_amt'], 2);
                $order['net_shipping_amt'] = $order['gross_shipping_amt'];
                if (array_key_exists('service', $order)) {
                    if (in_array('freeshipping', $order['service'])){
                        $order['service'] = 'freeshipping';
                        $order['net_shipping_amt'] -= 7.95;
                    } else if(in_array('10off50', $order)) {
                        $order['service'] = '10off50';
                        $order['net_shipping_amt'] -= 10;
                    }else {
                        $order['service'] = "none";
                    }
                } else {
                    $order['service'] = "none";
                }
                $order['subTotal'] = number_format($order['subTotal'], 2);
                $order['total'] = number_format($order['total'], 2);
                if (array_key_exists('promo_code', $order)) {
                    $promocode = Promocode::find('first', array('conditions' => array('code' => new MongoRegex("/" . $order['promo_code'] . "/i"))));
                    $order['promo_type'] = $promocode['type'];
                    $order['promo-code_amt'] = $promocode['discount_amount'];
                }else {
                    $order['promo_type'] = "";
                    $order['promo-code_amt'] = "";
                }
                /*
                * Grab credit information
                */
                if (array_key_exists('credit_used', $order)){
                    $order['net_shipping_amt'] += (array_key_exists('credit_used', $order)) ? $order['credit_used']:0;
                    $order['net_shipping_amt'] += (array_key_exists('promo_discount', $order)) ? $order['promo_discount']:0;
                }
                $order['net_shipping_amt'] = number_format((float)$order['net_shipping_amt'], 2);
                $order['gross_revenue'] = number_format((float)Item::calculateProductGross($order['items']), 2);
                $order['net_revenue'] = $order['gross_revenue'] + $order['handling'] + $order['overSizeHandling'];
                $order['net_revenue'] = number_format($order['net_revenue'] , 2);
                $order['merchantReferenceCode'] = "";
                $order['process_by'] = "AuthorizeNet";
                if (array_key_exists('auth', $order)) {
                	$order['merchantReferenceCode'] = $order['auth']['response']['merchantReferenceCode'];
                	$order['process_by'] = $order['auth']['adapter'];
                }

                if (array_key_exists('auth_records', $order) && array_key_exists('auth', $order)) {
                    $tmp = array();
					foreach($order['auth_records'] as $auth_record) {
					    $auth_record['date_saved'] = date("m/d/Y h:i:s A", $auth_record['date_saved']->sec );
					    $tmp[] = $auth_record;
					}
					$tmp[] = array(
							'authKey' => $order['authKey'],
							'date_saved' => date("m/d/Y h:i:s A", strtotime($order['auth']['response']['ccAuthReply']['authorizedDateTime']))
						);
					$order['auth_records'] = $tmp;
				} else {
					$order['auth_records'] = array(
						array(
							'authKey' => $order['authKey'],
							'date_saved' => date("m/d/Y h:i:s A", $order['date_created']->sec)
						)); 
				}
				if (array_key_exists('void_records', $order) && array_key_exists('auth', $order)) {
                    $tmp = array();
					foreach($order['auth_records'] as $auth_record) {
					    $auth_record['date_saved'] = date("m/d/Y h:i:s A", $auth_record['date_saved']->sec );
					    $tmp[] = $auth_record;
					}

					$order['auth_records'] = $tmp;
				}
				if (array_key_exists('capture_records', $order) && array_key_exists('auth', $order)) {
                    $tmp = array();
					foreach($order['auth_records'] as $auth_record) {
					    $auth_record['date_saved'] = date("m/d/Y h:i:s A", $auth_record['date_captured']->sec );
					    $tmp[] = $auth_record;
					}

					$order['auth_records'] = $tmp;
				}

				if(!empty($order['payment_date']) && ($order['payment_date'] !== 'none')){
					$order['auth_records'][] = array(
							'authKey' => $order['auth_confirmation'],
							'date_saved' =>  date('m/d/Y h:i:s A',$order['payment_date']->sec)
						);
				}
				if (!empty($order['payment_date'])) {
                    $order['payment_date'] = date('m/d/Y',$order['payment_date']->sec);
                } else {
                    $order['payment_date'] = 0;
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
                if ($order && array_key_exists('ship_records', $order)) {
                    $order['ship_records'] = "Yes";
                } else {
                    $order['ship_records'] = "No";
                }

                if (array_key_exists('shipping', $order)) {
                    $order['shipping_name'] = $order['shipping']['firstname'] . ' ' . $order['shipping']['lastname'];
                    $address2 = (array_key_exists('address_2',$order['shipping']))?$order['shipping']['address_2'] :'';
                    $order['shipping_address'] = $order['shipping']['address'];
                    if (!empty($address2)) {
                        $order['shipping_address2'] = $address2;
                    } else {
                         $order['shipping_address2'] = "";
                    }
                    $order['shipping_city'] = $order['shipping']['city'];
                    $order['shipping_state'] = $order['shipping']['state'];
                    $order['shipping_zip'] = $order['shipping']['zip'];
                    unset($order['shipping']);
                } else {
                    $order['shipping_name'] = "";
                    $order['shipping_address2'] = "";
                    $order['shipping_city'] = "";
                    $order['shipping_state'] = "";
                    $order['shipping_zip'] = "";
                }

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
                if (!array_key_exists("cancel", $order)) {
                    $order["cancel"] = "false";
                } else {
                    $order["cancel"] = ($order["cancel"])? "true":"false";
                }
                $order = $this->sortArrayByArray($order, $this->summaryHeader);
                $order = $this->removeStraykeys($order, $this->summaryHeader);
                $this->log("Adding $order[order_id] to order update summary");
                $this->createXMLDoc('order_summary', $order);
                $this->log("Finish adding $order[order_id] to order update summary");
	    }
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

    /**
	 * removes any keys not in the header
	 * @param array $array - the array of values in question
	 * @param array $orderArray - the header to compare $array by
	 */
	private function removeStrayKeys($array, $orderArray){
	    $keys = array_keys($array);
	    $unwantedKeys = array_diff($keys, $orderArray);
	    foreach($unwantedKeys as $key) {
	        unset($array[$key]);
	    }
	    return $array;
	}

    /**
	 * Exports the processed files to the accounting server
	 * @param
	 */
	private function exportFiles() {
            $processed = LITHIUM_APP_PATH . $this->processed_dir;
            $source = $this->tmp;
            $finished = false;
            $reporting = array(
                'success' => true,
                'error' => array(),
                'files_sent' => array(),
                'files_failed' => array()
            );
            $obj = $this;

            $To = "lhanson@totsy.com,scott.fisher@yourtechso.com,sadler@totsy.com,rminns@totsy.com";
            //$To = "lhanson@totsy.com";
            $headers = "From: reports@totsy.com";
            echo "Exporting Files \n\r";
            $this->log("Exporting to Accounting Server...");
            $directory = $this->directory;
            $output = "";

            if (is_dir($source)) {
                $cmd = "scp -F ~/.ssh/config {$source}*.xml accounting.totsy.com:/C/$directory";
                #This grabs the output when the command has run
                $proc = proc_open($cmd, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
                fwrite($pipes[0], $input); fclose($pipes[0]);
                $stdout = stream_get_contents($pipes[1]);fclose($pipes[1]);
                $stderr = stream_get_contents($pipes[2]);fclose($pipes[2]);
                $rtn = proc_close($proc);

                if ($rtn == 0) {
                        $reporting['success'] = true;
                        foreach(glob("{$source}*.xml") as $file) {
                            $filename = preg_split("#($source)#", $file);
                            $this->log("Moving " . $filename[1] . " to processed folder.");
                            $reporting['files_sent'][] = $filename[1] . " " . filesize($file) . " Bytes" ;
                            if (!is_dir($processed)) {
                                mkdir($processed, 0777, true);
                            }
                            rename($file,$processed.$filename[1]);
                        }
                } else {
                        $this->log("Fail: " .  print_r($stderr, true));
                        $reporting['success'] = false;
                        $reporting['error'][] = "Fail: " .  print_r($stderr, true);
                        $this->log("Fail: File upload error.");
                }
            }

            $subject = "Accounting Auto Reporting Job - Report";

            if (!$reporting['success']) {
                $message = "Automating reporting results - FAILED: \r\n";
                $message .= implode("\r\n", $reporting['error']);
            } else {
                 $message = "Automating reporting results - SUCCESS: \r\n";
            }
            if (!empty($reporting['files_failed'])) {
                $message .= "The following files failed to transfer: \r\n";
                $message .= implode("\r\n", $reporting['files_failed']);
            }
            if (!empty($reporting['files_sent'])) {
                $message .= "The following files were transferred to $directory: \r\n";
                $message .= implode("\r\n", $reporting['files_sent']);
            }
            $this->log("Sending out email");
            mail($To , $subject , $message , $headers);
	}

	/**
	* Creates XML from data passed in
	* @param string $type type of information: used for the root tags of the xml file
	* @param array $records multidimensional array holding the records to converted to xml
	**/
	private function createXMLDoc($type, $record) {
	    if ($this->xml == null) {
	       $this->xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><$type></$type>");
	    }

        $recordTag = $this->xml->addChild('record');
        foreach($record as $key => $value) {
            //SimpleXMLElement doesn't like ampersand for some reason so I am replacing it with 'and'
            if (is_array($value) && ($key == "auth_records")) {
				$auth_records = $recordTag->addChild($key);
				foreach($value as $sub_key => $sub_value) {
					$auth = $auth_records->addChild('auth');
                    if (is_array($sub_value)) {
                        foreach($sub_value as $k => $v) {
                            $auth->addChild($k, $v);
                        }
                    } else {
                        $auth->addChild($sub_key, $sub_value);
                    }
				}
		   } else {
				$record[$key] = preg_replace('/&/','and',$record[$key]);
				$recordTag->addChild($key, (string)$record[$key]);
			}
        }
	}

	private function saveXmlFile($file) {
	    if (!is_null($this->xml) && $this->xml->count() > 0 ) {
	        $this->xml->asXml($file);
	    }
	}
}
?>
