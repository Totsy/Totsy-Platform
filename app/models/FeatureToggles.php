<?php

namespace app\models;

class FeatureToggles extends Base {

	public static function getValue($feature_name = null) {
		$state = false;
		$featuretoggle = static::find('first',array('conditions' => array('feature' => $feature_name)));
		if(!empty($featuretoggle['value'])) {
			$state = $featuretoggle['value'];
		}
		return $state;
	}
}

?>