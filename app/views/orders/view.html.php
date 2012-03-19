<?php $this->title("Order Confirmation"); ?>
<?php
	$totalQty = 0;
	$brandNew = ($order->date_created->sec > (time() - 10)) ? true : false;
	$new = ($order->date_created->sec > (time() - 120)) ? true : false;

?>
<div class="grid_16">
	<h2 class="page-title gray">My Orders</h2>
	<hr />
</div>
<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'myAccountNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
	<div class="roundy grey_inside">
		<?php echo $this->html->image('being_green/carbonzero.gif', array('style' => 'margin-right: 10px; margin-bottom:20px; float:left;')); ?>
		<p>A tree was planted with your first order. It is watered with every additional order so it can grow big and strong to help our earth!<br>
			<strong style="color:#E00000;font-weight:normal"></strong><br />
			<?php echo $this->html->link('Learn how every purchase helps', array('Pages::being_green')); ?>
		</p>
	</div>
</div>
<?php if ($order): ?>
<div class="grid_11 omega roundy grey_inside b_side">
	<h2 class="page-title gray">My Orders</h2>
	<hr />
	<table class="cart-table" cellspacing="0" cellpadding="0" border="0" width="695">
		<tr>
			<td colspan="4">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td valign="top">
						<div style="display:none;">
							<div class="rounded" style="color:#009900; margin:0px 10px 0px 0px; float: left; display:block; background:#ebffeb; border:1px solid #ddd; width:180px; text-align:center; padding:20px;">Shipping / Billing Info</div>
							<div id="arrow-right">
							  <div id="arrow-right-1"></div>
							  <div id="arrow-right-2"></div>
							</div><!--arrow-right-->
							<div class="rounded" style="color:#009900; margin:0px 10px 0px 0px; float: left; display:block; background:#ebffeb; border:1px solid #ddd; width:180px; padding:20px; text-align: center;">Payment</div>
							<div id="arrow-right">
								<div id="arrow-right-1"></div>
								<div id="arrow-right-2"></div>
							</div><!--arrow-right-->
							<div class="rounded" style="color:#009900; margin:0px 0px 0px 0px; float:left; display:block; background:#ebffeb; border:1px solid #ddd; width:188px; padding:20px; text-align:center;">Confirmation</div>
						</div>
						<div style="background:#f7f7f7; padding:10px; border:1px solid #ddd;">
							<h2>Thank you! Your order has been successfully placed! <span style="float:right;">Order #<?php echo $order->order_id;?></span>
							<br /><span style="float:right;">Estimated Ship Date: <?php echo date('m-d-Y', $shipDate) ?></span><br />
							</h2>
						</div>
						 
						<div style="clear:both;"></div>
						</td>
					</tr>
					<tr>
						<td valign="top">
					</tr>
					<tr>
						<td colspan="4"><!-- start order detail table -->
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<?php foreach ($itemsByEvent as $key => $event): ?>
									<?php if(!empty($openEvent[$orderEvents[$key]['_id']])): ?>
										<tr>
											<td colspan="2" style="padding:5px; text-align::left;"><?php echo $orderEvents[$key]['name']?></td>
											<?php if (!empty($orderEvents[$key]['ship_message'])): ?>
												<td>
													<?php echo $orderEvents[$key]['ship_message']?>
												</td>
											<?php endif ?>
										</tr>
										<tr style="background:#ddd;">
											<td style="padding:5px; width:70px;"><strong>Item</strong></td>
											<td style="padding:5px; width:340px;"><strong>Description</strong></td>
											<td style="padding:5px; width:100px;"><strong>Price</strong></td>
											<td style="padding:5px; width: 50px;"><strong>Qty</strong></td>
											<td style="padding:5px; width:100px;"><strong>Subtotal</strong></td>
										</tr>
										<?php foreach ($event as $item): ?>
											<?php if(empty($item['cancel'])): ?>
												<tr>
												<?php
													if (!empty($item['primary_image'])) {
														$image = '/image/'. $item['primary_image'] . '.jpg';
													} else {
														$image = "/img/no-image-small.jpeg";
													}
												?>
													<td style="padding:5px;" title="item">
														<?php echo $this->html->image("$image", array('width' => "60", 'height' => "60", 'style' => "margin:2px; padding:2px; background:#fff; border:1px solid #ddd;")); ?>
													</td>
													<td style="padding:5px" title="description">
														<?php echo $item['description']?>
														<br>
														Color: <?php echo $item['color']?>
														<br>
														Size: <?php echo $item['size']?>
														
														<?php 
														$convertdate = date("Y-m-d h:i:s", 1322071200);
														//echo $orderdate;
														
														?>

													</td>
													<td style="padding:5px; color:#009900;" title="price">
														$<?php echo number_format($item['sale_retail'],2); ?>
													</td>
													<td style="padding:5px;" title="quantity">
														<?php 
															  echo $item['quantity'];
															  $totalQty += $item['quantity'];	
														?>
													</td>
													<td title="subtotal" style="padding:5px; color:#009900;">
														$<?php echo number_format(($item['quantity'] * $item['sale_retail']),2)?>
													</td>
												</tr>
											<?php endif ?>
										<?php endforeach ?>
									<?php endif ?>
								<?php endforeach ?>
							</table>
						</td><!-- end order detail table -->
					</tr>
				</table>
			</td>
		</tr> <!-- end body of email -->
	</table>
	<div style="float:right;margin-right:135px;">
		<?php if($new) {
			echo $spinback_fb;
		}?>
	</div>
	<div class="clear"></div>
	<div class="grid_3">
		<strong>Shipping Address</strong>
		<hr />
		<?php echo $order->shipping->firstname;?> <?php echo $order->shipping->lastname;?>							
		<br />
		<?php echo $order->shipping->address; ?><?php echo $order->shipping->address_2; ?>
		<br />
		<?php echo $order->shipping->city; ?>, <?php echo $order->shipping->state; ?><?php echo $order->shipping->zip; ?>
		<br />
		<br />	
	</div>
	<div class="grid_3">
		<strong>Payment Method</strong>
		<hr />
		<?php echo strtoupper($order->card_type)?> XXXX-XXXX-XXXX-<?php echo $order->card_number?>
	</div>
	<div class="grid_5">
		<strong>Order Information</strong>
		<hr />
		Order Subtotal: <span class="fr">$<?php echo number_format($order->subTotal,2); ?></span>
		<br>
		<?php if ($order->credit_used): ?>
		Credit Applied: <span class="fr">-$<?php echo number_format(abs($order->credit_used),2); ?></span>
			<br>
		<?php endif ?>
		<?php if (($order->promo_discount) && empty($order->promocode_disable)): ?>
		Promotion Discount [<?php echo $order->promo_code?>]: <span class="fr">-$<?php echo number_format(abs($order->promo_discount),2); ?></span>
			<br>
		<?php endif ?>
		<?php if ($order->discount): ?>
		Discount: <span class="fr">-$<?php echo number_format(abs($order->discount),2); ?></span>
			<br>
		<?php endif ?>
		Sales Tax: <span class="fr">$<?php echo number_format($order->tax,2); ?></span>
		<br>
		Shipping: <span class="fr">$<?php echo number_format($order->handling,2); ?></span>
		<?php if ( array_key_exists('overSizeHandling', $order->data()) && $order->overSizeHandling !=0): ?>
	        <br>
	        Oversize Shipping: <span class="fr">$<?php echo number_format($order->overSizeHandling,2); ?></span>
	    <?php endif; ?>
		<br>
		<hr/>
			<strong style="font-weight:bold;color:#606060; font-size:16px;">Total:</strong> <strong style="font-weight:bold;color:#009900; font-size:16px; float:right;">$<?php echo number_format($order->total,2); ?></strong>
		</div>											
	<div class="clear"></div>
	<br>
	<hr/>
	<div class="grid_11">
		<p style="text-align: center; font-size:18px; margin-top:10px;">Thank you for shopping on Totsy.com!</p>
	</div>	
<div class="clear"></div>

</div>
<?php else: ?>
	<strong>Sorry, we cannot locate the order that you are looking for.</strong>
<?php endif ?>
</div>
<?php

$orderTotal = number_format($order->total,2);
$promoCode = $order->promo_code;

?>

<!-- ECOMMERCE TRACKING -->
<?php if ($brandNew): ?>
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  <?php if(Session::read("layout", array("name"=>"default"))=="mamapedia"): ?>
	 // mamasource google analytics tracking code
	  _gaq.push(['_setAccount', 'UA-675412-22']);
	  <?php else: ?>
	  // totsy.com google analytics tracking code
	  _gaq.push(['_setAccount', 'UA-675412-15']);
	  <?php endif ?>
	  _gaq.push(['_trackPageview']);
	  _gaq.push(['_addTrans',
	    '<?php echo $order->order_id?>',           // order ID - required
	    '',  // affiliation or store name
	    '<?php echo $order->total?>',          // total - required
	    '<?php echo $order->tax?>',           // tax
	    '<?php echo $order->handling?>',              // shipping
	    '<?php echo $order->shipping->city?>',       // city
	    '<?php echo $order->shipping->state?>',     // state or province
	    'US'             // country
	  ]);

	   // add item might be called for every item in the shopping cart
	   // where your ecommerce engine loops through each item in the cart and
	   // prints out _addItem for each

	  <?php foreach($itemsByEvent as $event): ?>
			<?php foreach($event as $item): ?>
				 _gaq.push(['_addItem',
				'<?php echo $order->order_id?>',			// order ID - required
				'<?php echo $item['sku']?>',			// SKU/code - required
				'<?php echo $item['description']?>',		// product name
				'<?php echo $item['color']?>',		// category or variation
				'<?php echo $item['sale_retail']?>',        // unit price - required
				'<?php echo $item['quantity']?>'         // quantity - required
				 ]);
			<?php endforeach ?>
		<?php endforeach ?>
	  _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
<?php endif ?>

<script language="javascript">
	<!-- Upsellit.com confirmation
	var siteID = "6525";
	var productID = "77";
	var position = "1";
	var orderID ="<?php echo $order->order_id?>"; //To be filled in by site
	var orderAmt ="<?php echo $order->total?>"; //To be filled in by site
	var command = "REPORT"
	var upsellit_tag = "<scr" + "ipt " + "SRC='http" + (document.location.protocol=='https:'?'s://www':'://www') + ".upsellit.com/upsellitReporting.jsp?command="+command+"&siteID=" + siteID + "&productID=" + productID + "&position=" + position + "&orderID=" + orderID + "&orderAmt=" + orderAmt +"'><\/scr" + "ipt>";
	document.write(upsellit_tag);
	// -->
</script>

<?php
		//converion tracking for Echosystem: a 3rd party JS conversion tracking tool
		if($brandNew){ 
			echo("<img src='https://api.theechosystem.com/Core/Conversion/Save?echoTrackPack=" . $_COOKIE['EchoTrackPack'] . "&revenue=".$orderTotal."&quantity=".(int)$totalQty."&promocode=".$promoCode."' style='width:1px;height:1px;' />");  
		}
?>


<?php if ($new): ?>
	
	<!-- Google Code for acheteurs Remarketing List -->
	<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 1019183989;
		var google_conversion_language = "en";
		var google_conversion_format = "3";
		var google_conversion_color = "666666";
		var google_conversion_label = "SeX0CLn9igIQ9Yb-5QM";
		var google_conversion_value = 0;
		/* ]]> */
	</script>
	
	<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js"></script>
	
	<noscript>
		<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1019183989/?label=SeX0CLn9igIQ9Yb-5QM&amp;guid=ON&amp;script=0"/>
		</div>
	</noscript>
	
	<?php
		//srting of GET variables passed into criteo link
		$criteoVars = "";
		$iCounter = 1;
		
		foreach($itemsByEvent as $event) {
		     foreach($event as $item) {
		     	$criteoVars .=
		     	"&i". $iCounter ."=". (string) $item['item_id'] ."&p". $iCounter ."=". $item['sale_retail'] ."&q". $iCounter ."=". $item['quantity'];
		    	$iCounter++;
		    }
		}
	?>
		
	<script type="text/javascript">
	
		var criteoVars = "<?php echo $criteoVars?>";
		
		//now using global JS variables 
		document.write("<img src=\"" + document.location.protocol + "//dis.us.criteo.com/dis/dis.aspx?p1=" + escape("v=2&wi=7714288&s=1&t=" + orderID + criteoVars ) + "&t1=transaction&p=3290&c=2&resptype=gif\" width=\"1\" height=\"1\" />");
	
	</script>
			
	<!-- END OF Google Code for acheteurs Remarketing List --> 
	<!--  E-COMMERCE -->
		<!--  END OF E-COMMERCE -->
<?php endif ?>