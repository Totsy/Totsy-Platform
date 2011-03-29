<?php

namespace admin\models;



class Promocode extends Base {

    protected $_meta = array('source' => 'promocodes');

    public static function setToBool( $var ) {
    	if( $var == '1' || $var == 'on' ){
			return true;
		}else{
			return false;
		}
    }

}


?>
