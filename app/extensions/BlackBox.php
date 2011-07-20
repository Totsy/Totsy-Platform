<?php 

namespace app\extensions;

class BlackBox {
	
	public static function __callStatic($name,$args){
		$fh = fopen(LITHIUM_APP_PATH.'/resources/tmp/logs/'.$name.'.log','a');
		fwrite($fh,date('[ Y-m-d H:i:s ] ').$args[0]."\n");
		fclose($fh);
	}
}
?>