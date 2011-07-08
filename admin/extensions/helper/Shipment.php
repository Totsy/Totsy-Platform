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
		$upsBase = 'http://wwwapps.ups.com/WebTracking/processInputRequest?';
		$upsDetails = 'sort_by=3D=status&tracknums_displayed=3D1&TypeOfInquiryNumber=3DT&loc=3D=en_US&InquiryNumber1=';
		$upsDetails2 = '&track.x=3D0&track.y==3D0';
		$html = "";
		switch ($type) {
			case 'UPS':
			case 'NEXTDAY':
			case 'UPSGROUND':
			case 'SP':
				if (strlen($number) == 22) {
					$html = "<a href='http://www.ups-mi.net/packageID/PackageID.aspx?PID=$number' target='_blank' title='Tracking Info'>$number</a>";
				} else {
					$html = "<a href=".$upsBase.$upsDetails.$number.$upsDetails2." target='_blank' title='Tracking Info'>$number</a>";
				}
				break;
			default:
				break;
		}
		return $html;
	}
	
	public function linkNoHTML($number, array $options = array()) {
		$type = $options['type'];
		$upsBase = 'http://wwwapps.ups.com/WebTracking/processInputRequest?';
		$upsDetails = 'sort_by=3D=status&tracknums_displayed=3D1&TypeOfInquiryNumber=3DT&loc=3D=en_US&InquiryNumber1=';
		$upsDetails2 = '&track.x=3D0&track.y==3D0';
		$html = "";
		
		if (substr($number,0,2)=='1Z') { $type = "UPS"; }
		else if (is_numeric($number)) { $type="USPS"; }
		
		switch ($type) {
			case 'UPS':
			case 'NEXTDAY':
			case 'UPSGROUND':
			case 'SP':
				if (strlen($number) == 22) {
					$html = "http://www.ups-mi.net/packageID/PackageID.aspx?PID=$number";
				} else {
					$html = $upsBase.$upsDetails.$number.$upsDetails2;
				}
				break;
			case 'USPS':
					$html = "http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?strOrigTrackNum=".$number;
				break;
			default:
				break;
		}
		return $html;
	}
}
?>
