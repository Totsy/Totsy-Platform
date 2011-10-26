<?php

namespace admin\models;

class Affiliate extends Base {

	protected $_meta = array('source' => 'affiliates');

	protected $_schema = array(
			'_id' => array('type' => 'id'),
			'invitation_codes'=>array('type'=>'array', 'null'=>false ),
			'affiliate'=>array('type'=>'boolean', 'null'=>false, 'default'=>true),
			'active'=>array('type'=>'boolean', 'null'=>false, 'default'=>true)
			);

	public static function pixelFormating($pixels, $codes){
		if ( empty($pixels) ){ return array(); }
		$formatted = array();
		foreach($pixels as $key=>$pixel){
		    if ($pixel['enable'] == '1' || $pixel['enable'] == 'on'){
		    	$temp['enable'] = true;
		    } else {
		    	$temp['enable'] = false;
		    }
		    if ($pixel['enable']
		    	&& array_key_exists('page', $pixel)
		    	&& in_array('/a/', $pixel['page'])) {
		    	foreach($codes as $value){
		    		$pixel['page'][] = '/a/' . $value;
		    	}
		    }
		    $temp['page'] = array_values($pixel['page']);
		    $temp['pixel'] = $pixel['pixel'];

		    $formatted[] = $temp;

		}
		return $formatted;
	}

	public static function landingPages(){
		$landing = array();
	}
}

?>