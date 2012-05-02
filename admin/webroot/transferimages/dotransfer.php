<?php
#validate skus 

$eventarray[] = '4f70ce841d5ecb5a7300004e';
$eventarray[] = '4f7ca2af1d5ecb8f48025d93';
$eventarray[] = '4f8c26661d5ecb7b6b000057';
$eventarray[] = '4f8ec4701d5ecbce1e000045';
$eventarray[] = '4f8f54ee1d5ecb2b54000033';
$eventarray[] = '4f984ccf1d5ecbf00d00003b';
$eventarray[] = '4f99e1311d5ecb1f2b0000a9';
$eventarray[] = '4f99e2e71d5ecbab2b00001e';
$eventarray[] = '4f99e4ae1d5ecbd32b000043';
$eventarray[] = '4f9aa7e71d5ecb7472000026';
$eventarray[] = '4f9aa91c1d5ecb147100003b';
$eventarray[] = '4f9acd1f1d5ecbc67e00007a';
$eventarray[] = '4f9ad6af1d5ecb1704000076';
$eventarray[] = '4f9ae0061d5ecb370700006d';
$eventarray[] = '4f9ae0211d5ecb590700005f';
$eventarray[] = '4f9ae2741d5ecb500700009e';
$eventarray[] = '4f9ae2c81d5ecbb00b000000';
$eventarray[] = '4f9ae7381d5ecb3f0a000086';
$eventarray[] = '4f9af9881d5ecb9613000030';
$eventarray[] = '4f9afd6d1d5ecb9713000098';
$eventarray[] = '4f9afe961d5ecb9c14000028';
$eventarray[] = '4f9afeef1d5ecb9b14000034';
$eventarray[] = '4f9b00151d5ecbaa14000040';
$eventarray[] = '4f9b046e1d5ecbaf16000102';
$eventarray[] = '4f9b05541d5ecbe61600005d';
$eventarray[] = '4f9b05f91d5ecbae160000fe';
$eventarray[] = '4f9b06081d5ecbcf16000098';
$eventarray[] = '4f9b07921d5ecb041900000b';
$eventarray[] = '4f9b08971d5ecb031900002a';
$eventarray[] = '4f9b08bb1d5ecbf618000032';
$eventarray[] = '4f9b093d1d5ecb2618000054';
$eventarray[] = '4f9b0c461d5ecb071a00001e';
$eventarray[] = '4f9b0d0c1d5ecbfd19000052';
$eventarray[] = '4f9b0d351d5ecb0b1a00004b';
$eventarray[] = '4f9b10831d5ecbff1b00002d';
$eventarray[] = '4f9b128c1d5ecb7c1c00001e';
$eventarray[] = '4f9b129c1d5ecb721c00003a';
$eventarray[] = '4f9b15191d5ecb811c000061';
$eventarray[] = '4f9b15b31d5ecb7f1c000070';
$eventarray[] = '4f9b3a0c1d5ecb552b00001d';
$eventarray[] = '4f9b3a8f1d5ecbe829000031';
$eventarray[] = '4f9b3b901d5ecbe829000058';
$eventarray[] = '4f9b3c2d1d5ecb532b000063';
$eventarray[] = '4f9b3ca11d5ecbd3290000ed';
$eventarray[] = '4f9b486f1d5ecba92e0000d2';
$eventarray[] = '4f9b49971d5ecbcf2e000070';
$eventarray[] = '4f9b4cc21d5ecbcd2e00009e';
$eventarray[] = '4f9b4db01d5ecbcd2e0000a1';
$eventarray[] = '4f9b4fd61d5ecbd12e00009a';
$eventarray[] = '4f9b509f1d5ecbd12e0000a0';
$eventarray[] = '4f9e99791d5ecb546a00000d';
$eventarray[] = '4f9ea3001d5ecba46d00002b';
$eventarray[] = '4f9ea7d71d5ecb9b6d00007a';
$eventarray[] = '4f9ea8041d5ecbc86d00006d';
$eventarray[] = '4f9ea8931d5ecb9b6d000083';
$eventarray[] = '4f9eadd81d5ecb7e70000023';
$eventarray[] = '4f9eafd71d5ecb4e7000e2d1';

$m = new Mongo();
$totsy = $m->totsy;
$items = $totsy->items;

$results = $items->find(array('event' => array('$in' => $eventarray)));



foreach($results as $item){
	$output = "log.html";
	
	$primary_image = $item['primary_image'];
	$zoom_image = $item['zoom_image'];
	
	system('wget http://www.totsy.com/image/'. $primary_image .'.jpg',$output);
	system('wget http://www.totsy.com/image/'. $zoom_image .'.jpg',$output);

}



?>