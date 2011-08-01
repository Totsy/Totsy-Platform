<?php
/**
 * 
 * @author Swetal.Desai
 * @package AvaCert
 */
class CommunicationMode {
  public static $Email = 'Email';
  public static $Mail = 'Mail';
  public static $Fax = 'Fax';
  
	public static function Values()
	{
		return array(
			CommunicationMode::$Email,
			CommunicationMode::$Mail,
			CommunicationMode::$Fax
					
		);
	}

}

?>
