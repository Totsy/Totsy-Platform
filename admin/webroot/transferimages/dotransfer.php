<?php
#validate skus 

$imagesarray = "/image/4f9ebdca1d5ecb9e77000047.jpg";
$imagesarray = "/image/4f9ebdca1d5ecb9e77000047.jpg";
$imagesarray = "/image/4f9ebdca1d5ecb9e77000047.jpg";
$imagesarray = "/image/4f9ebeab1d5ecb90770008a7.jpg";
$imagesarray = "/image/4f9ebeab1d5ecb90770008a7.jpg";
$imagesarray = "/image/4f9ebeab1d5ecb90770008a7.jpg";
$imagesarray = "/image/4f9ebeef1d5ecbe077000050.jpg";
$imagesarray = "/image/4f9ebeef1d5ecbe077000050.jpg";
$imagesarray = "/image/4f9ebeef1d5ecbe077000050.jpg";
$imagesarray = "/image/4f9ebf5d1d5ecb0d7800004b.jpg";
$imagesarray = "/image/4f9ebf5d1d5ecb0d7800004b.jpg";
$imagesarray = "/image/4f9ebf5d1d5ecb0d7800004b.jpg";
$imagesarray = "/image/4f9ebf921d5ecb877700007c.jpg";
$imagesarray = "/image/4f9ebf921d5ecb877700007c.jpg";
$imagesarray = "/image/4f9ebf921d5ecb877700007c.jpg";
$imagesarray = "/image/4f9ebdca1d5ecb9e77000047.jpg";
$imagesarray = "/image/4f9ebdca1d5ecb9e77000047.jpg";
$imagesarray = "/image/4f9ebdca1d5ecb9e77000047.jpg";
$imagesarray = "/image/4f9ebff01d5ecb157800005d.jpg";
$imagesarray = "/image/4f9ebff01d5ecb157800005d.jpg";
$imagesarray = "/image/4f9ebff01d5ecb157800005d.jpg";
$imagesarray = "/image/4f9ec1381d5ecb007a00001c.jpg";
$imagesarray = "/image/4f9ec1381d5ecb007a00001c.jpg";
$imagesarray = "/image/4f9ec1381d5ecb007a00001c.jpg";
$imagesarray = "/image/4f9ebf5d1d5ecb0d7800004b.jpg";
$imagesarray = "/image/4f9ebf5d1d5ecb0d7800004b.jpg";
$imagesarray = "/image/4f9ebf5d1d5ecb0d7800004b.jpg";
$imagesarray = "/image/4f9ec0751d5ecb027a000001.jpg";
$imagesarray = "/image/4f9ec0751d5ecb027a000001.jpg";
$imagesarray = "/image/4f9ec0751d5ecb027a000001.jpg";
$imagesarray = "/image/4f9ebdca1d5ecb9e77000047.jpg";
$imagesarray = "/image/4f9ebdca1d5ecb9e77000047.jpg";
$imagesarray = "/image/4f9ebdca1d5ecb9e77000047.jpg";
$imagesarray = "/image/4f9ebdca1d5ecb9e77000047.jpg";
$imagesarray = "/image/4f9ebff01d5ecb157800005d.jpg";
$imagesarray = "/image/4f9ebff01d5ecb157800005d.jpg";
$imagesarray = "/image/4f9ebff01d5ecb157800005d.jpg";
$imagesarray = "/image/4f9ebff01d5ecb157800005d.jpg";
$imagesarray = "/image/4f9ec1381d5ecb007a00001c.jpg";
$imagesarray = "/image/4f9ec1381d5ecb007a00001c.jpg";
$imagesarray = "/image/4f9ec1381d5ecb007a00001c.jpg";
$imagesarray = "/image/4f9ec1381d5ecb007a00001c.jpg";
$imagesarray = "/image/4f9ebf5d1d5ecb0d7800004b.jpg";
$imagesarray = "/image/4f9ebf5d1d5ecb0d7800004b.jpg";
$imagesarray = "/image/4f9ebf5d1d5ecb0d7800004b.jpg";
$imagesarray = "/image/4f9ebf5d1d5ecb0d7800004b.jpg";
$imagesarray = "/image/4f9ec0751d5ecb027a000001.jpg";
$imagesarray = "/image/4f9ec0751d5ecb027a000001.jpg";
$imagesarray = "/image/4f9ec0751d5ecb027a000001.jpg";
$imagesarray = "/image/4f9ec0751d5ecb027a000001.jpg";

foreach($imagesarray as $image){
	$output = "log.html";
	
	//system('wget http://www.totsy.com/image/'. $primary_image .'.jpg',$output);
	system('wget http://www.totsy.com'. $image ,$output);

}



?>