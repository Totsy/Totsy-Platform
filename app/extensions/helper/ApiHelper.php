<?php 
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