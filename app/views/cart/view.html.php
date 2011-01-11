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
	<h2 class="gray mar-b">My Cart</h2>
	<hr />
	<div id='message'></div>
	<div id="middle" class="fullwidth">
		<table width="100%" class="cart-table">
			<thead>
				<tr>
					<th>Item</th>
					<th>Description</th>
					<th>QTY</th>
					<th>Price</th>
					<th>Total Cost</th>
					<th>Time Remaining</th>
					<th>Remove Item</th>
				</tr>
			</thead>
			<tbody>
	<?php $x = 0; ?>
	<?php foreach ($cart as $item): ?>
		<!-- Build Product Row -->
					<tr id="<?=$item->_id?>" class="alt0" style="margin:0px!important; padding:0px!important;">
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
						'style' => 'border:1px solid #ddd; background:#fff; margin:2px; display:block; padding:2px;')),
							array('Items::view', 'args' => $item->url),
								array(
								'id' => 'main-logo_', 'escape'=> false
							)
						); ?>
					</td>
					<td class="cart-desc">
						<?=$this->form->hidden("item$x", array('value' => $item->_id)); ?>
						<strong><?=$this->html->link($item->description,'sale/'.$item->event.'/'.$item->url); ?></strong><br>
						<strong>Color:</strong> <?=$item->color;?><br>
						<strong>Size:</strong> <?=$item->size;?>
					</td>
					<td class="<?="qty-$x";?>">
						<?=$this->form->text('', array(
							'value' => $item->quantity,
							'name' => 'qty',
							'id' => $item->_id,
							'class' => 'inputbox',
							'size' => '1',
							'maxlength' => 1
							));
						?>

					</td>
					<td class="<?="price-item-$x";?>">
						<strong style="color:#009900;">$<?=number_format($item->sale_retail,2)?></strong>
					</td>
					<td class="<?="total-item-$x";?>">
						<strong style="color:#009900;">$<?=number_format($item->sale_retail * $item->quantity ,2)?></strong>
					</td>
					<td class="cart-time"><div id='<?php echo "itemCounter$x"; ?>'></div></td>
					<td class="cart-actions">
						<a href="#" id="remove<?=$item->_id; ?>" title="Remove from your cart" class="delete">remove</a>
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
						    expiryText: '<div class=\"over\" style=\"color:#fff; background: #ff0000;\">This item is no longer reserved</div>', $countLayout});
						var now = new Date()
						if (itemExpires < now) {
							$(\"#itemCounter$x\").html('<div class=\"over\" style=\"color:#fff; background: #ff0000;\">This item is no longer reserved</div>');
						}
						});
						</script>";
					$removeButtons[] = "<script type=\"text/javascript\" charset=\"utf-8\">
							$('#remove$item->_id').click(function () { 
								$('#$item->_id').remove();
								$.ajax({url: $.base + \"cart/remove\", data:'$item->_id', context: document.body, success: function(data){
								      }});
							    });
						</script>";
					$subTotal += $item->quantity * $item->sale_retail;
					$x++; 
				?>
	<?php endforeach ?>
		<tr class="cart-total">
			<td colspan="7" id='subtotal'><strong>Subtotal: <span style="color:#009900;">$<?=number_format($subTotal,2)?></span></strong></td>
		    </td>
		</tr>
		<tr class="cart-buy">
			<td colspan="5" class="return-policy">
				<a href='../../pages/returns'><strong style="font-size:12px; font-weight:normal;">Refund &amp; Return Policy</strong></a><br />
			</td>
			<td class="cart-button" colspan="2">
				<?=$this->html->link('Buy Now', 'Orders::add', array('class' => 'proceed-to-checkout')); ?>
				<?=$this->html->link('Buy Now', 'Events::index', array('class' => 'continue-shopping')); ?>
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
<div id='empty-cart'><h1><center>You have no items in your cart. <br> Take a look at our open sales to continue shopping.</center></h1><div>
	
<?php endif ?>

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

