<?php
/**
 * 
 * @author Swetal.Desai
 * @package AvaCert
 */
class InitiateExemptCertResult extends BaseResult {
  private $TrackingCode; // string

  public function setTrackingCode($value){$this->TrackingCode=$value;} // string
  public function getTrackingCode(){return $this->TrackingCode;} // string

}

?>
