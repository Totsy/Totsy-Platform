<div id="entire">
<?php echo $this->html->script('jquery-1.4.2');?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->style(array('jquery_ui_blitzer.css', 'table'));?> 
<?php
	$this->title(" - Order Confirmation");
?>

	<style type="text/css">
		td{font-family:Arial,sans-serif;color:#888888;font-size:14px;line-height:18px}
		img{border:none}
	</style>
	<table cellspacing="0" cellpadding="0" border="0" width="695">
		
		<tr>
			<td colspan="4">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr style="background:#ddd;">
						<td style="padding:5px; width:70px;"><strong>Item</strong></td>
						<td style="padding:5px; width:340px;"><strong>Description</strong></td>
						<td style="padding:5px; width:100px;"><strong>Price</strong></td>
						<td style="padding:5px; width: 50px;"><strong>Qty</strong></td>
						<td style="padding:5px; width:80px;"><strong>Subtotal</strong></td>
						<td style="padding:5px; width:30px;"><strong>Tax Return</strong></td>
					</tr>
			<?php echo $this->form->create(null ,array('id'=>'partOrderTaxReturnForm','enctype' => "multipart/form-data")); ?>
			<?php $items = $order['items']; ?>
			<?php foreach ($items as $key => $item): ?>
				<?php if (!isset($item['cancel'])){ $item['cancel']=null; }?>
				<?php $name = 'items['.strval($key).'][cancel]'; ?>
				<?php echo $this->form->hidden($name, array('class' => 'inputbox', 'id' => $name, 'value' => (string) $item['cancel'])); ?>
				<?php echo $this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"])); ?>
					<tr class="item_line"
						<?php if($item["cancel"] == true) {
							echo "style='background-color:red;opacity:.5'"; 
							} ?>
							 id="<?php echo $key?>">
						<?php
							if (!empty($item['primary_image'])) {
								$image = '/image/'. $item['primary_image'] . '.jpg';
							} else {
								$image = "/img/no-image-small.jpeg";
							}
						?>
						<td style="padding:5px;" title="item">
							<?php echo $this->html->image("$image", array(
								'width' => "60",
								'height' => "60",
								'style' => "margin:2px; padding:2px; background:#fff; border:1px solid #ddd;"
								));
							?>
						</td>
						<td style="padding:5px" title="description">
							Event: <?php echo $this->html->link($item['event_name'], array(
								'Events::preview', 'args' => $item['event_id']),
								array('target' =>'_blank')
							); ?><br />
							Item: <?php echo $this->html->link($item['description'],
								array('Items::preview', 'args' => $item['url']),
								array('target' =>'_blank')
							); ?><br />
							Color: <?php echo $item['color']?><br/>
							Size: <?php echo $item['size']?><br/>
							Vendor Style: <?php echo $sku["$item[item_id]"];?><br/>
							Category: <?php echo $item['category'];?><br/>
						</td>
						<td style="padding:5px; color:#009900;" title="price">
							$<?php echo number_format($item['sale_retail'],2); ?>
						</td>
						<td style="padding:5px;" title="quantity">
						<?php if($edit_mode): ?>
							<?php
							if(!empty($item['initial_quantity'])) {
								$limit = $item['initial_quantity'];
							} else {
								$limit = $item['quantity'];
							}
							$i = 0;
							$quantities = array();
							do {
								$quantities[$i] = $i;
								$i++;
							} while ($i <= $limit)
							?>
							<?php echo $this->form->hidden("items[".$key."][initial_quantity]", array('class' => 'inputbox', 'id' => "initial_quantity", 'value' => $limit )); ?>
							<?php echo $this->form->select('items['.$key.'][quantity]', $quantities, array('style' => 'float:left; width:50px; margin: 0px 20px 0px 0px;', 'id' => 'dd_qty', 'value' => $item['quantity'], 'onchange' => "change_quantity()"));
							?>
						<?php else :?>
								<?php echo $item['quantity'] ?>
						<?php endif ?>
						</td>
						<td title="subtotal" style="padding:5px; color:#009900;">
							$<?php echo number_format(($item['quantity'] * $item['sale_retail']),2)?>
						</td>
						<td>
							<?php if ($item['quantity']>1){?>
							<input class="return_check_multi" type="checkbox" name="return_check[]" value="<?php echo $item['item_id']?>">
							<div id="div_<?php echo $item['item_id']?>" style="display: none;">
								<select name="return_quantity[<?php echo $item['item_id']?>]">
								<?php for($i=1;$i<=$item['quantity'];$i++){?>
									<option value="<?php echo $i?>"><?php echo $i?></option>
								<?php }?>
								</select>
							</div>
							<?php } else { ?>
							<input class="return_check" type="checkbox" name="return_check[]" value="<?php echo $item['item_id']?>">
							<input type="hidden" name="return_quantity[<?php echo $item['item_id']?>]" value="1">
							<?php } ?>
						</td>
					</tr>
								
				<?php endforeach ?>	
			</table>
		</td><!-- end order detail table -->
	</tr>
	<tr>
		<td colspan="4">
			<?php if(empty($order['cancel'])): ?>
			<table style="width:200px; float: right;">
				<tr>
					<td valign="top">
						Order Subtotal:
						<br>
						<?php if (isset($order['credit_used']) && $order['credit_used']): ?>
						Credit Applied:
						<br>
						<?php endif ?>
						<?php if (isset($order['promo_discount']) && ($order['promo_discount']) && (empty($order['promocode_disable']))): ?>
						Promotion Discount:
							<br>
						<?php endif ?>
						Sales Tax:
						<br>
						Shipping:
						<br><br><br>
						<strong style="font-weight:bold;color:#606060">Total:</strong> 
					</td>
					<td style="padding-left:15px; text-align:right;" valign="top">
					$<?php echo number_format($order['subTotal'],2); ?>
					<br>
					<?php if (isset($order['credit_used']) && $order['credit_used']): ?>
						-$<?php echo number_format(abs($order['credit_used']),2); ?>
						<br>
					<?php endif ?>
					<?php if (isset($order['promo_discount']) && ($order['promo_discount']) && (empty($order['promocode_disable']))): ?>
						-$<?php echo number_format(abs($order['promo_discount']),2); ?>
						<br>
					<?php endif ?>
					$<?php echo number_format($order['tax'],2); ?>
					<br>
					$<?php echo number_format($order["handling"] + $order["overSizeHandling"] - $order["handlingDiscount"]- $order["overSizeHandlingDiscount"], 2); ?>
					<br><br><br>
					<strong style="font-weight:bold;color:#009900;">$<?php echo number_format($order['total'],2); ?></strong>
					</td>
				</tr>
			</table>
			<?php endif ?>			
			</td>
		</tr>
	</table>
	<p style="text-align:center;">
		<input type="submit" id="update_button"  onclick="update_order(); return false;" value="Update Order"/>
	</p>
	<?php echo $this->form->end();?>
</div>
<script type="text/javascript" >
$(document).ready(function(){
	$(".return_check_multi").click(function () {
		if ($(this).attr('checked')){
			$('#div_'+$(this).val()).show();
		} else {
			$('#div_'+$(this).val()).hide();
		}
	});
});
</script>