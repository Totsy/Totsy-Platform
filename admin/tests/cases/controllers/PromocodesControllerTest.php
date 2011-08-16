<?php

namespace admin\tests\cases\controllers;

use \admin\controllers\PromocodesController;

class PromocodesControllerTest extends \lithium\test\Unit {

	public function testCreatingPromocodes() {
		
		$remote = new PromocodesController();
		//var_dump($remote);
		$post = array('enabled' => 'on',
					'code' =>'testcode2',
					'description' => 'testing code',
					'type'=> 'percentage',
					'discount_amount' => '.1',
					'minimum_purchase' => '60',
					'max_use' => '0',
					'start_date' => '12/27/2010 00:00',
					'end_date' => '01/03/2010'
				);
			
		$result = $remote->add($post);
		//var_dump($result);
		$this->assertEqual( true, $result );
		
	}
	
	public function testPromotionReporting() {
		
		$remote = new PromocodesController();
		$remote->request->data = array();
		$result = gettype($remote->report());
		
		$this->assertEqual('array', $result);
		
	}

	public function testPromotionReportingWithSearch() {
		
		$remote = new PromocodesController();
		$remote->request->data = array('search'=>'vipmom');
		$result = gettype($remote->report());
		
		$this->assertEqual('array', $result);
	}
	
	/*public function testPromocodeEditing(){
		$remote = new PromocodesController();
		
		$remote->request->data = array('enabled' => '',
					'code' =>'testcode2',
					'description' => 'testing code',
					'type'=> 'percentage',
					'discount_amount' => '.1',
					'minimum_purchase' => '60',
					'max_use' => '0',
					'start_date' => '12/27/2010 00:00',
					'end_date' => '01/03/2010'
				);
				
		$result = gettype($remote->edit('4d11059dde55906912000000'));
		
		$this->assertEqual( true, $result );
	}*/

}

?>
