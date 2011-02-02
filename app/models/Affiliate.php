<?php

namespace app\models;

class Affiliate extends Base {

    protected $_meta = array('source'=> 'affiliates');
	public $validates = array();

	public static function getPixels($invited_by) {
	  if(!($invited_by)) return null;

         $url = $_SERVER['REQUEST_URI'];
        if(strpos($url, '&')) {
            $url = substr($url,0,strpos($url, '&'));
        }
        if(preg_match('(/orders/view/)',$url)) {
            $url = '/orders/view';
        }

		$options = array('conditions' => array(
		                        'invitation_codes' => $invited_by,
								'pixel' => array(
									'$elemMatch'=>array(
										'page' =>$url,
										'enable' => true
								))), 'fields'=>array('pixel.pixel' => 1, 'pixel.page' => 1));
		$pixels = Affiliate::find('all', $options );
		$pixels= $pixels->data();
		$pixel = NULL;

		foreach($pixels as $data) {
			foreach($data['pixel'] as $index) {
                if(in_array($url, $index['page'])) {
                    if($invited_by == 'w4'){
                        $transid = 'totsy'. static::randomString();
                        $pixel .= '<br>\n'. str_replace('$', $transid, $index['pixel']);
                    }else{
				        $pixel .= '<br>\n'. $index['pixel']. '<br>';
				    }
				}
			}
		}

		return $pixel;
	}

	protected static function randomString($length = 8, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
		$chars_length = (strlen($chars) - 1);
		$string = $chars{rand(0, $chars_length)};
		for ($i = 1; $i < $length; $i = strlen($string)) {
			$r = $chars{rand(0, $chars_length)};
			if ($r != $string{$i - 1}) $string .=  $r;
		}
		return $string;
	}

}

?>