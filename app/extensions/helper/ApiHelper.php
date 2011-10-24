<?php 

/* 2011-06-21 update notes
 *  - Moved errorCodes Method from Api::errorCodes() to ApiHelper::errorCodes();
 */

namespace app\extensions\helper;

use SimpleXMLElement;

class ApiHelper {

	public static function converter ($data,$format = 'xml'){
		if ($format == 'xml'){
			$xml = static::__toXml($data, new SimpleXMLElement('<root/>'));
			return $xml->asXML();
		} else if ($format == 'csv') {
			return static::__toCsv($data, '') ;
		}
	}
	
	public static function errorCodes($status = 500){
		$codes = Array(
			  2 => 'Missing HTTP-Request Parameter',
			  3 => 'Unknown AUTH_KEY',
			  4 => 'Unknown TIME',
			  5 => 'Unknown SIG',
			  6 => 'Invalid AUTH_KEY',
			  7 => 'Paremeter TIME is out of range',
			  8 => 'Invalid SIG',
			  9 => 'Unknown TMP_TOKEN',
			 10 => 'Invalid TMP_TOKEN',
			 11 => 'Expired TMP_TOKEN',
			 12 => 'Unknown TOKEN',
			 13 => 'Invalid TOKEN',
			 14 => 'Expired TOKEN',
			 15 => 'Unknown ITEM_ID',
			 16 => 'Invalid ITEM_ID',			 
			 81 => 'sig 1',
			 82 => 'sig 2',
			 83 => 'sig 3',
			 84 => 'sig 4',
			 85 => 'sig 5',
			 86 => 'sig 6',
			 93 => 'Variable "control" invalid',
			 94 => 'Variable "time" invalid',	 
			 95 => 'Variable "auth_id" invalid',
		 	 96 => 'No "auth_id" variable in the request',	    
			 97 => 'No "time" variable in the request',
			 98 => 'No "control" variable in the request',
			 99 => 'Request-URI Too short',
			100 => 'Continue', 		    
			101 => 'Switching Protocols',
			195 => 'Invalid TOKEN',
			196 => 'Unknown TOKEN',
			197 => 'Unknown AUTH_TOKEN',
			198 => 'Authorization failed',
			199 => 'Invalid AUTH_TOKEN', 		    
			200 => 'OK', 		    
			201 => 'Created', 		    
			202 => 'Accepted', 		    
			203 => 'Non-Authoritative Information', 		    
			204 => 'No Content', 		    
			205 => 'Reset Content', 		    
			206 => 'Partial Content', 		    
			300 => 'Multiple Choices', 		    
			301 => 'Moved Permanently', 		    
			302 => 'Found', 		    
			303 => 'See Other', 		    
			304 => 'Not Modified', 		    
			305 => 'Use Proxy', 		    
			306 => '(Unused)', 		    
			307 => 'Temporary Redirect', 		    
			400 => 'Bad Request', 		    
			401 => 'Unauthorized', 		    
			402 => 'Payment Required', 		    
			403 => 'Forbidden', 		    
			404 => 'Not Found', 		    
			405 => 'Method Not Found', 		    
			406 => 'Not Acceptable', 		    
			407 => 'Proxy Authentication Required', 		    
			408 => 'Request Timeout', 		    
			409 => 'Conflict', 		    
			410 => 'Gone', 		    
			411 => 'Length Required', 		   
			412 => 'Precondition Failed', 		    
			413 => 'Request Entity Too Large', 		    
			414 => 'Request-URI Too Long', 		    
			415 => 'Unsupported Media Type', 		    
			416 => 'Requested Range Not Satisfiable', 		    
			417 => 'Expectation Failed', 		    
			500 => 'Internal Server Error', 		    
			501 => 'Not Implemented', 		    
			502 => 'Bad Gateway', 		    
			503 => 'Service Unavailable', 		    
			504 => 'Gateway Timeout', 		    
			505 => 'HTTP Version Not Supported' 		
		);  
				
		if (is_numeric($status) && array_key_exists($status, $codes)) { 
			$error = array('code' => $status, 'message' => $codes[$status]);
		} else {
			// in case unknown error code ocurred
			$error = array('code' => 500, 'message' => $codes['500']); 
		}
		
		return compact('error');
	}
	
	protected static function __toCsv ($data, $csv, $options = null){
		
		if (is_array($data) && array_key_exists('products', $data)) {
			$data = $data['products']['product'];
		}
		
		if (is_array($options) && array_key_exists('addHeder', $options) && $options['addHeader']==true){ 
			$csv = implode('|', array_keys($data))."\n";
		}
		
	    foreach ($data as $k => $v) {
	        if(is_array($v) || is_object($v)) {
	        	$csv.= static::__toCsv($v, $csv)."\n";
	        } else {
				$csv .=  $v.'|';
	        }
	    }
	    return $csv;
	}
	
	protected static function __toXml ($data, $xml) {
		//if $xml
	    foreach ($data as $k => $v) {
	        if(is_array($v) || is_object($v)) {
	        	if (is_array($v) && array_key_exists('id', $v)){
	        		$child = $xml->addChild($k);
	        		$child->addAttribute('id', $v['id']);
	        		unset($v['id']);
	        		static::__toxml($v, $child);
	        	} else static::__toxml($v, $xml->addChild($k));
				
	        } else {
				$xml->addChild($k, $v);
	        }
	    }
	    return $xml;
	} 
}
?>