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
		$pid = new Pid($this->tmp,  'OrderExport');
		if ($pid->already_running == false) {
			$this->emailNotificationSender();
		} else {
			Logger::info("Already Running! Stoping Execution");
		}
		Logger::info('Order Shipped Finished');
	}
	
	protected function emailNotificationSender() {
	// collections
		$ordersCollection = Order::connection()->connection->orders;
		$usersCollection = Order::connection()->connection->users;
		$ordersShippedCollection = OrderShipped::collection();
		$itemsCollection = Item::collection();
		
		$time = time();
		$keys = array('OrderId' => true);
		$inital = array('totalItems' => 0, 'totalTracking' => 0, 'TrackNums' => array() );
		$reduce = new MongoCode("function(a,b){ 
			b.totalItems += 1;
			if (typeof(b.TrackNums[a['Tracking #']]) == 'undefined'){
				b.TrackNums[a['Tracking #']] = new Array();
				b.totalTracking += 1;
			}
			
			b.TrackNums[a['Tracking #']].push(a['ItemId']);
		}");
		//Conditions with date converted to the right timezone
		$conditions = array(
			'ShipDate' => array(
				'$gt' => new MongoDate(strtotime('-1 day',$time)),
				'$lte' => new MongoDate($time)),
			'OrderId' => array('$ne' => null),
			// validate tracking number
			'Tracking #' => new MongoRegex("/^[1Z]{2}[A-Za-z0-9]+/i"),
			// do not send notification if it already send
			'emailNotification' => array('$exists' => false)
		);

		$results = $ordersShippedCollection->group($keys, $inital, $reduce, $conditions);
		
		if (is_object($result) && get_class_name($results)=='MongoCursor'){
			Logger::info('Found "'.$result::count().'" orders');
		} else if ( is_array($result)){
			$results = $results['retval'];
			Logger::info('Found "'.count($results).'" orders');
		}
		foreach ($results as $result){
			if ($result['totalTracking']>0){
				$data = array();
				$data['order'] = Order::find('first', array('conditions' => array('_id' => $result['OrderId'])));
				$data['user'] = $usersCollection->find(array('_id' => $data['order']->user_id));
				$data['email'] = $data['user']['email']; 
				$data['items'] = array();
				foreach($result['TrackNums'] as $trackNum => $items){
					foreach ($items as $item){
						$data['items'][$trackNum][ (string) $item ] = Item::find('first', array('conditions' => array('_id' => $item)))->data();
					}
				}
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