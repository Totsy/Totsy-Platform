<?php

namespace li3_payments\libraries\payments\adapter\interfaces;

use lithium\data\Collection;

interface CreditCardStandardInterface {
	
	public function authorize(Collection $transaction);
	public function purchase(Collection $transaction);
	public function capture(Collection $transaction);
	public function credit(Collection $transaction);
	public function void(Collection $transaction);
	
}