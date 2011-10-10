<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use admin\models\Order;
use admin\models\Item;
use admin\models\Promocode;
use admin\models\Dashboard;
use MongoDate;
use MongoRegex;
use MongoId;

/**
 * Fix orders with that use promo codes greater than their subtotal
 */
class ExportPromocodes extends \lithium\console\Command {

	/**
	 * The environment to use when running the command. db='development' is the default.
	 * Set to 'production' if running live when using a cronjob.
	 */
	public $env = 'development';
	public $beginning = "Today";
	public $end = "Now";

	public function run() {
		$this->header('Launching Create Promocode Detail....');
		Environment::set($this->env);
		$this->_fix();
		$this->out('Finished Create Promocode Detail');
	}

	/**
	 * The _fix method melts your brain
	 *
	 */
	protected function _fix() {
		// orders { oversizehandling, handling, service, promo_discount, promo_code, subTotal, tax, total }

		$startDate  = new MongoDate(strtotime($this->beginning));
		$endDate  = new MongoDate(strtotime($this->end));
		// Find all orders with free shipping service
		$conditions = array(
			'type' => 'promocodes',
			'date' => array(
				'$gte' => $startDate,
				'$lt' => $endDate)
		);
		$dashboards = Dashboard::find('all', array('conditions' => $conditions, 'order' => 'date_string'));//, 'limit' => 10
		
		$file = fopen('export_promo.csv', 'w');
		fwrite($file,"amount_saved,number_used,date_string,code,code_id,code_value,code_type,gross_total,net_total\n");
		
		foreach ($dashboards as $dashboard) {
			
			$dashboard = $dashboard->data();
			//var_dump($dashboard);
			//exit;
			
			foreach ($dashboard['codes'] as $code) {
				fputcsv($file, $code);
			}
			
		}

		//$this->out('Fixed ' . $i . ' order records.');
	}
}