<?php
namespace admin\extensions\command;

use lithium\analysis\Logger;
use lithium\core\Environment;
use admin\models\User;
use lithium\security\Auth;
use lithium\storage\Session;
use lithium\data\Connections;
use admin\models\Order;
use admin\models\Item;
use admin\models\OrderShipped;
use Mongo;
use MongoCode;
use MongoDate;
use MongoRegex;
use MongoId;
use li3_silverpop\extensions\Silverpop;
use admin\extensions\command\Pid;

/**
 * Process email notifications for orders shipped.
 */
class OrderShippedNotifications extends \lithium\console\Command  {
	
	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';
	
	/**
	 * Directory of tmp files.
	 *
	 * @var string
	 */
	public $tmp = '/resources/totsy/tmp/';
	
	public function run() {
		Logger::info('Order Shipped Processor');
		Environment::set($this->env);
		$this->tmp = LITHIUM_APP_PATH . $this->tmp;
		$pid = new Pid($this->tmp,  'OrderShippedNotification');
		if ($pid->already_running == false) {
			$this->emailNotificationSender();
		} else {
			Logger::info("Already Running! Stoping Execution");
		}
		Logger::info('Order Shipped Finished');
	}
	
	protected function emailNotificationSender() {
	// collections;		
	    $ordersCollection = Order::connection()->connection->orders;
		$usersCollection = User::connection()->connection->users;
		$ordersShippedCollection = OrderShipped::collection();
		$itemsCollection = Item::collection();
		
		$keys = array('OrderId' => true);
		$inital = array('totalItems' => 0, 'Tracking' => 0, 'TrackNums' => array());
		$reduce = new MongoCode("function(a,b){ 
			b.totalItems += 1;
			if (b.Tracking == 0 && typeof(a['Tracking #']) != 'undefined'){
				b.Tracking = a['Tracking #'];
			}
			
			if (typeof(b.TrackNums[b.Tracking]) == 'undefined'){
				b.TrackNums[b.Tracking] = new Array();
			}
			
			b.TrackNums[b.Tracking].push(a['ItemId']);
		}");
		//Conditions with date converted to the right timezone
		$conditions = array(
			'ShipDate' => array(
				'$gte' => new MongoDate(mktime(0, 0, 0, date("m"), date("d"), date("Y"))),
				'$lt' => new MongoDate(mktime(0, 0, 0, date("m"), date("d")-4, date("Y")))
			),
			'OrderId' => array('$ne' => null)//,
			//'OrderNum' => '4DBA18C5F223',
		    // don't validate TRCK # because sometimes there could shipped item without tracking # 
			// validate tracking number
			//'Tracking #' => new MongoRegex("/^[1Z]{2}[A-Za-z0-9]+/i"),
			// do not send notification if it already send
			//'emailNotification' => array('$exists' => false)
		);

		$results = $ordersShippedCollection->group($keys, $inital, $reduce, $conditions);
		
		if (is_object($results) && get_class_name($results)=='MongoCursor'){
			Logger::info('Found "'.$results::count().'" orders');
		} else if ( is_array($results)){
			if (array_key_exists('errmsg',$results)){
				Logger::info('ERROR: "'.$results['errmsg']);
				// to make shure that process closes correctly
				return false;
			}
			$results = $results['retval'];
			Logger::info('Found "'.count($results).'" orders');
		}
		
		$cc = 0;
		foreach ($results as $result){
			if (count($result['TrackNums'])>0){
				$data = array();
				$data['order'] = $ordersCollection->findOne(  array('_id' => $result['OrderId']));
				$data['user'] = $usersCollection->findOne(array('_id' => $data['order']['user_id']));
				$data['email'] = $data['user']['email'];
				$data['items'] = array();
			
				$ordItm = array();
				foreach($data['order']['items'] as $itm){
					$ordItm[$itm['item_id']] = $itm;
				}
				
				foreach($result['TrackNums'] as $trackNum => $items){
					foreach ($items as $item){
						$data['items'][$trackNum][ (string) $item ] = $ordItm[ (string) $item ];
					}
				}
				unset($ordItm);

				Logger::info('Sening email for order #'.$result['OrderId'].' to '.$data['email']);
				Silverpop::send('orderShipped', $data);
				unset($data);
				
				//SET send email flag
				foreach($result['TrackNums'] as $trackNum => $items){
					foreach ($items as $item){
						$conditions = array(
								'ItemId' =>  $item, 
								'OrderId' => $result['OrderId']
						);
						$ordersShippedCollection->update($conditions, array('$set' => array('emailNotificationSend' => new MongoDate())));
					}
				}
				
			}
		}
		
		
	}
}

?>