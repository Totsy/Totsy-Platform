<?php
$itemscount = count($eventItems);
$outout="";
//foreach($thisitem as $itemkey => $itemvalue){
for($i=0;$i<$itemscount;$i++){
	$vendorstyle = $eventItems[$i]['vendor_style'];
	$vendor = $eventItems[$i]['vendor'];


	$sale_whol = $eventItems[$i]['sale_whol'];
	$orig_whol = $eventItems[$i]['orig_whol'];
	$sale_retail = $eventItems[$i]['sale_retail'];
	$msrp = $eventItems[$i]['msrp'];


	$color = $eventItems[$i]['color'];

	$primary_image = $eventItems[$i]['primary_image'];
	$zoom_image = $eventItems[$i]['zoom_image'];


	$description = $eventItems[$i]['description'];
	$eventid = $eventItems[$i]['event'][0];

	//quantity size=>quant
	$details = $eventItems[$i]['details'];

	//sold amount size=>quant
	$sale_details = $eventItems[$i]['sale_details'];

	//quantity size=>quant (original)
	$details_original = $eventItems[$i]['details_original'];

	//skus i => sku
	$skus = $eventItems[$i]['skus'];
	
	//details size=>sku
	$sku_details = $eventItems[$i]['sku_details'];
	
	//details size=>sku
	$skus = $eventItems[$i]['skus'];

	//count
	$skucount = count($eventItems[$i]['sku_details']);
	
	$j=0;

	//for($i=0;$i<$skucount;$i++){
	foreach($details as $thiskeyvalue => $thisitemvalue){
	
		$sold_quantity = $sale_details[$thiskeyvalue]['sale_count'];
		$sku = $sku_details[$thiskeyvalue];
	
		if($sku_details[$thiskeyvalue]){
			$sku = $sku_details[$thiskeyvalue];
		}
		else{
			$sku = "<font color=ff0000>NO SKU ALERT!!!!</font>";
		}
	
		$output .= "<tr>";
		$output .= "<td>$event->name</td>";
		$output .= "<td>$vendor</td>";
		$output .= "<td>$vendorstyle</td>";
		$output .= "<td>$description</td>";
		$output .= "<td>$color</td>";
		$output .= "<td>$sale_whol</td>";
		$output .= "<td>$sku</td>";
		$output .= "<td>$thiskeyvalue</td>";
		$output .= "<td>$details[$thiskeyvalue]</td>";
		$output .= "<td>$sold_quantity</td>";
		$output .= "<td>$details_original[$thiskeyvalue]</td>";
		$output .= "<td>/image/".$primary_image.".jpg</td>";
		$output .= "<td>/image/".$zoom_image.".jpg</td>";
		$output .= "</tr>";
		$j++;
	}

}
?>

<table width=100% style="font-size:12px;">
<tr>
<td>Event</td>
<td>Vendor</td>
<td>vendor_style</td>
<td>name</td>
<td>color</td>
<td>sale_wholesale</td>
<td>original_wholesale</td>
<td>special_price</td>
<td>price</td>
<td>sku</td>
<td>size</td>
<td>qty</td>
<td>Sold</td>
<td>Original</td>
<td>image</td>
<td>media_gallery</td>
</tr>
<!-- 
-->
<?php echo $output; ?>
</table>
