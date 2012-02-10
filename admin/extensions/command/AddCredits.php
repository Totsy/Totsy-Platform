<?php 

namespace admin\extensions\command;

use lithium\core\Environment;
use lithium\analysis\Logger;
use admin\models\Credit;
use admin\models\Order;
use admin\models\OrderShipped;
use admin\models\User;
use admin\models\Invitation;
use admin\extensions\BlackBox;

use MongoId;
use MongoRegex;
use MongoException;
use MongoCursorTimeoutException;

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
	 * Set development user id. Meaning process invitations
	 * only send by this user
	 */
	public $test = null;
	
	/**
	 * temporary var to store invitation data
	 * 
	 * @var array
	 */
	private $row = array();
	
	/**
	 * Tmp ref to credited emails collection
	 */
	private $tmpCredited = null;
	
	/**
	 * Tmp ref to filtered invitations
	 */
	private $tmpInvites = null;

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
			// check if tmp collections exist
			// and create proper handlers
			$this->setTmpCollections();
			// filter invitation data
			$this->applyFilterCredits();
			// apply cderits
			$this->addCredits();
			// clean tmp collection
			$this->clean();
		} else {
			Logger::info('Already Running! Stoping Execution'."\n");
		}
		Logger::info('Add Credits Process Finished'."\n");
	}	
	
	public function setTmpCollections(){
		
		// list of tmp collections to check
		$list = array('tmp.invites');
		// set database
		$totsy = Invitation::connection()->connection; 
		
		/** 
		 * drop collections only when number of rows that contains 'tmp'
		 * elements are equal to zero!
		 * When there is rows with tmp element - meaning cron job
		 * wasn't finishied succesfully so, finish what we had before.
		 */
		$conditions = array( 'tmp' => array('$exists' => true));
		$counter = Invitation::collection()->find($conditions)->count();

		if ($counter == 0) {
			// drop tmp collections if any
			$dbs = $totsy->listCollections();
			foreach ($dbs as $db){
				if (in_array($db->getName(),$list)){
					$db->drop();
				}
			}
			
			//create new tmp collections
			foreach( $list as $collection ){
				$totsy->createCollection(array(
					'create' => $collection,
					'capped' => false
				));
			}
		} else {
			$this->mssg('no db cleanup. found rows with tmp element: '.$counter);
		}
					
		$this->tmpInvites = $totsy->{'tmp.invites'};
		$this->tmpInvites->ensureIndex(array('user_id'=>1));
		
		// make sure all necessary fields have indexes
		$ensure_indexes = array( 'email', 'user_id', 'credited', 'tmp', 'creditet', 'status');
		$is = Invitation::collection()->getIndexInfo();
		$indexes = array();
		foreach ($is as $i){
			foreach ($i['key'] as $k=>$v){
				$indexes[] = $k;
			}
		}
		
		$indexes = array_unique($indexes);
		foreach ($ensure_indexes as $ie){
			if (!in_array($ie, $indexes)){
				Invitation::collection()->ensureIndex(array($ie => 1));
			}
		}
		
	}
	
	public function clean(){
	
		// list of tmp collections to check
		$list = array('tmp.invites');
		// set database
		$totsy = Invitation::connection()->connection;
		// drop tmp collections if any
		$dbs = $totsy->listCollections();
		foreach ($dbs as $db){
			if (in_array($db->getName(),$list)){
				$db->drop();
			}
		}
		
		// unset tmp flag
		Invitation::collection()->update(
			array('tmp' => array('$exists' => true)),
			array('$unset' => array('$tmp' => true))
		);
	}
	
	public function applyFilterCredits(){
		// get users ivited users and codes	
		$this->mssg('RUNNING');

		$conditions = array(
			'credited' => array( '$ne' => true ),
			'status' => 'Accepted',
			'tmp' => array('$exists' => false),
			/********** IMPORTANT **************/
			/* make sure to skip an old-scholl */
			/*	affiliate user that we no      */
			/* longer using. id is 4292  	   */
			/***********************************/
			'user_id' => array('$ne'=>'4292')
		);
		
		if (!empty($this->test)){
			$conditions['user_id'] = $this->test;
		}
		
		$invitedCursor = Invitation::collection()->find($conditions);
		if (!$invitedCursor->hasNext()){ 
			$this->mssg('No pending invitations found');
			unset($invitedCursor);
			return ;
		}
		$invitedCursor->timeout(100);
		
		$c = $invitedCursor->count();
		$process = true;
		$skip = null;
		while($process == true){  
			try{
				if (!$invitedCursor->hasNext()){
					$process = false;
					$this->mssg('No more records. finish "while" loop.');
					continue;
				}
			} catch (MongoException $e) {
				
				$this->mssg('-cursor timeout.');
				$invitedCursor = Invitation::collection()->find($conditions);	
				$invitedCursor->timeout(900);
				$this->mssg('-creteate new cursor....');
				$c = $invitedCursor->count();
				$this->mssg('found '.$c);
				if ($c==0){
					$process = false;
				} 
				continue;
			}
			if (!$invitedCursor->valid()){
				$this->mssg('cursor is not valid. Reset cursor'."\n");
				try{
					$skip = $invitedCursor->info();
					$invitedCursor = Invitation::collection()->find($conditions);
					$invitedCursor->skip($skip['numReturned']);
					$skip = null;
					$invitedCursor->rewind();
				} catch (MongoCursorTimeoutException $e){
					$this->mssg("\n".'cannot rewind');
					continue;
				}
				$this->mssg('rewined....');
				continue;
			}
			
			try{
				$row =  $invitedCursor->getNext();
			} catch (Exception $e){
				$this->mssg('Exception...');
				Logger::info('add-credits FATAL ERRROR. Check AddCredits LOG and OUT files');
				BlackBox::AddCreditsOUT(print_r($e,true));
				exit(0);
			}

			$tmpInviterCursor = $this->tmpInvites->find(array(
				'user_id'=>$row['user_id']
			));
			
			$emails = $this->_parseEmail($row);

			if ($emails==false){
				continue;
			}
			if ($tmpInviterCursor->hasNext()){
				$tmpInvite = $tmpInviterCursor->getNext();
				$add = $tmpInvite['emails'];
				
				foreach ($emails as $email){
					$mail5 = md5($email);
					$add[$mail5]['email'] = $email;
					$add[$mail5]['invitation_ids'][] = (string) $row['_id'];
				}

				$this->tmpInvites->update(
					array('_id'=>$tmpInvite['_id']),
					array( '$set' => array(
						'emails' => $add,
						'email_counter' => count($add)
				)));
			} else {
				
				$add = array();
				foreach ($emails as $email){
					$mail5 = md5($email);
					$add[$mail5] = array(
						'email' => $email,
						'invitation_ids' => array((string) $row['_id'])
					);
				}
				
				$this->tmpInvites->insert(array(
					'user_id' => $row['user_id'],
					'emails' => $add,
					'email_counter' => count($add)
				));
			}
			
			Invitation::collection()->update(
				array('_id' => $row['_id']),
				array('$set' => array( 'tmp' => true))
			);
			
			unset($add,$emails);
		}
		unset($invitedCursor);
	}
	
	private function mssg($msg){
		BlackBox::AddCreditsLog($msg);
	}
	
	public function addCredits(){
		$this->mssg('add credits chunk has started');
		
		$invitedCursor = $this->tmpInvites->find(array());
		
		if (!$invitedCursor->hasNext()){
			$this->mssg('No filtered invitations found');
			unset($invitedCursor);
			return ;
		}
	
		$total = 0; 
		while($invitedCursor->hasNext()){
		
			$row = $invitedCursor->getNext();
			
			foreach ($row['emails'] as $k=> $email){
				
				$invited_email = $email['email'];
				$invited = $this->_checkInvitedEmail(array(
					'ids' => $email['invitation_ids'],
					'user_id' => $row['user_id'],
					'email' => $invited_email
				));
				if( $invited == false ){
					$this->row = array();
					continue;
				}
				
				// check if invited user made a order
				if( $this->_checkUserOrder() == false ){
					$this->row = array();
					continue;
				}
 				
				//check for credited
				if( $this->_checkForCredited() === true ){
					$this->row = array();
					continue;
				}
				
				//Apply credits
				$this->_addCredit();
				
				//Upadte invitations
				$this->_updateInvitations();

				$total++;
				$this->row = array();
			}
			$this->_updateTmpInvites($row);
			unset($row);
		}
		$this->mssg('Added Credits for '.$total.' invitations'."\n");
		unset($invitedCursor);
	}
	
	private function _checkForCredited(){
		$num = Invitation::collection()->find(array(
			'email' => $this->row['email'],
			'credited' => true
		))->count();
		
		if ($num>0){
			BlackBox::AddCreditsOUT('Credit already applied to invter for email: '.$this->row['email']);
			return true;
		} else {
			return false;
		}
	}
	
	private function _updateTmpInvites(&$tmpInvites,&$row){
		
		BlackBox::AddCreditsOUT('Updating tmp invites processed: '.$row['_id']);
		
		$this->tmpInvites->update(
			array('_id'=> $row['_id']),
			array('$set'=>array(
				'processed' => true
			))
		);
	}
	
	private function _parseEmail (&$row){
		/*
		 * string "
		 * email@email.com\n email@email.com
		 * email@email.com\r\n email@email.com
		 * email@email.com\r\n\t email@email.com
		 * email@mail.com; email@email.com
		 * "Email" <email@email.com>, "Email" <email@mail.com> 
		 * "Email" <email@email.com>; "Email" <email@mail.com>
		 * Email (email@email.com)  
		 * "
		 */
		
		$r_email = preg_replace("/[\t]+/","",$row['email']);
		$r_email = preg_replace("/[\r\,\;]+/","\n",$r_email);
		$r_email = preg_replace("/[\n]+/","\n",$r_email);
		$r_email = preg_split("/[\n]/", $r_email);
		$emails = array();
		
		if (empty($r_email)){
			return false;
		}
		foreach ($r_email as $m){
			$m = trim($m);
			preg_match("/[\+\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?[\.A-Za-z0-9]{2,}/",$m,$e);
			if (!empty($e)){
				$emails = array_merge($emails,$e);
			}	
		}
		if (empty($emails)){
			unset($log,$lc);
			return false;
		}

		unset($r_email,$e);
		
		// skip
		if (sizeof($emails)>1){
			return false;
		}
		
		return $emails;
		
	}
	
	private function _checkInvitedEmail( array $row = array() ){		
		// get inviter info
		$noObject = false;
		$userCursor = User::collection()->find(array('_id'=> new MongoId($row['user_id']) ));
		if (!$userCursor->hasNext()){
			BlackBox::AddCreditsOUT('invite does not exist: '.$row['user_id']);
			unset($userCursor);
			$noObject = true;
			//return false;
		}
		
		if ($noObject == true){
			BlackBox::AddCreditsOUT('trying to find user without mongo id object');
			$userCursor = User::collection()->find(array('_id'=>$row['user_id'] ));
			if (!$userCursor->hasNext()){
				BlackBox::AddCreditsOUT('invite does not exist for non-object user id: '.$row['user_id']);
				unset($userCursor);
				return false;
			}
		}
		
		$inviter = $userCursor->getNext();
		if (!is_array($inviter['invitation_codes']) && is_string($inviter['invitation_codes'])){
			$inviter['invitation_codes'] = array($inviter['invitation_codes']);
		}
		unset($userCursor);
		
		// get invited user info
		$userCursor = User::collection()->find(array('email'=>$row['email']));
		if (!$userCursor->hasNext()){
			BlackBox::AddCreditsOUT('invited user with email does not exist: '.$row['email']);
			unset($userCursor);
			return false;
		}
		
		$invited = $userCursor->getNext();
		unset($userCursor);
		
		if (in_array($invited['invited_by'],$inviter['invitation_codes'])){
			
			$this->row['ids'] = $row['ids'];
			
			$this->row['invited_user_id'] = $invited['_id'];
			$this->row['email'] = $invited['email'];
			
			$this->row['invited_by_user_id'] = $inviter['_id'];
			$this->row['invited_by_user_email'] = $inviter['email'];
			
			unset($invited,$inviter);
			return true;
		} else {
			BlackBox::AddCreditsOUT('Invited user ('.$invited['email'].') accepted invitation from another inviter');
			BlackBox::AddCreditsOUT('invited by '.$invited['invited_by'].' VS. '.implode(',',$inviter['invitation_codes']));
		}
		
		unset($invited,$inviter);
		return false;
	}

	private function _checkUserOrder(){
		
		$orderCursor = Order::collection()->find(array(
			'user_id' => (string) $this->row['invited_user_id']
		))->fields(array('_id' => true, 'order_id' => true));
		
		if (!$orderCursor->hasNext()){
			BlackBox::AddCreditsOUT( 'no orders for user: '.$this->row['invited_user_id']);
			unset($orderCursor);
			return false;
		}
		$orders = array();
		while($orderCursor->hasNext()){
			$order = $orderCursor->getNext();
			$orders[] = (string) $order['order_id'];
		}
		unset($orderCursor);
		
		if (empty($orders)){ 
			BlackBox::AddCreditsOUT('no orders #2 for user: '.$this->row['invited_user_id']);
			return false;	
		}
		
		// check if there is any shipped otrders
		if( $this->_checkOrderShipped($orders) == false ){
			BlackBox::AddCreditsOUT('no Shipped orders for user: '.$this->row['invited_user_id']);
			unset($orders);
			return false;
		}
		
		$this->row['has_shipped_order'] = true;
		return true;
	}
	
	private function _checkOrderShipped( $orders = array() ){
		
		$shippedOrders = OrderShipped::collection()->find(array(
					'OrderNum' => array( '$in' => $orders ),
					'Tracking #' => array( '$exists' => true )
		))->count();
		
		if ($shippedOrders>0){
			return true;
		} else {
			return false;
		}
	}
	
	private function _addCredit(){
		$data = array(
			'user_id' => $this->row['invited_by_user_id'],
			'description' => 'Invite accepted to : '.$this->row['invited_by_user_email']. 
							 ', for inviting: '.$this->row['email']
		);
		$options = array('type' => 'Invite');
		if (Credit::add($data, $options) && User::applyCredit($data, $options)) {
			Logger::info('add-credit: Added Credit to UserId: '.$this->row['invited_by_user_id'] );
			$row['credit_added'] = true;
		}
	}
	
	private function _updateInvitations(){
		
		$collection = Invitation::collection();
		
		foreach ($this->row['ids'] as $id){
			$collection->update(
				array('_id'=> new MongoId( (string) $id ) ),
				array('$set'=>array(
					'status' => 'Sent',
					'credited' => true
				))
			);
		}
		
		unset($collection);
	}
	
}

?>