<?php
/**
 * 
 * @author Swetal.Desai
 * @package AvaCert
 */
class InitiateExemptCertRequest {
  private $Customer; // Customer
  private $LocationCode; // string
  private $CustomMessage; // string
  private $CommunicationMode; // CommunicationMode
  
  public function setCustomer($value){$this->Customer=$value;} // Customer
  public function getCustomer(){return $this->Customer;} // Customer

  public function setLocationCode($value){$this->LocationCode=$value;} // string
  public function getLocationCode(){return $this->LocationCode;} // string

  public function setCustomMessage($value){$this->CustomMessage=$value;} // string
  public function getCustomMessage(){return $this->CustomMessage;} // string

  public function setCommunicationMode($value){$this->CommunicationMode=$value;} // CommunicationMode
  public function getCommunicationMode(){return $this->CommunicationMode;} // CommunicationMode
  
 }

?>
