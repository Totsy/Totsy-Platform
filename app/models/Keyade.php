<?php

namespace app\models;

use lithium\data\Connections;
use MongoDate;
use MongoId;
use lithium\util\Validator;


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
		return $output;
	}

	/**
	 * Signups by Referral list
	 *
	 * @return xml object
	 */

	public static function signupsByReferral($data){
	    $output = '';
		$referrals = Invitation::collection()->find(array(
		    'user_id' => array('$ne' => "4292"),
		    'status' => 'Accepted',
		    'date_accepted' => array(
				'$gte' => $data['start_date'],
				'$lte' => $data['end_date']
			)), array(
		    'user_id' => true,
		    'email' => true,
		    'date_accepted' => true,
		    '_id' => false
		));

		$user_ids = array();
		$emails = array();
		$info = array();

		//Custom validator to test for mongo ids
		 Validator::add('mongoId', function($value) {
			return (strlen($value) >= 10) ? true : false;
		});

        //gather information for futher queries
		foreach($referrals as $referral) {
		    $user_ids[] = (string) $referral['user_id'];
		    if (Validator::isMongoId($referral['user_id'])) {
                 $user_ids[] = new MongoId($referral['user_id']);
            }
            $emails[] = $referral['email'];
            $info[$referral['email']] = array(
                '_id' => '',
                'ref_id' => $referral['user_id'],
                'keyade_user_id' => '',
                'date_accepted' => $referral['date_accepted']
            );
		}

		$emails = array_unique($emails);

		//retrieve referrer's keyade_user_ids
		$referrers = User::collection()->find(array(
		    '_id' => array('$in' => $user_ids),
		    'keyade_user_id' => array('$exists' =>true)),
        array(
            'keyade_user_id' =>true,
            '_id' => true
        ));

        //closure that searches on a multi-dimensional array level and returns the level up key associated
        $multiD_search = function($haystack, $needle, $multi_key = null, $return_key_value = null) {
            foreach ($haystack as $key => $value) {
                if (is_array($value) ) {
                    if ($multi_key && ((string)$value[$multi_key] == $needle) && $return_key_value) {
                        return $value[$return_key_value];
                    }
                } else {
                    return false;
                }
            }
            return false;
        };

        foreach($info as $email => $record) {
            if ($keyade_user_id = $multiD_search($referrers, (string)$record['ref_id'],'_id','keyade_user_id')) {
               // var_dump($referred_user);
                $info[$email]['keyade_user_id'] = $keyade_user_id;
            } else {
                unset($info[$email]);
            }
        }
        $emails = array_keys($info);

        //retrieve referrer's keyade_user_ids
		$referrees = User::collection()->find(array(
		    'email' => array('$in' => $emails)),
        array(
            '_id' => true,
            'email' => true
        ));

        foreach($referrees as $key => $value) {
            $info[$value['email']]['_id'] = $key;
        }

		// If we have keyade referred signups, output them
		if((count($info) != 0)){
			$output = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
			<!DOCTYPE report PUBLIC "report" "https://dtool.keyade.com/dtd/conversions_v5.dtd">
			<report>
			';
			foreach($info AS $row){

				$output .= '	<entry clickId="' . $row['keyade_user_id'] . '" eventMerchantId="' . $row['_id'] . '" count1="1" time="' .  $row['date_accepted']->sec . '" eventStatus="confirmed" />
				';
			}
			$output .= "</report>\n";
		}
		return $output;
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
		$orders = $c_orders->find( $options , array(
		    'order_id' => true,
		    'total' => true,
		    'date_created' => true,
		    'user_id' => true
		    ));
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
		return $output;
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
		return $output;
	}

}
