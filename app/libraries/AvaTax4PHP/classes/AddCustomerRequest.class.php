<?php
/**
 * 
 * @author Swetal.Desai
 * @package AvaCert
 */
class AddCustomerRequest {
  private $Customer; // Customer

  /**
   * Sets the customer value for this AddCustomerRequest.
   * 
   * @param Customer $value    
   */
  public function setCustomer($value){$this->Customer=$value;} // Customer
  
  /**
   * Gets the customer value for this AddCustomerRequest.
   * @return Customer
   */
  public function getCustomer(){return $this->Customer;} // Customer

}

?>
