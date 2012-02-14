<?php

namespace admin\extensions\command;
use lithium\analysis\Logger;
use lithium\core\Environment;
use admin\controllers\OrdersController;
use admin\models\Order;
use admin\models\User;
use admin\models\Event;
use admin\models\Item;
use admin\models\Credit;
use admin\models\OrderShipped;
use admin\models\Invitation;
use admin\extensions\Mailer;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;
use MongoCursor;


/**
 * Process payments from Authorize.net based on confirmed shipping log.
 */
class ProcessPayment extends \lithium\console\Command  {

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';
	/**
	 * To change into test mode set the test variable to 'true'. 'false' by default.
	 *
	 * @var string
	 */
	public $test = 'false';
	/**
	 * This variable is only available in test mode
	 * In test mode you need to pass in an order id, e.g. '4d2e93f00'
	 * li3 process-payment --test='true' --orderid='4d2e93f00'
	 * @var string
	 */
	public $orderid = null;

	protected $failedCaptures = array();

	/**
	 * Find all the orders that haven't been shipped which have stock status.
	 */
	public function run() {
		error_reporting(E_ERROR | E_PARSE);
		Logger::info('Starting Payment Processor');
		Environment::set($this->env);
		$this->capture();
		$this->reportFailedCaptures();
		Logger::info('Payment Processor Finished');
	}

	/**
	 * Find all orders that have a the field ship_records but no confirmation
	 * auth Key. These are the orders that have a corresponding ship record(s).
	 *
	 * @todo What happens when we apply credit to someone who didn't come in via
	 * an invitation request? Should an invite document be created?
	 */
	public function capture() {
	    /**
	        Continue to process no matter how long it takes
	    **/
	    MongoCursor::$timeout = 50000;
		$ordersCollection = Order::collection();
		if ($this->test != 'true') {
            $orders = $ordersCollection->find(array(
                'ship_records' => array('$exists' => true),
                'auth_confirmation' => array('$exists' => false)
            ));
		} else {
		    if (is_null($this->orderid)) {
		        $this->out('You need to provide an order id.  Please refer to "li3 help process-payment".');
		        exit(0);
		    }
		    $orders = $ordersCollection->find(array(
                'order_id' => $this->orderid
            ));
		}
		if ($orders) {
			foreach ($orders as $order) {
				Logger::info('Process Order : ' . $order['order_id']);
				$conditions = array('_id' => $order['user_id']);
				$user = User::find('first', compact('conditions'));
				$oc = new OrdersController;
				$order = $oc->cancelUnshippedItems($order);
				$processedOrder = Order::process($order);
				if (Order::failedCaptureCheck($order['order_id'])){
				    $this->failedCaptures[] = $order['order_id'];
				}
				if ($processedOrder && $user->purchase_count == 1) {
					if ($user->invited_by) {
						$inviter = User::find('first', array(
							'conditions' => array(
								'invitation_codes' => $user->invited_by
						)));
						if ($inviter) {
							$data = array(
								'user_id' => $inviter->_id,
								'description' => "Invite accepted from: $user->email"
							);
							$options = array('type' => 'Invite');
							if (Credit::add($data, $options) && User::applyCredit($data, $options)) {
								Logger::info("process-payment: Added Credit to UserId: $inviter->_id");
								$updateInvite = Invitation::find('first', array(
									'conditions' => array(
										'email' => $user->email,
										'user_id' => $inviter->_id
								)));
								if ($updateInvite) {
									$updateInvite->credited = true;
									$updateInvite->save();
								}
							}
						}
					}
				}
			}
		}
	}

	public function reportFailedCaptures() {

	    $failedOrders = Order::collection()->find(array('order_id' => array('$in' => $this->failedCaptures)), array('order_id' => 1, 'authKey' => 1, 'date_created' => 1, 'auth_error', 'total' => 1));
	    $tableInfo = array();
	    foreach($failedOrders as $order) {
	        $order['date_created'] = date('m/d/Y', $order['date_created']->sec);
	        $tableInfo[] = $order;
	    }
	    $content['tableInfo'] = $tableInfo;
	    if ($failedOrders) {
            if ($this->test != "true" && (Environment::is('production'))) {
                Mailer::send('Failed_Capture_Report',"searnest@totsy.com",$content);
                Mailer::send('Failed_Capture_Report',"gsuper@totsy.com",$content);
                Mailer::send('Failed_Capture_Report',"kogrady@totsy.com",$content);
                Mailer::send('Failed_Capture_Report',"mruiz@totsy.com",$content);
            } else {
                //Mailer::send('Failed_Capture_Report',"lhanson@totsy.com",$content);
                Mailer::send('Failed_Capture_Report',"troyer@totsy.com",$content);
            }
        }
	}

}