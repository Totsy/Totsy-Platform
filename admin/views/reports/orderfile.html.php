<?php echo $this->html->script('jquery-1.4.2.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->style('TableTools');?>
<?php if (!empty($event)): ?>
	<div class="grid_16">
		<h2 id="page-heading">Order File - <?php echo $event->name?></h2>
	</div>
<?php endif ?>

<div class="clear"></div>
<?php if (!empty($orderFile)): ?>
	<div class="clear"></div>
	<div class="grid_16">
			<table id="order_list" class="datatable" border="1" style="width:960px;">
				<thead>
					<tr>
						<?php 
						foreach ($heading as $key => $value) {
							echo "<th>$key</th>";
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($orderFile as $orders): ?>
						<tr>
							<?php foreach ($orders as $key => $value): ?>
								<td><?php echo $value?></td>
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
		$('#order_list').dataTable({
			'bAutoWidth' : false,
			"sDom": 'T<"clear">lfrtip',
			"bScrollCollapse": true
					});
	} );
</script>