<?php

namespace li3_payments\libraries\payments\adapter\interfaces;

use lithium\data\Collection;

interface UnlinkedCreditInterface {
	
	public function unlinkedCredit(Collection $t);
	
}