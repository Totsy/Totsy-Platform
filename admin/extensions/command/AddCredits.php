<?php 
namespace admin\extensions\command;

use lithium\core\Environment;
use admin\models\Credit;
use admin\models\Order;
use admin\models\User;
use admin\models\Invitation;
use MongoDate;
use MongoRegex;
use MongoId;


/**
* Run this command to provide invitation credits for all users
* who have accepted an invitation and have sucessfully placed an order.
*/
class AddCredits extends \lithium\console\Command {
	
	/**
	* The environment to use when running the command. db='development' is the default.
	* Set to 'production' if running live when using a cronjob.
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
	* Main method for adding Credits.
	*
	* The run method will query 'Accepted' invitations
	* that have not yet been processed and have firrst pirachse.
	*
	*/
	public function run() {
		Logger::info("\n");
		Logger::info('Add Credits Process Started');
		Environment::set($this->env);
		
		$this->tmp = LITHIUM_APP_PATH . $this->tmp;
		$pid = new Pid($this->tmp,  'AddCredits');
		
		if ($pid->already_running == false) {
			$this->getCommandLineParams();
				
			$this->addCredits();
		} else {
			Logger::info('Already Running! Stoping Execution'."\n");
		}
		Logger::info('Add Credits Process Finished'."\n");
	}	
	
	public function addCredits(){
		// get users ivited users and codes
		$invitedCursor = Invitation::collection()->find(array(
			'status' => 'Accepted',
			'credited' => array( '$ne' => true ) 
		))->fields(array(
			'user_id' => true,
			'invitation_code' => true 
		));
		if (!$invitedCursor->hasNext()){
			Logger::info('No pending invitations found'."\n");
			unset($invitedCursor);
			return ;
		}
		$total = 0;
		while($invitedCursor->hasNext()){
			$row = $invitedCursor->getNext();
			// check invitation code and get invitation sender
			if( $this->_checkInvitationCodes($row) == false ){
				unset($row);
				continue;
			}
			// check if invited user made a order
			if( $this->_checkUserOrder($row) == false ){
				unset($row);
				continue;
			}
			// just to make sure that we have necessary data
			if (empty($row['invited_by_user_id']) || empty($row['has_shipped_order'])){
				unset($row);
				continue;				
			}
			//Apply credits
			$this->_addCredit($row);
			//Upadte invitations
			$this->_updateInvitations($row);
			$total ++;
			unset($row);
		}
		Logger::info('Added Credits for '.$total.' invitations'."\n");
		unset($invitedCursor);
	}
	
	private function _checkInvitationCodes(&$row){
		$userCursor = User::collection()->find(array('invitation_codes'=>$row['invitation_code']));
		if (!$userCursor->hasNext()){
			unset($userCursor);
			return false;
		}
		
		$inviter = $userCursor->getNext();
		$row['invited_by_user_id'] = (string) $inviter['_id'];
		$row['invited_by_user_email'] = $inviter['email'];
		
		unset($inviter, $userCursor);
		
		return true;
	}
	
	private function _checkUserOrder(&$row){
		$orderCursor = Order::collection()->find(array(
			'user_id' => $row['user_id']
		))->fileds(array('_id' => true));
		
		if (!$orderCursor->hasNext()){
			unset($orderCursor);
			return false;
		}
		$orders = array();
		while($orderCursor->hasNext()){
			$order = $orderCursor->getNext();
			$orders[] = (string) $order['order_id'];
		}
		unset($orderCursor);
		
		if (empty($orders)){ return false;	}
		
		// check if there is any shipped otrders
		if( $this->_checkOrderShipped($orders) == false ){
			unset($orders);
			return false;
		}
		
		$row['has_shipped_order'] = true;
		
	}
	
	private function _checkOrderShipped( (array) $orders = array() ){
		
		$shippedOrders = OrderShipped::collection()->find(array(
					'OrderId' => array( '$in' => $orders ),
					'Tracking #' => array( '$exists' => true )
		))->count();
		
		if ($shippedOrders>0){
			return true;
		} else {
			return false;
		}
	}
	
	private function _addCredit(&$row){
		$data = array(
			'user_id' => $row['invited_by_user_id'],
			'description' => 'Invite accepted to : '.$row['invited_by_user_email']. 
							 ', for inviting: '.$row['email']
		);
		$options = array('type' => 'Invite');
		if (Credit::add($data, $options) && User::applyCredit($data, $options)) {
			Logger::info('add-credit: Added Credit to UserId: '.$row['invited_by_user_id'] );
			$row['credit_added'] = true;
		}
	}
	
	private function _updateInvitations(&$row){
		Invitation::collection()->update(
			array('_id'=>$row['_id']),
			array('$set'=>array(
				'status' => 'Sent',
				'credited' => true
			))
		);
	}
	
}

?>