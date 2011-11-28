<?php

namespace admin\models;

class ProcessedOrder extends Base {

	public $validates = array();

	protected $_meta = array('source' => 'orders.processed');

	/**
	 * The order file column heading.
	 *
	 * @var	array
	 */
	public static $_fileHeading = array(
		'Date' => null,
		'ClientId' => 'TOT',
		'DC' => 'ALN',
		'ShipMethod' => null,
		'RushOrder (Y/N)' => null,
		'OrderNum' => null,
		'SKU' => null,
		'Qty' => null,
		'CompanyOrName' => null,
		'ContactName' => null,
		'Address1' => null,
		'Address2' => null,
		'City' => null,
		'StateOrProvince' => null,
		'Zip' => null,
		'Country' => null,
		'Email' => null,
		'Tel' => null,
		'Customer PO #' => null,
		'Pack Slip Comment' => null,
		'Special Packing Instructions' => null,
		'Ref1' => null,
		'Ref2' => null,
		'Ref3' => null,
		'Ref4' => null,
		'Order Creation Date' => null,
		'Promised Ship-by Date' => null,
		'Ref5' => null,
		'Ref6' => null,
		'Ref7' => null,
		'Ref8' => null,
		'Ref9' => null,
		'Ref10' => null,
		'BillType (R/3P)' => null,
		'R3PAccountNum' => null,
		'Billing CompanyName' => null,
		'Billing Address1' => null,
		'Billing Address2' => null,
		'Billing City' => null,
		'Billing State' => null,
		'Billing Country' => null,
		'Billing Telephone' => null,
		'COD (Y/N)' => null,
		'Order COD Value' => null,
		'COD: Require Payment By Cashier\'s Check/Money Order (Y/N)' => null,
		'COD: Add Shipping Costs to COD Amount (Y/N)' => null
	);

	/**
	 * The product file report column heading and default values.
	 *
	 * @var array
	 */
	public static $_productHeading = array(
		'ClientID' => 'TOT',
		'SKU' => null,
		'Description' => null,
		'WhsInsValue (Cost)' => null,
		'ShipInsValue' => null,
		'Expiration_Date' => null,
		'UPC' => null,
		'Description for Customs' => null,
		'HSC Code' => null,
		'Class for LTL' => null,
		'Country of Origin' => 'USA',
		'Velocity' => 'B',
		'Ref1' => null,
		'Ref2' => null,
		'Ref3' => null,
		'Ref4' => null,
		'Ref5' => null,
		'UOM1' => 'EA',
		'UOM1_Qty' => 1,
		'UOM1_Weight' => '1.00',
		'UOM1_Length' => '1.00',
		'UOM1_Width' => '1.00',
		'UOM1_Height' => '1.00',
		'UOM1_Cube' => '1.00',
		'Style' => null
	);

	/**
	 * The purchase order column headings.
	 *
	 * @var array
	 */
	public static $_purchaseHeading = array(
		'ClientID' => 'TOT',
		'ShipToDC' => null,
		'ShipMethod' => null,
		'Tracking #' => null,
		'Supplier' => null,
		'PO # / RMA #' => null,
		'SKU' => null,
		'Qty' => null,
		'Vendor Style' => null,
		'Vendor Name' => null,
		'Item Color' => null,
		'Item Size' => null,
		'Item Description' => null,
		'Order Creation Date' => null,
		'Promised Ship-by Date' => null,
		'Event Name' => null,
		'Event End Date' => null,
		'WhsInsValue (Cost)' => null,
		'ShipInsValue' => null,
		'Description for Customs' => null,
		'Ref1' => null,
		'Ref2' => null,
		'Ref3' => null,
		'Country of Origin' => 'USA',
		'Velocity' => 'B',
		'UOM1' => 'EA',
		'UOM1_Qty' => 1,
		'UOM1_Weight' => '1.00',
		'UOM1_Length' => '1.00',
		'UOM1_Width' => '1.00',
		'UOM1_Height' => '1.00',
		'UOM1_Cube' => '1.00',
	);
}
?>