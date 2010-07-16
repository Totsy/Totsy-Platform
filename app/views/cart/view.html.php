<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>
<?php
	$countLayout = "layout: '{mnn}{sep}{snn}'";
?>
<?php if (!empty($cart)): ?>
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
					<tr class="alt0">
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
						<strong><?=$this->html->link($item->item->description, array('Controller::action')); ?></strong><br>
						<strong>Color:</strong> <?=$item->item->color;?><br>
						<strong>Size:</strong><?=$item->item->size;?>
					</td>
					<td class="cart-qty">
						<input type="text" value="<?=$item->item->quantity?>" name="qty" id="qty" class="inputbox" size="1">
					</td>
					<td class="cart-price">
						<strong><?=$item->item->sale_retail?></strong>
					</td>
					<td class="cart-time"><div id="<?php echo "itemCounter$x"; ?>"</div></td>
					<td class="cart-actions">
						<a href="#" title="Remove from your cart" class="delete">delete</a>
					</td>
				</tr>
				<?php
					$date = $item->expires->sec * 1000;
					$counterDiv = "#itemCounter$x";
					$itemCounters[] = "<script type=\"text/javascript\">$(function () {var itemExpires = new Date();itemExpires = new Date($date);$(\"$counterDiv\").countdown({until: itemExpires, $countLayout});});</script>";?>
				<?php $x++; ?>
	<?php endforeach ?>
	
			</tbody>
		</table>
	</div>
<?php endif ?>

<!--Javascript Output for Future Events-->
<?php if (!empty($itemCounters)): ?>
	<?php foreach ($itemCounters as $counter): ?>
		<?php echo $counter ?>
	<?php endforeach ?>
<?php endif ?>

<script type="text/javascript" src="../js/jquery.equalheights.js">
</script><script type="text/javascript">
$(document).ready(function() {
		$("#tabs").tabs();
	});
</script>
