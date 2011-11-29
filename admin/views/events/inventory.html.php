<?php ini_set("display_erros", 0); ?>
<?php use admin\models\Event; ?>


<?php

$alleventnames = array('Duckies!','Sequin Tops by Verve','Sweet Heart Rose Dollie & Me','Madness Jr. Sport Shoes','Toddler Footwear Event','Gift Ideas by RXB','Jefferies Tights','Envy Womens Footwear','Larsen Gray Top Event','Womens Apparel by Cyrus','Favorite Character Tees','Under 10 Infant Footwear','Carters Footwear','10 Womens Footwear','15 Womens Fashion Boots','Under 30 Cold Weather Boots','Primitives by Kathy Holiday','The Jay Companies','e.l.f','Girls Legging Sets Under 14.00 ','Youngland Dress Extravaganza','Styles By Joseph A ','Tops By Isabella Rodriguez');
$eventnamearray['4ec52d2a12d4c97a7f000161'] = "Duckies! ";
$eventnamearray['4ec52fcd12d4c95d04000003'] = "Sequin Tops by Verve";
$eventnamearray['4ec59636943e836b7d00000c'] = "Sweet Heart Rose Dollie & Me";
$eventnamearray['4ec74642538926d607000004'] = "Madness Jr. Sport Shoes";
$eventnamearray['4ec3dfea12d4c94664000011'] = "Toddler Footwear Event";
$eventnamearray['4ec696e712d4c9892800003f'] = "Gift Ideas by RXB";
$eventnamearray['4ec72a4b943e83a125000081'] = "Jefferies Tights";
$eventnamearray['4eca83de12d4c92a290000ae'] = "Envy Womens Footwear";
$eventnamearray['4eca878e538926063d000199'] = "Larsen Gray Top Event";
$eventnamearray['4ec7212a12d4c94735000049'] = "Womens Apparel by Cyrus";
$eventnamearray['4eca808512d4c93029000078'] = "Favorite Character Tees";
$eventnamearray['4ecbb8b3538926c07e000006'] = "Under 10 Infant Footwear";
$eventnamearray['4ecab607943e83d339000066'] = "Carters Footwear";
$eventnamearray['4ecab8d75389262e470001d7'] = "10 Womens Footwear";
$eventnamearray['4ecbe597943e839259000010'] = "15 Womens Fashion Boots";
$eventnamearray['4ecac69412d4c90f2d000227'] = "Under 30 Cold Weather Boots";
$eventnamearray['4ecc202512d4c94e5400001d'] = "Primitives by Kathy Holiday";
$eventnamearray['4ecbfa5b5389263c0f000003'] = "The Jay Companies";
$eventnamearray['4ecbddfd5389267309000000'] = "e.l.f";
$eventnamearray['4ecad2ce538926e74e000260'] = "Girls Legging Sets Under 14.00 ";
$eventnamearray['4ecc2f00943e832761000024'] = "Youngland Dress Extravaganza";
$eventnamearray['4ecc0c48943e834a5e000000'] = "Styles By Joseph A ";
$eventnamearray['4ecadeb612d4c9a93000025d'] = "Tops By Isabella Rodriguez";



$eventnamearray['4ec45991538926fd7400003a'] = "Hartstrings Kids";
$eventnamearray['4ec45c4412d4c90c6c000150'] = "Hartstrings Baby";


$eventnamearray['4ecaea12943e83663d0000ee'] = "Cupio";
$eventnamearray['4ed3ba7b538926d15c00000a'] = "Cable and Gauge Top Blow Out";


$fieldstoshow = array('_id','vendor','vendor_style','sku_details','color','size', 'sale_retail');
$count=0;
	$itemscount = count($eventItems);
	//foreach($thisitem as $itemkey => $itemvalue){
	for($i=0;$i<$itemscount;$i++){
		$vendorstyle = $eventItems[$i]['vendor_style'];
		$vendor = $eventItems[$i]['vendor'];
		$sale_whol = $eventItems[$i]['sale_whol'];
		$color = $eventItems[$i]['color'];
		$description = $eventItems[$i]['description'];
		$eventid = $eventItems[$i]['event'][0];

		//quantity size=>quant
		$details = $eventItems[$i]['details'];

		//quantity size=>quant (original)
		$details_original = $eventItems[$i]['details_original'];

		//skus i => sku
		$skus = $eventItems[$i]['skus'];
		
		//details size=>sku
		$sku_details = $eventItems[$i]['sku_details'];



		$skucount = count($eventItems[$i]['sku_details']);
		
		$j=0;

		foreach($sku_details as $thiskeyvalue => $thisitemvalue){
		//for($i=0;$i<$skucount;$i++){
			$output .= "<tr>";
			$output .= "<td>$event->name</td>";
			$output .= "<td>$vendorstyle</td>";
			$output .= "<td>$vendor</td>";
			$output .= "<td>$description</td>";
			$output .= "<td>$color</td>";
			$output .= "<td>$sale_whol</td>";
			$output .= "<td>$thisitemvalue</td>";
			$output .= "<td>$thiskeyvalue</td>";
			$output .= "<td>$details[$thiskeyvalue]</td>";
			$output .= "<td>$details_original[$thiskeyvalue]</td>";
			$output .= "</tr>";
			
			$j++;
		}

	}

	$count++;


?>


<table width=100% style="font-size:12px;">
<tr>
<td>event</td>
<td>vendor_style</td>
<td>vendor</td>
<td>description</td>
<td>color</td>
<td>sale_whol</td>
<td>SKU</td>
<td>Size</td>
<td>Quant</td>
<td>Quant (original)</td>
</tr>
<!-- 
-->
<?=$output?>


</table>
