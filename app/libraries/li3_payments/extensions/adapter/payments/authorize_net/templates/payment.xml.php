<?php

use li3_payments\extensions\payments\ECheck;
use li3_payments\extensions\payments\CreditCard;

if ($payment instanceof CreditCard) {
	echo '<payment>';
	$data = array('card' => $payment);
	echo $this->view()->render('template', $data, array('template' => 'credit_card'));
	echo '</payment>';
}

if ($payment instanceof ECheck) {
	echo '<payment>';
	$data = array('account' => $payment);
	$this->view()->render('template', $data, array('template' => 'e_check'));
	echo '</payment>';
}

?>