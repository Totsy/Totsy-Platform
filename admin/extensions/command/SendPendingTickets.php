<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use lithium\analysis\Logger;
use admin\extensions\Mailer;
use admin\models\Ticket;
use MongoDate;

/**
 * Make a check for a normal transaction email.
 */
class SendPendingTickets extends \lithium\console\Command {
	
	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';
	/**
	 * Email address to send
	 *
	 * @var string
	 */
	public 	$email = 'support@totsy.com';
	/**
	 * Sailthru Template to use.
	 *
	 * @var string
	 */
	public $template = 'Tickets';	
	/**
	 * Instances
	 */
	public function run() {
		Environment::set($this->env);

		$tickets = $this->getPending();

		foreach($tickets as $ticket) {
		//	$email = $ticket['user']['email'];
			Logger::debug("Sending mail {$ticket['user']['email']}");
			$email = $this->email;
			if (array_key_exists('email', $ticket['user']) && !empty( $ticket['user'])){
				$options['replyto'] = $options['behalf_email'] = $email;
			} else if (array_key_exists('confirmemail',$ticket['user']) && !empty($ticket['user']['confirmemail'])){
				$options['replyto'] = $options['behalf_email'] = $ticket['user']['confirmemail'];
			} 
			$ticket['date_created'] = date('m/d/Y H:i:s', $ticket['date_created']->sec);
			$status = Mailer::send('Tickets', $email, $ticket, $options);
			if (array_key_exists('error', $status)) {
				Logger::debug('Error : ' . $status);
				Ticket::collection()->update(array('_id' => $ticket['_id']),array(
						'$set' => array('status' => 'Pending')
					));
			} else {
				Ticket::collection()->update(array('_id' => $ticket['_id']),array(
						'$set' => array('status' => 'Sent' , 'date_sent' => new MongoDate())
					));
			}
		}
		
	}
	protected function getPending() {
		return Ticket::collection()->find(array('status' => 'Pending'));
	}
}
?>