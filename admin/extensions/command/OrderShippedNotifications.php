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
use admin\extensions\command\Pid;
use admin\extensions\Mailer;
use admin\extensions\helper\Shipment;

/**
 * Process email notifications for orders shipped.
 *
 * Since 06-29-2011 supports command line params.
 * (only for public or protected variables)
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
	/**
	 *
	 * Flag for debug mode and email address -
	 * if non-null, we're debugging and sending
	 * info to the specified email address.
	 *
	 * Example for debug:
	 * protected $debugemail = "skosh@totsy.com";
	 * Example for production:
	 * protected $debugemail = null;
	 *
	 * Example for debug specefied in cli:
	 * li3 order-shipped-notifications --debugemail=skosh@totsy.com
	 */
	protected $debugemail = null;

	public function run() {
		Logger::info('Order Shipped Processor');
		Environment::set($this->env);
		$this->tmp = LITHIUM_APP_PATH . $this->tmp;
		$pid = new Pid($this->tmp,  'OrderShippedNotification');
		if ($pid->already_running == false) {
			$this->getCommandLineParams();
			$this->emailNotificationSender();
		} else {
			Logger::info("Already Running! Stoping Execution");
		}
		Logger::info('Order Shipped Finished');
	}

	protected function emailNotificationSender() {
	// collections;
	    $ordersCollection = Order::collection();
		$usersCollection = User::collection();
		$ordersShippedCollection = OrderShipped::collection();
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
			b.TrackNums[b.Tracking].push({ 'id': a['ItemId'], 'sku': a['SKU'] });
		}");
		//Conditions with date converted to the right timezone
		$conditions = array(
			'ShipDate' => array(
				'$gte' => new MongoDate(mktime(0, 0, 0, date("m"), date("d")-2, date("Y"))),
				'$lt' => new MongoDate(mktime(0, 0, 0, date("m"), date("d"), date("Y"))) 
			),
			'OrderId' => array('$ne' => null),
			'emailNotificationSent' => array('$exists' => false));

		$results = $ordersShippedCollection->group($keys, $inital, $reduce, $conditions);
		if (is_object($results) && get_class_name($results)=='MongoCursor'){
			Logger::info('Found "'.$results::count().'" orders');
		} else if ( is_array($results)){
			if (array_key_exists('errmsg',$results)){
				Logger::info('ERROR: "'.$results['errmsg'].'"');
				// to make sure that process closes correctly
				if (!isset($results['retval']) || count($results['retval'])==0){
					return false;
				}
			}
			$results = $results['retval'];
			Logger::info('Found "'.count($results).'" orders');
		}
		$skipped = array();
		$c = 0;
		$shipment = new Shipment();
		foreach ($results as $result){
			if (count($result['TrackNums'])>0){
				$do_break = false;
				$data = array();
				$data['order'] = $ordersCollection->findOne(  array('_id' => $result['OrderId'] ));
				$data['user'] = $usersCollection->findOne(array('_id' => $this->getUserId($data['order']['user_id']) ));
				if (is_null($this->debugemail)) {
					$data['email'] = $data['user']['email'];
				} else {
					$data['email'] = $this->debugemail;
				}
				$data['items'] = array();
				$itemSkus = Item::getSkus($data['order']['items']);
				$problem = '';
				foreach($result['TrackNums'] as $trackNum => $items){
					if ( $trackNum == 0 || (strlen($trackNum) < 15 && $data['order']['auth_confirmation'] < 0) ){
						$problem = 'No tracking number and payment auth confirmation error';
						$do_break = true;
						break;
					}
					if ( $do_break===false ){
						$itemCount = 0;
						foreach ($items as $item){
							if (!array_key_exists($item['sku'] , $itemSkus)){
								Logger::info('Items don\'t match [' . $data['order']['order_id'] . ']');
								$problem = 'Some items don\'t match';
								$do_break = true;
								break;
							}
							$data['items'][$trackNum][ (string) $item['id'] ] = $itemSkus[ $item['sku'] ];
							$data['urls'][$trackNum] = $shipment->linkNoHTML($trackNum,array('type'=>'UPS'));
							$itemCount++;
						}
					}
				}
				if ($do_break===true){
					Logger::info('skip ['.$data['order']['order_id'].']');
					$do_break = false;
					$skipped[] = array(
					    'OrderId'=>$data['order']['order_id'],
					    'MongoId'=>$result['OrderId'],
					    'problem' => $problem
					);
					continue;
				}

				unset($itemSkus);
				unset($do_break);
				Logger::info('Trying to send email for order #' . $data['order']['order_id'] .
				    '(' . $result['OrderId'] . ' to ' . $data['email'] .
				    ' (total items: ' . $itemCount . ')');
				Mailer::send('Order_Shipped', $data['email'], $data);
				#Send An Email To The Person Who Invited during First Purchase Case
				if (array_key_exists('purchase_count' , $data['user']) &&
				    $data['user']['purchase_count'] == 1 && !empty($data['user']['invited_by'])) {
					$inviter = $usersCollection->findOne(array('invitation_codes' => $data['user']['invited_by']));
					if (is_null($this->debugemail)) {
						Mailer::send('Invited_First_Purchase', $inviter['email']);
					} else {
						Mailer::send('Invited_First_Purchase', $this->debugemail);
					}
				}
				unset($data);
				if(is_null($this->debugemail)) {
					foreach($result['TrackNums'] as $trackNum => $items){
						foreach ($items as $item){
							$conditions = array(
									'ItemId' =>  $item['id'],
									'OrderId' => $result['OrderId']
							);
							$ordersShippedCollection->update($conditions, array('$set' => array('emailNotificationSent' => new MongoDate())));
						}
					}
				}
			}

			// don't send more than 10 emails in debug mode
			if (!is_null($this->debugemail)) {
				if ($c==10){
					break;
				}
				$c++;
			}

		}

		if (count($skipped)>0){
			$data['skipped'] = $skipped;

			if(is_null($this->debugemail)) {
				$data['email'] = 'email-notifications@totsy.com';
			}
			else {
				$data['email'] = $this->debugemail;
			}
			Mailer::send('Order_Skipped', $data['email'], $data);
			unset($data);
		}
	}

	private function getUserId($id) {
		if (strlen($id)<10){
			return $id;
		} else {
			return new MongoId($id);
		}
	}

	private function getCommandLineParams(){
		$params = $this->request->params;
		$vars = get_class_vars(get_class($this));
		foreach ($vars as $var=>$value){
			if (array_key_exists($var,$params)){
				$this->{$var} = $params[$var];
			}
		}
	}

}

?>