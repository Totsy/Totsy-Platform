<?php

namespace app\controllers;

//use app\controllers\BaseController;
use \app\models\Giftcard;
use li3_silverpop\extensions\Silverpop;

class GiftcardsController extends BaseController {

	public function index() {
		$giftcards = Giftcard::all();
		return compact('giftcards');
	}

	public function view() {
		$giftcard = Giftcard::first($this->request->id);
		return compact('giftcard');
	}

	/*
    attempt to create an image containing the error message given.
    if this works, the image is sent to the browser. if not, an error
    is logged, and passed back to the browser as a 500 code instead.
*/
	public function fatal_error($message)
	{
		// send an image
		if(function_exists('ImageCreate'))
		{
			$width = imagefontwidth(5) * strlen($message) + 10 ;
			$height = imagefontheight(5) + 10 ;
			if($image = imagecreate($width,$height))
			{
				$background = imagecolorallocate($image,255,255,255) ;
				$text_color = imagecolorallocate($image,0,0,0) ;
				imagestring($image,5,5,5,$message,$text_color) ;
				header('Content-type: image/png') ;
				imagepng($image) ;
				imagedestroy($image) ;
				exit ;
			}
		}

		// send 500 code
		header("HTTP/1.0 500 Internal Server Error") ;
		print($message) ;
		exit ;
	}


	/*
    decode an HTML hex-code into an array of R,G, and B values.
    accepts these formats: (case insensitive) #ffffff, ffffff, #fff, fff
*/
	public function hex_to_rgb($hex) {
		// remove '#'
		if(substr($hex,0,1) == '#')
			$hex = substr($hex,1) ;

		// expand short form ('fff') color to long form ('ffffff')
		if(strlen($hex) == 3) {
			$hex = substr($hex,0,1) . substr($hex,0,1) .
				substr($hex,1,1) . substr($hex,1,1) .
				substr($hex,2,1) . substr($hex,2,1) ;
		}

		if(strlen($hex) != 6)
			fatal_error('Error: Invalid color "'.$hex.'"') ;

		// convert from hexidecimal number systems
		$rgb['red'] = hexdec(substr($hex,0,2)) ;
		$rgb['green'] = hexdec(substr($hex,2,2)) ;
		$rgb['blue'] = hexdec(substr($hex,4,2)) ;

		return $rgb ;
	}

	public function preview() {
		//1 - Accept: to, from, recpient's name, background image, message,
		//2 - Call GD library functions

		// customizable variables

		//need to find where this exists on the getcwdserver
		$font_file      = '/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans.ttf';

		//probably 12px?
		$font_size = 9 ; // font size in pts

		//leave as white
		$font_color = '#FFF';
		$text = strip_tags($_POST['gc_message']);

		$text = wordwrap($text, 35, "\n");
			
		//background image chosen by user from a list provided by Totsy
		//this will be dynamic
		$image_file = '/var/www/evan.totsy.com/app/webroot/img/giftcard_image.png';

		// x and y for the bottom right of the text
		// so it expands like right aligned text
		$x_finalpos     = 380;
		$y_finalpos     = 50;

		// trust me for now...in PNG out PNG
		$mime_type          = 'image/png' ;
		$extension          = '.png' ;
		$s_end_buffer_size  = 4096 ;

		// check for GD support
		if(!function_exists('ImageCreate'))
			$this->fatal_error('Error: Server does not support PHP image generation') ;

		// check font availability;
		if(!is_readable($font_file)) {
			$this->fatal_error('Error: The server is missing the specified font.') ;
		}

		// create and measure the text
		//some formatting for the hex font color thats all
		$font_rgb = $this->hex_to_rgb($font_color) ;

		//why did they spell it this way? hmmm, either its documeneted
		$box = imageftbbox($font_size,0,$font_file,$text) ;

		$text_width = abs($box[2]-$box[0]);
		$text_height = abs($box[5]-$box[3]);

		$image =  imagecreatefrompng($image_file);

		if(!$image || !$box)
		{
			$this->fatal_error('Error: The server could not create this image.') ;
		}

		// allocate colors and measure final text position
		$font_color = imagecolorallocate($image,$font_rgb['red'],$font_rgb['green'],$font_rgb['blue']) ;

		$image_width = imagesx($image);

		$put_text_x = $image_width - $text_width - ($image_width - $x_finalpos);
		$put_text_y = $y_finalpos;

		// Write the text
		imagefttext($image, $font_size, 0, $put_text_x,  $put_text_y, $font_color, $font_file, $text, array('linespacing'=>1.5));

		$cache_folder = "/var/www/evan.totsy.com/app/webroot/temp";

		$hash = md5(date('Y-m-s'));
		$cache_filename = $cache_folder . '/' . $hash . $extension;

		//Is already it cached?
		if($file = @fopen($cache_filename,'rb')) {
			header('Content-type: ' . $mime_type);

			while(!feof($file)) {
				print(($buffer = fread($file,4096)));
			}

			fclose($file);
			exit;
		} else {
			//Generage a new image and save it
			imagepng($image,$cache_filename); //Saves it to the given folder

			//Send image
			//header('Content-type: ' . $mime_type);
			//imagepng($image);
		}

		$data = Array (
			'order' => Array('to'=> $_REQUEST['to'],
				'value'=> $_REQUEST['value'],
				'ocassion'=> $_REQUEST['ocassions'],
				'img'=> 'http://evan.totsy.com/temp/' . $hash . $extension
			),
			'email' => $_POST['recipients_email'],
			'shipDate' => date("Y-m-s")
		);
		
		//save to mongo here
		//1 - store image path, expiration_date, user_id of person who purchased
		
		if(file_exists($cache_filename)){
			Silverpop::send('giftCard', $data);
		}
		//if (array_key_exists('freeshipping', $service) && $service['freeshipping'] === 'eligible') {
		//Silverpop::send('nextPurchase', $data);
		//}

		//imagedestroy($image) ;
		exit ;
	}

	public function add() {
		$giftcard = Giftcard::create();

		if (($this->request->data) && $giftcard->save($this->request->data)) {
			$this->redirect(array('Giftcards::view', 'args' => array($giftcard->id)));
		}
		return compact('giftcard');
	}

	public function edit() {
		$giftcard = Giftcard::find($this->request->id);

		if (!$giftcard) {
			$this->redirect('Giftcards::index');
		}
		if (($this->request->data) && $giftcard->save($this->request->data)) {
			$this->redirect(array('Giftcards::view', 'args' => array($giftcard->id)));
		}
		return compact('giftcard');
	}
}

?>