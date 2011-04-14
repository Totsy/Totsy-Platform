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
				$upsBase = 'http://wwwapps.ups.com/WebTracking/processInputRequest?sort_by=3D=status&';
				$upsDetails = 'tracknums_displayed=3D1&TypeOfInquiryNumber=3DT&loc=3D=en_US&InquiryNumber1=';
				$upsDetails2 = '&track.x=3D0&track.y==3D0';
				$url = $upsBase.$upsDetails.$number.$upsDetails2;
				$link = $this->_context->html->link($number, $url, array('target' => '_blank'));
				break;
			default:
				break;
		}
		return $link;
	}
}