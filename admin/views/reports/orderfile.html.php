<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('table');?>
<?=$this->html->style('TableTools');?>

<div class="grid_16">
	<h2 id="page-heading">Order File - <?=$event->name?></h2>
</div>
<div class="clear"></div>
<?php if (!empty($orderFile)): ?>
	<div class="clear"></div>
	<div class="grid_16">
			<table id="order_list" class="datatable" border="1">
				<thead>
					<tr>
						<?php 
						foreach ($heading as $value) {
							echo "<th>$value</th>";
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($orderFile as $orders): ?>
						<tr>
							<?php foreach ($orders as $key => $value): ?>
								<td><?=$value?></td>
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
			"sDom": 'T<"clear">lfrtip',
			'bLengthChange' : false,
			"bPaginate": false,
			"bFilter": false
		}
		);
	} );
</script>