<?php

namespace li3_payments\libraries\payments\adapter\interfaces;

use lithium\data\Collection;

interface VisaVerifiedInterface {
	
	public function visaVerify(Collection  $t);
	
}