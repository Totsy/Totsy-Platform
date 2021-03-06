<?php
namespace app\extensions\helper;

class Shipment extends \lithium\template\Helper{

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
}