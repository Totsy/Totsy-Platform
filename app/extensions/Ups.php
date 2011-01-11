<?php

namespace app\extensions;

use lithium\net\http\Service;

class Ups {

	public static function shippingOptions($domestic = false) {
		$default = array(
			'1DM' == 'Next Day Air Early AM',
			'1DA' == 'Next Day Air',
			'1DP' == 'Next Day Air Saver',
			'2DM' == '2nd Day Air Early AM',
			'2DA' == '2nd Day Air',
			'3DS' == '3 Day Select',
			'GND' == 'Ground'
		);
		$intl = array(
			'STD' == 'Canada Standard',
			'XPR' == 'Worldwide Express',
			'XDM' == 'Worldwide Express Plus',
			'XPD' == 'Worldwide Expedited',
		);
		return $domestic ? $default : $default + $intl;
	}

	/**
	 * 1DM == Next Day Air Early AM
	 * 1DA == Next Day Air
	 * 1DP == Next Day Air Saver
	 * 2DM == 2nd Day Air Early AM
	 * 2DA == 2nd Day Air
	 * 3DS == 3 Day Select
	 * GND == Ground
	 * STD == Canada Standard
	 * XPR == Worldwide Express
	 * XDM == Worldwide Express Plus
	 * XPD == Worldwide Expedited
	 *
	 * @param string $prod
	 * @return void
	 */
	public function product($prod) {
		$this->upsProductCode = $prod;
	}

	public function origin($zip) {
		$this->originPostalCode = $zip;
		$this->originCountryCode = 'US';
	}

    function dest($zip) {
		$this->destPostalCode = $zip;
		$this->destCountryCode = 'US';
	}

	function rate($foo) {
		switch($foo) {
			case "RDP":
				$this->rateCode = "Regular+Daily+Pickup";
			break;
			case "OCA":
				$this->rateCode = "On+Call+Air";
			break;
			case "OTP":
				$this->rateCode = "One+Time+Pickup";
			break;
			case "LC":
				$this->rateCode = "Letter+Center";
			break;
			case "CC":
				$this->rateCode = "Customer+Counter";
			break;
		}
	}

    public function container($foo) {
		switch($foo) {
			case "CP": // Customer Packaging
				$this->containerCode = "00";
			break;
			case "ULE": // UPS Letter Envelope
				$this->containerCode = "01";
			break;
			case "UT": // UPS Tube
				$this->containerCode = "03";
			break;
			case "UEB": // UPS Express Box
				$this->containerCode = "21";
			break;
			case "UW25": // UPS Worldwide 25 kilo
				$this->containerCode = "24";
			break;
			case "UW10": // UPS Worldwide 10 kilo
				$this->containerCode = "25";
			break;
		}
	}

	function weight($foo) {
		$this->packageWeight = $foo ?: 1;
	}

    function rescom($foo) {
		switch($foo){
			case "RES": // Residential Address
				$this->resComCode = "1";
			break;
			case "COM": // Commercial Address
				$this->resComCode = "2";
			break;
		}
	}

	public static function estimate(array $options) {
		$class = get_called_class();
		$estimator = new $class();

		foreach ($options as $key => $val) {
			$estimator->{$key}($val);
		}
		return $estimator->quote();
	}

	public function quote() {
		$upsAction = "3"; // You want 3. Don't change unless you are sure.
		$endpoint = "/using/services/rave/qcostcgi.cgi?";
		$query = array(
			'accept_UPS_license_agreement' => 'yes',
			'10_action' => $upsAction,
			'13_product' => $this->upsProductCode,
			'14_origCountry' => $this->originCountryCode,
			'15_origPostal' => $this->originPostalCode,
			'19_destPostal' => $this->destPostalCode,
			'22_destCountry' => $this->destCountryCode,
			'23_weight' => $this->packageWeight,
			'47_rateChart' => $this->rateCode,
			'48_container' => $this->containerCode,
			'49_residential' => $this->resComCode
		);
		$url = $endpoint . http_build_query($query);

		$service = new Service(array(
			'socket' => 'Context',
			'host' => 'www.ups.com'
		));

		if (!$result = $service->get($url)) {
			return null;
		}

		$result = explode("%", $result);
		$errcode = substr($result[0], -1);

		switch($errcode) {
			case 3:
				$returnval = $result[8];
			break;
			case 4:
				$returnval = $result[8];
			break;
			case 5:
				$returnval = $result[1];
			break;
			case 6:
				$returnval = $result[1];
			break;
		}
		return $returnval ?: null;
	}
}


