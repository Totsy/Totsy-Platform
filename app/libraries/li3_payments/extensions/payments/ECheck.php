<?php

namespace li3_payments\extensions\payments;

class ECheck extends \lithium\core\Object {

	const TYPE_PERSONAL = 'personal';

	const TYPE_SAVINGS = 'savings';

	const TYPE_BUSINESS = 'business';

	/**
	 * Account type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Checking account number.
	 *
	 * @var string
	 */
	public $number;

	/**
	 * The name of the account holder.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Checking account routing number
	 *
	 * @var string
	 */
	public $routing;

	/**
	 * An integer value between 1 and 12 representing the month of the expiration date.
	 *
	 * @var integer
	 */
	public $year;

	/**
	 * The CVV2 code for the credit card.
	 *
	 * @var string
	 */
	public $code;

}

?>