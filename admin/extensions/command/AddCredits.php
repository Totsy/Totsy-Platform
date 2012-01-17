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
		$list = array('tmp.invites','tmp.credited');
		// set database
		$totsy = Invitation::connection()->connection; 
		
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
				
		$this->tmpInvites = $totsy->{'tmp.invites'};
		$this->tmpInvites->ensureIndex(array('user_id'=>1));
		
		$this->tmpCredited = $totsy->{'tmp.credited'};
		$this->tmpCredited->ensureIndex(array('email'=>1));
	}
	
	public function clean(){
	
		// list of tmp collections to check
		$list = array('tmp.invites','tmp.credited');
		// set database
		$totsy = Invitation::connection()->connection;
		// drop tmp collections if any
		$dbs = $totsy->listCollections();
		foreach ($dbs as $db){
			if (in_array($db->getName(),$list)){
				$db->drop();
			}
		}
	}
	
	public function applyFilterCredits(){
		// get users ivited users and codes
		$invitedCursor = Invitation::collection()->find(array(
			'credited' => array( '$ne' => true ),
			'processed' => array( '$ne' => true )
		));
		if (!$invitedCursor->hasNext()){ 
			Logger::info('No pending invitations found'."\n");
			unset($invitedCursor);
			return ;
		}
		$invitedCursor->timeout(50000);
		
		while($invitedCursor->hasNext()){

			$row =  $invitedCursor->getNext();

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
					if (array_key_exists($mail5, $add)){
						$add[$mail5]['invitation_ids'][] = (string) $row['_id'];
					} else {
						$add[$mail5] = array(
							'email' => $email,
							'invitation_ids' => array((string) $row['_id'])
						);
					}
				}
				
				$this->tmpInvites->update(
					array('_id'=>$row['_id']),
					array(
						'user_id' => $row['user_id'],
						'emails' => $add,
						'email_counter' => count($add)
				));
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
			unset($add,$emails);
		}
		unset($invitedCursor);
	}
	
	public function addCredits(){
		$invitedCursor = $this->tmpInvites->find(array());
		
		if (!$invitedCursor->hasNext()){
			Logger::info('No filtered invitations found'."\n");
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
				
				// check if invited user made a order
				if( $this->_checkUserOrder() == false ){
					$this->row = array();
					continue;
				}
 				
				//check for credited
				if( $this->_checkForCredited() === false ){
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
		Logger::info('Added Credits for '.$total.' invitations'."\n");
		unset($invitedCursor);
	}
	
	private function _checkForCredited(){
		$num  = Invitation::collection()->find(array(
			'email' => $row['email']
		))->count();
		
		if ($num>0){
			return true;
		} else {
			return false;
		}
	}
	
	private function _updateTmpInvites(&$tmpInvites,&$row){
		
		BlackBox::AddCreditsOUT("\n".'Updating tmp invites processed: '.$row['_id']);
		
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