<?php

namespace app\models;

use lithium\data\Connections;
use MongoDate;
use MongoId;
use lithium\util\Validator;
use MongoRegex;


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

	public static function referringSales($data){
		$c_users = static::_connection()->connection->users;
		$c_orders = static::_connection()->connection->orders;
		$output = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
		<!DOCTYPE report PUBLIC "report" "https://dtool.keyade.com/dtd/conversions_v5.dtd">
		<report>
		';

		$options = array(
			'date_created' =>  array(
				'$gte' => $data['start_date'],
				'$lte' => $data['end_date']
			)
		);
		$orders = $c_orders->find($options, array(
		    'total' => true,
		    'order_id' => true,
		    'user_id' => true,
		    'date_created' => true));

		$ouser_info = array();
        $ouser_ids = array();
		foreach($orders as $order) {
		    $ouser_info[$order['user_id']][] = $order;
		    if (strlen($order['user_id']) > 10 ) {
		       $ouser_ids[] = new MongoId($order['user_id']);
		    }
		}
		$users = $c_users->find(array(
		    '_id' => array('$in' => $ouser_ids),
		    '$where' => "(this.invited_by != '/^keyade/i') && this.invited_by"),array(
		    'invited_by' => true,
		    'keyade_referral_user_id' => true
		    ));

        //separate those who already have keyade referrer id
        $no_referral_field = array();
        $invite_codes = array();
        foreach($users as $user) {
            if (array_key_exists('keyade_referral_user_id', $user)) {
                $ouser_info[(string)$user['_id']]['keyade_user_id'] = $user['keyade_referral_user_id'];
            } else {
                $no_referral_field[(string)$user['_id']] = $user;
                 $invite_codes[] = $user['invited_by'];
            }
        }
        $invite_codes = array_unique($invite_codes);
        $keyade_users = $c_users->find(array(
		    'invitation_codes' => array('$in' => $invite_codes),
		    'invited_by' => new MongoRegex('/^keyade/i')),array(
		    'invitation_codes' => true,
		    'keyade_user_id' => true
		    ));

	    /*
	    * closure that searches on a multi-dimensional array by one level.
	    * @var array haystack : array to search in
	    * @var mixed $needle : value to search for
	    * @var string $multi_key : key to search against
	    * @var string $return_key_value : name of key value to return
	    * @var bool $recursive : tell the function to search one more level down
	    * @var bool $return_multiple : tell function to return all matching values
	    * @return mixed :
	    *   - can return array if you set $return_multiple to `true`
	    *   - false if search failed
	    *   - return value
	    */
        $multiD_search = function($haystack, $needle, $multi_key = null, $return_key_value = null, $recursive = false, $return_multiple = false) {
            $multi = array();
            foreach ($haystack as $key => $value) {;
                if (is_array($value) ) {
                    if ($recursive) {
                        foreach($value as $s_key => $s_value) {
                            if ($multi_key && ((string)$s_value[$multi_key] == $needle) && $return_key_value) {
                                if ($return_multiple) {
                                    $multi[] = $s_value[$return_key_value];
                                } else {
                                    return $s_value[$return_key_value];
                                }
                            }
                        }
                    } else if ($multi_key && ((string)$value[$multi_key] == $needle) && $return_key_value) {
                        if ($return_multiple) {
                            $multi[] = $value[$return_key_value];
                        } else {
                            return $value[$return_key_value];
                        }
                    }
                } else {
                    return false;
                }
            }
            if ($return_multiple) {
                return $multi;
            } else{
                return false;
            }
        };

        foreach($keyade_users as $k_user) {
            $search = $multiD_search($no_referral_field, $k_user['invitation_codes'][0], 'invited_by', '_id',false,true);
            foreach($search as $result) {
                if (array_key_exists((string)$result, $ouser_info)) {
                    $ouser_info[(string)$result]['keyade_user_id'] = $k_user['keyade_user_id'];
                }
            }
        }
        //cleanup
        foreach($ouser_info as $key => $value) {
            if(!array_key_exists('keyade_user_id', $value)) {
                unset($ouser_info[$key]);
            }
        }

        if(!empty($ouser_info)){
            foreach($ouser_info as $id => $info) {
                foreach($info as $key => $value) {
                    if ($key != 'keyade_user_id') {
                        $output .= '	<entry clickId="' . $ouser_info[$id]['keyade_user_id'] .
                            '" lifetimeId="' . $value['user_id'] . '" eventMerchantId="' .
                                $value['order_id'] . '" value1="' . $value['total'] . '" time="' .
                                $value['date_created']->sec . '" />';
                    }
                }
            }
        }
		$output .= "</report>\n";
		return $output;
	}

}
?>