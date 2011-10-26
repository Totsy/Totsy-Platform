<?php

namespace admin\models;

class OrderShipped extends \lithium\data\Model {

	public static function collection() {
		$return = static::_connection();
		return $return->connection->{"orders.shipped"};
	}

	protected $_meta = array('source' => 'orders.shipped');

	/**
	 * The schema for orders.shipped is configured for both 3Linx and DC.
	 * The key position is very important as it directly maps to the column position
	 * of the DC ship file. 
	 */
	protected $_schema = array(
		"ShipDate" => array('type' => 'MongoDate', 'null' => false),
		"ShipDC" => array('type' => 'string', 'null' => false),
		"ShipMethod" => array('type' => 'string', 'null' => false),
		"OrderNum" => array('type' => 'string', 'null' => false),
		"BoxID" => array('type' => 'string', 'null' => false),
		"Tracking #" => array('type' => 'string', 'null' => false),
		"Cost" => array('type' => 'string', 'null' => false),
		"RecvDate" => array('type' => 'string', 'null' => false),
		"ClientID" => array('type' => 'string', 'null' => false),
		"DC" => array('type' => 'string', 'null' => false),
		"RushOrder (Y/N)" => array('type' => 'string', 'null' => false),
		"Qty" => array('type' => 'string', 'null' => false),
		"CompanyOrName" => array('type' => 'string', 'null' => false),
		"Blank1" => array('type' => 'string', 'null' => false),
		"SKU" => array('type' => 'string', 'null' => false),
		"Weight" => array('type' => 'string', 'null' => false),
		"ContactName" => array('type' => 'string', 'null' => false),
		"Blank2" => array('type' => 'string', 'null' => false),
		"Address1" => array('type' => 'string', 'null' => false),
		"Address2" => array('type' => 'string', 'null' => false),
		"City " => array('type' => 'string', 'null' => false),
		"StateOrProvince" => array('type' => 'string', 'null' => false),
		"Zip" => array('type' => 'string', 'null' => false),
		"Country" => array('type' => 'string', 'null' => false),
		"Email" => array('type' => 'string', 'null' => false),
		"Tel" => array('type' => 'string', 'null' => false),
		"Customer PO #" => array('type' => 'string', 'null' => false),
		"Pack Slip Comment" => array('type' => 'string', 'null' => false),
		"Special Packing Instructions for this Order" => array('type' => 'string', 'null' => false),
		"Ref1" => array('type' => 'string', 'null' => false),
		"Ref2" => array('type' => 'string', 'null' => false),
		"Ref3" => array('type' => 'string', 'null' => false),
		"Ref4" => array('type' => 'string', 'null' => false),
		"Ref5" => array('type' => 'string', 'null' => true),
		"Ref6" => array('type' => 'string', 'null' => true),
		"Ref7" => array('type' => 'string', 'null' => true),
		"Ref8" => array('type' => 'string', 'null' => true),
		"Ref9" => array('type' => 'string', 'null' => true),
		"Ref10" => array('type' => 'string', 'null' => true),
		"BillType (R/3P)" => array('type' => 'string', 'null' => true),
		"R / 3P Account Number" => array('type' => 'string', 'null' => true),
		"Billing CompanyName" => array('type' => 'string', 'null' => true),
		"Billing ContactName" => array('type' => 'string', 'null' => true),
		"Billing Address1" => array('type' => 'string', 'null' => true),
		"Billing Address2" => array('type' => 'string', 'null' => true),
		"Billing City" => array('type' => 'string', 'null' => true),
		"Billing State" => array('type' => 'string', 'null' => true),
		"Billing Zip" => array('type' => 'string', 'null' => true),
		"Billing Country" => array('type' => 'string', 'null' => true),
		"Billing Telephone" => array('type' => 'string', 'null' => true),
		"COD (Y/N)" => array('type' => 'string', 'null' => true),
		"Order COD Value" => array('type' => 'string', 'null' => true),
		"COD: Require Payment By Cashier's Check/Money Order (Y/N)" => array('type' => 'string', 'null' => true),
		"COD: Add Shipping Costs to COD Amount (Y/N)" => array('type' => 'string', 'null' => true),
		"hash" => array('type' => 'string', 'null' => false),
		"ItemId" => array('type' =>'MongoId', 'null' => true),
		"OrderId" => array('type' =>'MongoId', 'null' => true)
	);

}

?>