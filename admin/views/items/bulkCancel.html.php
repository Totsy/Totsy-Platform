<?
use admin\models\Order;

?>

<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>

<script>

function confirmSubmit()
{

var agree=confirm("Are you sure you wish to continue?");
if (agree)
	return true ;
else
	return false ;
}
</script>

</script>

<div class="grid_16">
	<h2 id="page-heading">
		Bulk Cancellation Tool
		<?php if ($search_sku): ?>
			- Searching for SKU <?=$search_sku;?>
		<?php endif; ?>
	</h2>
</div>

<div  class="grid_16">
<p>This bulk cancel tool shows you all orders for a particular item or line item.   You can sort by most recent orders and cancel only those orders that have either been short-shipped or marked as unreceivable by the warehouse.  When the line-item is canceled, the user will receive an email notification letting them know the line-item was canceled from their order.
</p></div>

<div id="clear"></div>


<div id="clear"></div>
<div class="grid_16">
	<div class="box">
	<h2>
		<a href="#" id="toggle-order-search">Search</a>
	</h2>
	<div class="block" id="order-search">
		<fieldset>
			<?=$this->form->create(); ?>
				<?=$this->form->text('search', array(
					'id' => 'search',
					'style' => 'float:left; width:440px; margin: 0px 10px 0px 0px;'
					));
				?>
				<?=$this->form->submit('Submit'); ?>
				(Search By SKU only)
			<?=$this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>

<div id="clear"></div>
<div id="clear"></div>

<div class="grid_16">
<?php if (!empty($items)): ?>
			<?php
				$x=0;
				foreach ($items as $item):
			?>
		<table border="1">
		<thead>
			<tr>
				<th>Image</th>
				<th>Sale Retail</th>
				<th>MSRP</th>
				<th>Description</th>
				<th>Vendor</th>
				<th>Vendor Style</th>
				<th>Color</th>
				<th>Size</th>
				<th>Totsy SKU</th>
			</tr>
		</thead>
		<tbody>

				<tr>
					<?php
						if (!empty($item['primary_image'])) {
							$image = '/image/'. $item['primary_image'] . '.jpeg';
						} else {
							$image = "/img/no-image-small.jpeg";
						}
					?>
					<td width="5%">
						<?=$this->html->image("$image", array(
							'width' => "110",
							'height' => "110",
							'style' => "margin:2px; padding:2px; background:#fff; border:1px solid #ddd;"
							));
						?>
					</td>
					<td>$<?=$item['sale_retail']?></td>
					<td>$<?=$item['msrp']?></td>
					<td width="5%"><?=$item['description']?></td>
					<td><?=$item['vendor']?></td>
					<td width="5%"><?=$item['vendor_style']?></td>
					<td>
					<?php if (empty($item['color'])): ?>
						None
					<?php else: ?>
						<?=$item['color']?>
					<?php endif ?>
					</td>
					<td>
						<?php foreach ($item['sku_details'] as $key => $value): ?>
							<?=$key?><br />
						<?php endforeach ?>
					</td>
					<td>
						<?php foreach ($item['sku_details'] as $key => $value): ?>
							<a href="/items/bulkCancel/<?=$value;?>"><?=$value?></a><br />
						<?php endforeach ?>
					</td>
				</tr>
				<tr>
					<td colspan="9">

					<?=$this->form->create(null, array('url'=>'/orders/cancelMultipleItems')); ?>

					<?=$this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $item["_id"])); ?>
					<?=$this->form->hidden('sku', array('class' => 'inputbox', 'sku' => 'sku', 'value' => $search_sku)); ?>

<?php

					$item_id =  $item['_id']->__toString();

					$orders = Order::find('all',array('conditions'=> array('items.item_id' => $item_id)));
?>


						<table id="orderTable_<?=$x;?>" class="datatable" >
		<thead>
								<tr>
									<th><div class="controls">
	<span><input type="checkbox" class="checkAll" /><!-- <b>Check All</b> <span> or
	<span><a href="javascript:void(0);" class="invertSelection">Invert Selection</a></span> -->
</div></th>
									<th>Order ID</th>
									<th>Order Status</th>
									<th>SKU</th>
									<th>Firstname Lastname</th>
									<th>Quantity</th>
									<th>Size</th>
									<th>Color</th>
									<th>Order Date / Time</th>
									<th>Actions</th>
								</tr>
								</thead>
								<fieldset>
								<tbody>
						<?php

						$i=0;

						foreach($orders as $order) {
								$order_temp = $order->data();
								$o=0;
								while ($o < sizeof($order_temp["items"])) {
									if ($order_temp["items"][$o]["item_id"] == $item['_id']) {
										$line_item = $order_temp["items"][$o];
										$o = 99; //break out of loop
									}
									$o++;
								}

								foreach ($item['sku_details'] as $key => $value):
									if ($key == $line_item["size"]) {
										$sku = $value;
									}
								endforeach;



								if ($sku == $search_sku) {

						?>

										<tr>
											<td>

		<div style="display: none;">
			<input type="type" id="<?=$order[_id];?>" name="order[<?=$i;?>]" value="" class="cb-element" >
			<input type="hidden" id="line_number[<?=$i;?>]" name="line_number[<?=$i;?>]" value="<?=$line_item[line_number];?>">

		</div>

<?php if (!$line_item["cancel"] || $line_item["cancel"] == 0): ?>
		<input type="checkbox" id="<?=$order[_id];?>" name="order[<?=$i;?>]" value="<?=$order[_id];?>" class="cb-element" >
		<input type="hidden" id="line_number[<?=$i;?>]" name="line_number[<?=$i;?>]" value="<?=$line_item[line_number];?>">
<?php endif; ?>
											</td>
											<td><?=$order["order_id"];?></td>
											<td><?php
											    if ($line_item["cancel"] == 1): ?>
											    <strong>Cancelled</strong>
											    <?php else:?>
											        <?=$line_item["status"];?>
											    <?php endif; ?></td>
											<td><?=$sku;?></td>
											<td><?=$order->billing->firstname." ".$order->billing->lastname;?></td>
											<td><?=$line_item["quantity"];?></td>
											<td><?=$line_item["size"];?></td>
											<td>
								<?php if (empty($line_item['color'])): ?>
									None
								<?php else: ?>
									<?=$line_item['color']?>
								<?php endif; ?>
											</td>
											<td><?=date('Y-M-d h:i:s',$order_temp[date_created]['sec']);?></td>
											<td>

											<a href="/orders/view/<?=$order[_id];?>">View Order</a>
											<?php if ($line_item[cancel] != 1) {?> | <a href="/orders/cancelOneItem?line_number=<?=$line_item[line_number];?>&order_id=<?=$order[_id];?>&item_id=<?=$item_id;?>&sku=<?=$sku;?>" onclick="return cancelLineItem();">Cancel</a><?php } ?></td>
										</tr>
									<?php
						 		$i++;
						 		} //end of search for sku
						  }  //end of orders TR
						?>
							</tbody>
							</fieldset>
						</table>
						<!-- end of orders table within items table -->

					</td>
				</tr>



		</tbody>
	</table>

					<br/>		<input type="submit" id="submit"  value="Bulk Cancel these Line Items" onClick="return confirmSubmit()"/>
						<?=$this->form->end();?>
<br/><br/>
<hr/>

			<?php
			$x++;

			endforeach  //end of items foreach

			?>
	<!-- end of items table -->



<?php  endif ?>
</div>


<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";

		<?php
			$a=0;
			while ($a < $x) {
		?>
		$('#orderTable_<?=$a;?>').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"aaSorting": [[ 8, "desc" ]],
			"bStateSave": true,
			"aoColumnDefs": [ {"bSortable": false, "aTargets": [0] } ]
		});

		<?php
				$a++;
			}
		?>

	}
	);

</script>

<script type="text/javascript" language="javascript">
	$( function() {
		$( '.checkAll' ).live( 'change', function() {
			$( '.cb-element' ).attr( 'checked', $( this ).is( ':checked' ) ? 'checked' : '' );
			$( this ).next().text( $( this ).is( ':checked' ) ? 'Uncheck All' : 'Check All' );
		});
		$( '.invertSelection' ).live( 'click', function() {
			$( '.cb-element' ).each( function() {
				$( this ).attr( 'checked', $( this ).is( ':checked' ) ? '' : 'checked' );
			}).trigger( 'change' );

		});
		$( '.cb-element' ).live( 'change', function() {
			$( '.cb-element' ).length == $( '.cb-element:checked' ).length ? $( '.checkAll' ).attr( 'checked', 'checked' ).next().text( 'Uncheck All' ) : $( '.checkAll' ).attr( 'checked', '' ).next().text( 'Check All' );

		});
	});
</script>