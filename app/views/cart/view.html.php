<?=$this->html->script(array('jquery.countdown.min','jquery.number_format'));?>
<?=$this->html->style(array('jquery.countdown', 'base'));?>
<?php
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
	$test = $cart->data();
?>
<div class="message"></div>
<?php if (!empty($test)): ?>
<?php $subTotal = 0; ?>
<?=$this->form->create(); ?>
	<h1 class="page-title gray"><span class="red"><a href="/" title="Sales">Today's Sales</a> /</span> My Cart</h1>

	<hr />
	<div id='message'><?php echo $message; ?></div>
	<div class='fr' style="padding:10px; background:#fffbd1; border-top:1px solid #D7D7D7; border-right:1px solid #D7D7D7; border-left:1px solid #D7D7D7;">Estimated Ship Date: <?=date('m-d-Y', $shipDate)?></div>
	<div id="middle" class="fullwidth">
		<table width="100%" class="cart-table">
			<thead>
				<tr>
					<th>Item</th>
					<th>Description</th>
					<th>Price</th>
					<th>Quantity</th>
					<th>Total</th>
					<th>Time Remaining</th>
					<th>Remove Item</th>
				</tr>
			</thead>
			<tbody>
	<?php $x = 0; ?>
	<?php foreach ($cart as $item): ?>
		<!-- Build Product Row -->
					<tr id="<?=$item->_id?>" class="alt0">
					<td class="cart-th">
						<?php
							if (!empty($item->primary_image)) {
								$image = $item->primary_image;
								$productImage = "/image/$image.jpg";
							} else {
								$productImage = "/img/no-image-small.jpeg";
							}
						?>
						<?=$this->html->link(
							$this->html->image("$productImage", array(
								'width'=>'60',
								'height'=>'60',
						'style' => 'margin:2px; display:block; padding:4px;')),
							array('Items::view', 'args' => $item->url),
								array(
								'id' => 'main-logo_', 'escape'=> false
							)
						); ?>
					</td>
					<td class="cart-desc" style="width:325px;">
						<?=$this->form->hidden("item$x", array('value' => $item->_id)); ?>
						<strong><?=$this->html->link($item->description,'sale/'.$item->event_url.'/'.$item->url); ?></strong><br>
						<strong>Color:</strong> <?=$item->color;?><br>
						<strong>Size:</strong> <?=$item->size;?>
					</td>

					<td class="<?="price-item-$x";?>" style="width:45px;">
						<strong style="color:#009900;">$<?=number_format($item->sale_retail,2)?></strong>
					</td>
					<td class="<?="qty-$x";?>" style="width:65px; text-align:center">
					<!-- Quantity Select -->
					<?php
						if($item->available < 9){
							$qty = $item->available;
							if($item->quantity > $qty){
								$select = array_unique(array_merge(array('0'), range('1',(string)$item->quantity)));
							}else{
								$select = array_unique(array_merge(array('0'), range('1',(string)$qty)));
							}
						}else{
							$select = array_unique(array_merge(array('0'), range('1','9')));
						}
					?>
					<?=$this->form->select("cart[{$item->_id}]", $select, array(
    					'id' => $item->_id, 'value' => $item->quantity
					));
					?>
					</td>
					<td class="<?="total-item-$x";?>" style="width:55px;">
						<strong style="color:#009900;">$<?=number_format($item->sale_retail * $item->quantity ,2)?></strong>
					</td>
					<td class="cart-time" style="width:220px;"><img src="/img/clock_icon.gif" class="fl"/><div id='<?php echo "itemCounter$x"; ?>' class="fl" style="margin-left:5px;"></div></td>
					<td class="cart-actions">
						<a href="#" id="remove<?=$item->_id; ?>" title="Remove from your cart" class="delete" onclick="return deletechecked('Are you sure you want to remove this item?');" style="color: red!important;">remove</a>
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
						    expiryText: '<div class=\"over\" style=\"color:#fff; padding:5px; background: #ff0000;\">no longer reserved</div>', $countLayout});
						var now = new Date()
						if (itemExpires < now) {
							$(\"#itemCounter$x\").html('<div class=\"over\" style=\"color:#fff; padding:5px; background: #ff0000;\">no longer reserved</div>');
						}
						});
						</script>";
					$removeButtons[] = "<script type=\"text/javascript\" charset=\"utf-8\">
							$('#remove$item->_id').click(function () {
								$('#$item->_id').remove();
								$.post(\"/cart/remove\" , { id: '$item->_id' } );
							    });
						</script>";
					$subTotal += $item->quantity * $item->sale_retail;
					$x++;
				?>
	<?php endforeach ?>

		<tr class="cart-total">

			<td colspan="7" id='subtotal'><strong>Subtotal: <span style="color:#009900;">$<?=number_format($subTotal,2)?></span></strong><br/><hr/><?=$this->form->submit('Update Cart', array('class' => 'button'))?></td>

		</tr>
		<tr class="cart-buy">
			<td colspan="4" class="return-policy">
				<a href='../../pages/returns'><strong style="font-size:12px; font-weight:normal;">Refund &amp; Return Policy</strong></a><br />
			</td>
			<td class="cart-button" colspan="3">
				<?=$this->html->link('Proceed To Checkout', 'Orders::add', array('class' => 'button')); ?>
				<?=$this->html->link('Continue Shopping', "sale/$returnUrl", array('class' => 'button', 'style' => 'margin-right:10px;')); ?>
			</td>
			</tbody>
		</table>


	</div>
<?=$this->form->end(); ?>


	<?php if (!empty($itemCounters)): ?>
		<?php foreach ($itemCounters as $counter): ?>
			<?php echo $counter ?>
		<?php endforeach ?>
	<?php endif ?>

	<?php if (!empty($removeButtons)): ?>
		<?php foreach ($removeButtons as $button): ?>
			<?php echo $button ?>
		<?php endforeach ?>
	<?php endif ?>
<?php else: ?>
	<h1 class="page-title gray"><span class="red"><a href="/" title="Sales">Today's Sales</a> /</span> My Cart</h1>

	<hr />
<div><h1><center>You have no items in your cart. <br> <a href="/sales" title="Continue Shopping">Continue Shopping</a/></center></h1></div>
	</div>
<?php endif ?>
<div id="modal">

</div>
<script type="text/javascript" charset="utf-8">
	$(".inputbox").bind('keyup', function() {
	var id = $(this).attr('id');
	var qty = $(this).val();
	var price = $(this).closest("tr").find("td[class^=price]").html().split("$")[1];
	var cost = parseInt(qty) * parseFloat(price);

	var itemCost = $().number_format(cost, {
		numberOfDecimals: 2,
		decimalSeparator: '.',
		thousandSeparator: ','
	});

	$(this).closest("tr").find("td[class^=total]").html("<strong>$" + itemCost + "</strong>");
	var subTotal = 0;

	$("td[class^=total]").each(function() {
	    subTotal += parseFloat($(this).html().split("$")[1]);
	});

	var subTotalProper = $().number_format(subTotal, {
		numberOfDecimals: 2,
		decimalSeparator: '.',
		thousandSeparator: ','
	});

	$.ajax({
		url: $.base + 'cart/update',
		data: "_id=" + id + "&" + "qty=" + qty,
		context: document.body,
		success: function(message) {
			$('#message').addClass("cart-message");
			$('#message').css("padding: 0pt 0.7em;");
			$('#message').html('<center>' + message + '</center>');
		}
	});
	$("#subtotal").html("<strong>Subtotal: $" + subTotalProper + "</strong>");
});
</script>
<script>
function deletechecked(message)
        {
            var answer = confirm(message)
            if (answer){
                document.messages.submit();
                return false;
            }
            return false;
        }
</script>
<script language="javascript">
document.write('<sc'+'ript src="http'+ (document.location.protocol=='https:'?'s://www':'://www')+ '.upsellit.com/upsellitJS4.jsp?qs=263250249222297345328277324311272279294304313337314308344289&siteID=6525"><\/sc'+'ript>')
</script>