<?php echo $this->html->script('jquery-1.4.2.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->style('TableTools');?>

<div class="grid_16">
	<h2 id="page-heading">Order File - <?php echo $event->name?></h2>
</div>
<div class="clear"></div>
<?php if (!empty($orderList)): ?>
	<?php echo $this->form->create(null, array('url' => 'Reports::orderfile')); ?>
	<div class="grid_16">
		<?php echo $this->form->submit('Generate Final Order List'); ?>
		<?php echo $this->form->hidden('event_id', array('value' => $event->_id)); ?>
	</div>
	<div class="clear"></div>
	<div class="grid_16">
			<table id="order_list" class="datatable" border="1">
				<thead>
					<tr>
						<?php foreach ($orderHeading as $heading): ?>
							<?php if ($heading == 'Select'): ?>
								<th><input type="checkbox" id="orders_all"> Toggle Select</th>
							<?php else: ?>
								<th><?php echo $heading?></th>
							<?php endif ?>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php $inc = 0 ?>
					<?php foreach ($orderList as $orders): ?>
						<tr>
							<?php ++$inc; ?>
							<?php foreach ($orders as $key => $value): ?>
								<?php if ($key == 'Select'): ?>
									<td><center><input type="checkbox" class="order" name="<?php echo $orders['id']?>-<?php echo $inc?>" value="<?php echo $orders['Cart']?>" <?php echo $value?></center></td>
								<?php elseif ($key == 'Note'): ?>
										<?php if (is_array($value)): ?>
											<?php if ($value['Open'] == 0): ?>
												<td bgcolor="#FF0000">
													All Sale Items Closed
											<?php else: ?>
												<td bgcolor="#00FF00">
													<?php echo $value['Open']?> sale items are open.<br>
											<?php endif ?>
										<?php else: ?>
											<?php echo $value?>
										<?php endif ?>
									</td>
								<?php else: ?>
									<?php if (!in_array($key, array('Cart', 'id', 'Item'))): ?>
										<td><?php echo $value?></td>
									<?php endif ?>
								<?php endif ?>
							<?php endforeach ?>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
	</div>
	<?php echo $this->form->end(); ?>
<?php endif ?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#order_list').dataTable({
			"sDom": 'T<"clear">lfrtip',
			'bLengthChange' : false,
			"bPaginate": false
		}
		);
		$("#orders_all").click(function()
		{
			var checked_status = this.checked;
			$(".order").each(function()
			{
				this.checked = checked_status;
			});
		});
	} );
</script>
