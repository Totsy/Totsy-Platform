<?php

namespace app\models;

use lithium\data\Connections;
use MongoDate;
use MongoId;


class Keyade extends \lithium\data\Model {

	/**
	 * Subscriber list, provided by date
	 *
	 * @return xml object
	 */
	public static function signups( $data ){
		$connection = static::_connection()->connection->users;
		$options = array(
			'keyade_user_id' => array( '$exists' => true),
			'created_date' =>  array(
				'$gte' => $data['start_date'],
				'$lte' => $data['end_date']
			)
		);
		// Run that sucker!
		$cursor = $connection->find( $options );
		//echo "There are " . $cursor->count() . " users in this resultset.\n";
		$output = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<!DOCTYPE report PUBLIC "report" "https://dtool.keyade.com/dtd/conversions_v5.dtd">
<report>
';
		foreach( $cursor AS $row ){
			$output .= '	<entry clickId="' . $row['keyade_user_id'] . '" eventMerchantId="' . $row['_id'] . '" count1="1" time="' .  $row['created_date']->sec . '" eventStatus="confirmed" />
';
		}
		$output .= "</report>\n";
		echo $output;
	}

	/**
	 * Signups by Referral list
	 *
	 * @return xml object
	 */
	public static function signupsByReferral($data){
		$connection = static::_connection()->connection->users;
		$options = array(
			'invited_by' => array( '$exists' => true),
			'created_date' =>  array(
				'$gte' => $data['start_date'],
				'$lte' => $data['end_date']
			)
		);
		// Run that sucker!
		$cursor = $connection->find( $options );
		// Loop through the cursor, and identify users that were invited by keyade users
		$referrals = array();
		foreach($cursor AS $user){
			$inviter = $connection->findOne( array( 'invitation_codes' => $user['invited_by'], 'keyade_user_id' => array('$exists' => true)));
			if($inviter != null){
				// We got a user invited from a keyade referral
				$user['keyade_referral_user_id'] = $inviter['keyade_user_id'];
				$referrals[] = $user;
			}
		}
		// If we have keyade referred signups, output them
		if(count($referrals != 0) && ($referrals !=  null)){
			$output = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
			<!DOCTYPE report PUBLIC "report" "https://dtool.keyade.com/dtd/conversions_v5.dtd">
			<report>
			';
			foreach($referrals AS $row){
							$output .= '	<entry clickId="' . $row['keyade_user_id'] . '" eventMerchantId="' . $row['_id'] . '" count1="1" time="' .  $row['created_date']->sec . '" eventStatus="confirmed" />
				';
			}
			$output .= "</report>\n";
			echo $output;
		}
	}

	/**
	 * Sales list
	 *
	 * @return xml object
	 */
	public static function sales($data){
		$c_users = static::_connection()->connection->users;
		$c_orders = static::_connection()->connection->orders;
		$output = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
		<!DOCTYPE report PUBLIC "report" "https://dtool.keyade.com/dtd/conversions_v5.dtd">
		<report>
		';
		// Get a list of orders from the requested date range
		$options = array(
			'date_created' =>  array(
				'$gte' => $data['start_date'],
				'$lte' => $data['end_date']
			)
		);
		// Run that sucker!
		$orders = $c_orders->find( $options );
		foreach($orders AS $order){
			// test to see if a user_id is a numeric or objectid
			if((strlen($order['user_id']) != 24)){
				// numeric id
				$user_id = $order['user_id'];
			}else{
				// objectid
				$user_id = new MongoId( $order['user_id'] );
			}
			$user = $c_users->findOne( array( '_id' => $user_id, 'keyade_user_id' => array( '$exists' => true) ));
			if($user != null){
				// KEYADE ORDER!
				$output .= '	<entry clickId="' . $user['keyade_user_id'] . '" lifetimeId="' . $order['user_id'] . '" eventMerchantId="' . $order['order_id'] . '" value1="' . $order['total'] . '" time="' .  $order['date_created']->sec . '" />
				';

			}
		}
		$output .= "</report>\n";
		echo $output;
	}

	/**
	 * Referring Sales list
	 *
	 * @return xml object
	 */
	public static function referringSales($data){
		$c_users = static::_connection()->connection->users;
		$c_orders = static::_connection()->connection->orders;
		$output = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
		<!DOCTYPE report PUBLIC "report" "https://dtool.keyade.com/dtd/conversions_v5.dtd">
		<report>
		';
		// Get a list of orders from the requested date range
		$options = array(
			'date_created' =>  array(
				'$gte' => $data['start_date'],
				'$lte' => $data['end_date']
			)
		);
		// Run that sucker!
		$orders = $c_orders->find( $options );
		foreach($orders AS $order){
			// test to see if a user_id is a numeric or objectid
			if((strlen($order['user_id']) != 24)){
				// numeric id
				$user_id = $order['user_id'];
			}else{
				// objectid
				$user_id = new MongoId( $order['user_id'] );
			}
			// See if the user was invited
			$user = $c_users->findOne( array( '_id' => $user_id, 'invited_by' => array( '$exists' => true) ));
			if($user != null){
				// Invited user
				$inviter = $c_users->findOne( array( 'invitation_codes' => $user['invited_by'], 'keyade_user_id' => array( '$exists' => true)));
				if($inviter != null){
					// KEYADE ORDER!
					$output .= '	<entry clickId="' . $inviter['keyade_user_id'] . '" lifetimeId="' . $order['user_id'] . '" eventMerchantId="' . $order['order_id'] . '" value1="' . $order['total'] . '" time="' .  $order['date_created']->sec . '" />
					';

				}
			}
		}
		$output .= "</report>\n";
		echo $output;
	}

}
