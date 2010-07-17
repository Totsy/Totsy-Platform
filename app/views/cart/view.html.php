<?=$this->html->script(array('jquery.countdown.min','jquery.number_format'));?>
<?=$this->html->style('jquery.countdown');?>
<?php
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
	$test = $cart->data();
?>
<?php if (!empty($test)): ?>
<?php $subTotal = 0; ?>
<?=$this->form->create(); ?>
	<h1 class="page-title">
		Your Cart
	</h1>
	<div id="middle" class="fullwidth">
		<table width="100%" class="cart-table">
			<thead>
				<tr>
					<th>
						Item
					</th>
					<th>
						Description
					</th>
					<th>
						QTY
					</th>
					<th>
						Price
					</th>
					<th>
						Total Cost
					</th>
					<th>
						Time Remaining
					</th>
					<th>
						Remove Item
					</th>
				</tr>
			</thead>
			<tbody>
	<?php $x = 0; ?>
	<?php foreach ($cart as $item): ?>
		<!-- Build Product Row -->
					<tr id="<?=$item->_id?>" class="alt0">
					<td class="cart-th">
						<?php
							if (!empty($item->primary_images)) {
								$image = $item->primary_images[0];
								$productImage = "/image/$image.jpg";
							} else {
								$productImage = "/img/no-image-small.jpeg";
							}
						?>
						<?=$this->html->link(
							$this->html->image("$productImage", array('width'=>'93', 'height'=>'93')), '', array(
								'id' => 'main-logo', 'escape'=> false
							)
						); ?>
					</td>
					<td class="cart-desc">
						<?=$this->form->hidden("item$x", array('value' => $item->_id)); ?>
						<strong><?=$this->html->link($item->description, array('Items::view', 'args' => $item->url)); ?></strong><br>
						<strong>Color:</strong> <?=$item->color;?><br>
						<strong>Size:</strong><?=$item->size;?>
					</td>
					<td class="<?="qty-$x";?>">
						<input type="text" value="<?=$item->quantity?>" name="qty" id="qty<?=$x?>" class="inputbox" size="1">
					</td>
					<td class="<?="price-item-$x";?>">
						<strong>$<?=number_format($item->sale_retail,2)?></strong>
					</td>
					<td class="<?="total-item-$x";?>">
						<strong>$<?=number_format($item->sale_retail * $item->quantity ,2)?></strong>
					</td>
					<td class="cart-time"><div id="<?php echo "itemCounter$x"; ?>"</div></td>
					<td class="cart-actions">
						<a href="#" id="<?php echo "remove$item->_id"?>" title="Remove from your cart" class="delete">delete</a>
					</td>
				</tr>
				<?php
					$date = $item->expires->sec * 1000;
					$itemCounters[] = "<script type=\"text/javascript\">
						$(function () {
							var itemExpires = new Date();
							itemExpires = new Date($date);
							$(\"#itemCounter$x\").countdown('change', {until: itemExpires, $countLayout});
							
						$(\"#itemCounter$x\").countdown({until: itemExpires, 
						    expiryText: '<div class=\"over\">This item is no longer reserved</div>', $countLayout});
						var now = new Date()
						if (itemExpires < now) {
							$(\"#itemCounter$x\").html('<div class=\"over\">This item is no longer reserved</div>');
						}
						});
						</script>";
					$removeButtons[] = "<script type=\"text/javascript\" charset=\"utf-8\">
							$('#remove$item->_id').click(function () { 
								$('#$item->_id').remove();
								$.ajax({url: \"remove\", data:'$item->_id', context: document.body, success: function(data){
										location.reload();
								      }});
							    });
						</script>";
					$quantityBox[] = "";
						
					$subTotal += $item->quantity * $item->sale_retail;
					$x++; 
				?>
	<?php endforeach ?>
		<tr class="cart-total">
			<td colspan="7" id='subtotal'><strong>Subtotal: $<?=number_format($subTotal,2)?></strong></td>
		    </td>
		</tr>
		<tr class="cart-buy">
			<td colspan="5" class="cart-notes">
				<strong>Need more time to Shop?</strong><br />
				We combine shipping on any additional orders placed within 1 hour
				<br /><br />
				<strong>Refund &amp; Return Policy</strong><br />
			</td>
			<td class="cart-button" colspan="2">
				<button type="submit" class="flex-btn"><span>Buy Now</span></button>
			</td>
			</tbody>
		</table>
	</div>
<?=$this->form->end(); ?>

	<!--Javascript Output for Future Events-->
	<?php if (!empty($itemCounters)): ?>
		<?php foreach ($itemCounters as $counter): ?>
			<?php echo $counter ?>
		<?php endforeach ?>
	<?php endif ?>
	<!--Javascript Output for Future Events-->
	<?php if (!empty($removeButtons)): ?>
		<?php foreach ($removeButtons as $button): ?>
			<?php echo $button ?>
		<?php endforeach ?>
	<?php endif ?>
<?php else: ?>
<p>Your cart is empty. Let's do something about that!</p>
	
<?php endif ?>


<script type="text/javascript" src="../js/jquery.equalheights.js">
</script>
<script type="text/javascript">
$(document).ready(function() {
		$("#tabs").tabs();
	});
</script>
<script type="text/javascript" charset="utf-8">
$(".inputbox").change(function() {
	var qty = $(this).val();
	var price = $(this).closest("tr").find("td[class^=price]").html().split("$")[1];
	var cost = parseInt(qty) * parseFloat(price);
	var itemCost = $().number_format(cost, {
		     numberOfDecimals:2,
		     decimalSeparator: '.',
		     thousandSeparator: ','
	});
	$(this).closest("tr").find("td[class^=total]").html("<strong>$" + itemCost + "</strong>");
	var subTotal = 0;
	$("td[class^=total]").each(function() {
	    subTotal += parseFloat($(this).html().split("$")[1]);
	});
	var subTotalProper = $().number_format(subTotal, {
			numberOfDecimals:2,
			decimalSeparator: '.',
			thousandSeparator: ','
	});
	$("#subtotal").html("<strong>Subtotal: $" + subTotalProper + "</strong>");
});
</script>

​

