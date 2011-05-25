<?php
namespace admin\extensions\helper;

/**
 * The 'Shipment' class creates a url based on shipping method.
 *
 * Each shipping partner has a static url which usually wraps an identifier. The shipping
 * method provided in the option array will use the matching link wrapper.
 *
 * Usage:
 *
 *{{{
 * 	$options = array('type' => 'ups')
 *  <?=$this->shipment->link('Z1231423SDF234234, $options);?>
 *}}}
 *
 */
class Shipment extends \lithium\template\Helper {

	public function link($number, array $options = array()) {

		$type = $options['type'];
		$link = null;
		switch ($type) {
			case 'UPS':
			case 'NEXTDAY':
			case 'UPSGROUND':
			case 'SP':
			case 'UPS':
			case 'ups':
				
				if (strlen($number) == 22) {
					$html = "<a href='http://www.ups-mi.net/packageID/PackageID.aspx?PID=$number' target='_blank' title='Tracking Info'>$number</a>";
				} else {
					$html = "<a href=".$upsBase.$upsDetails.$number.$upsDetails2." target='_blank' title='Tracking Info'>$number</a>";
				}
				
				break;
			default:
				break;
		}
		return $link;
	}
}