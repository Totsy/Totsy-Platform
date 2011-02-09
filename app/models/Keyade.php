<?php

namespace app\models;

use \lithium\data\Connections;
use \MongoDate;
use \MongoId;


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
			'created_date' => array( '$gte' => $data['start_date']),
			'created_date' => array( '$lt' => $data['end_date'])
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
	public static function signupsByReferral($start = null, $end = null){
		
	}
	
	/**
	 * Sales list
	 * 
	 * @return xml object
	 */
	public static function sales($start = null, $end = null){
		
	}
	
	/**
	 * Referring Sales list
	 *
	 * @return xml object
	 */
	public static function referringSales($start = null, $end = null){
		
	}
	
}
