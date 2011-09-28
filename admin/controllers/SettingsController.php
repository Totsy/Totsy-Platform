<?php

namespace admin\controllers;

use admin\models\Setting;
use admin\models\User;
use admin\extensions\Mailer;
use MongoDate;
use MongoId;
use MongoRegex;
use MongoCollection;

class SettingsController extends \admin\controllers\BaseController {

	public function index() {
		if ($this->request->data['submit']) {
			$store_credit_card = $this->request->data['store_credit_card'];
			
			if (!$store_credit_card) {
				$store_credit_card = 0;
			}

			$setting = Setting::find('all',array('variable' => 'store_credit_card'));
			$setting_array = $setting->data();			
			
			if (sizeof($setting_array) == 0) {
				$setting = Setting::create();
				$setting->variable = "store_credit_card";
				$setting->value = $store_credit_card;
				$setting->save();
			} else if (sizeof($setting_array) > 0) {	
				$setting->variable = "store_credit_card";
				$setting->value = $store_credit_card;
				$setting->save();			
			}
			
			//alert the tech list that the credit card storage toggle has changed
			if ($store_credit_card) {
				$data[store_credit_card_text] = 'true';
				
//				Mailer::send('Tech_Credit_Card_Storage_Change', 'tech@totsy.com', $data);
			}
		}
	}
}

?>