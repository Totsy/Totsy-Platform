<?php echo $this->html->script('jquery-1.4.2.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->style('TableTools');?>

<div class="grid_16">
	<h2 id="page-heading">Purchase Order: <?php echo $poNumber; ?>
	(<?php echo $event->name?>)</h2>
</div>
<div id="clear"></div>
<div class="grid_8">
	<div id="box">
		<?php if ($total['sum'] == 0 && count($purchaseOrder) == 0): ?>
			<h2>No product has been sold for this event.</h2>
		<?php else: ?>
            <p>Total Quantity - <?php echo $total['quantity']?></p>
            <p>Order Total - $<?php echo number_format($total['sum'], 2)?></p>
		<?php endif ?>
	</div>
</div>
<?php if (!empty($purchaseOrder)): ?>
	<div class="grid_16">
			<table id="purchase_order" class="datatable" border="1">
				<thead>
					<tr>
						<?php
						foreach ($purchaseHeading as $heading) {
							echo "<th>$heading</th>";
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($purchaseOrder as $items): ?>
						<tr>
							<?php foreach ($items as $key => $value): ?>
								<?php if (in_array($key, array('Total', 'Unit'))): ?>
									<td>$<?php echo number_format($value, 2)?></td>
								<?php else: ?>
									<td><?php echo $value?></td>
								<?php endif ?>
							<?php endforeach ?>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
	</div>
<?php endif ?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#purchase_order').dataTable({
			"sDom": 'T<"clear">lfrtip',
			'bLengthChange' : false,
			"bPaginate": false
		}
		);
	} );
</script>