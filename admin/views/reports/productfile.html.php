<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('table');?>
<?=$this->html->style('TableTools');?>

<div class="grid_16">
	<h2 id="page-heading">Product File - <?=$event->name?></h2>
</div>
<div id="clear"></div>

<?php if (!empty($productFile)): ?>
	<div class="grid_16">
			<table id="product_file" class="datatable" border="1">
				<thead>
					<tr>
						<?php 
						foreach ($productHeading as $heading => $value) {
							echo "<th>$heading</th>";
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($productFile as $items): ?>
						<tr>
							<?php foreach ($items as $key => $value): ?>
								<?php if (in_array($key, array('Total', 'Unit'))): ?>
									<td>$<?=number_format($value, 2)?></td>
								<?php else: ?>
									<td><?=$value?></td>
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
		$('#product_file').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": false
		}
		);
	} );
</script>