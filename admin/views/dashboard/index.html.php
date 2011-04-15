<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<?=$this->html->script('FusionCharts.js')?>


<div class="grid_16">
	<h2 id="page-heading">Totsy Dashboard</h2>
</div>
<div class="clear"></div>
<div class="grid_16">
	<?=$FC->renderChart()?>
</div>
<div class="clear"></div>
<div class="grid_12">
	
	<div class="box">
		<h2>
			<p>Data of Registration/Sale Figures</p>
		</h2>
		<div class="block" id="forms">
			<fieldset>
				<table id="sale_table" class="datatable" border="1">
					<thead>
						<tr>
							<th>Day</th>
							<th># of Registrations</th>
							<th>% Change Registration</th>
							<th>Total Revenue</th>
							<th>% Change Revenue</th>
						</tr>
					</thead>
				<?php
					$i = 0;
				?>
				<?php while ($i < count($registrationDetails)):?>
					<tr>
						<td><?=date('m-d-Y', $registrationDetails[$i]->date->sec)?></td>
						<td><?=$registrationDetails[$i]->total?></td>
						<td>
						<?php if ($i <> 0): ?>
							<?php $percent = 100*($registrationDetails[$i]->total - $registrationDetails[$i-1]->total)/$registrationDetails[$i]->total;?>
							<?=number_format($percent, 2);?>%</td>
						<?php else: ?>
							-
						<?php endif?>
						</td>
						<td>$<?=number_format($revenuDetails[$i]->total, 2);?></td>
						<td>
						<?php if ($i <> 0): ?>
							<?=number_format(100*($revenuDetails[$i]->total - $revenuDetails[$i-1]->total)/$revenuDetails[$i]->total, 2);?>%</td>
						<?php else: ?>
							-
						<?php endif?>
						</td>
					</tr>
					<?php ++$i?>
				<?php endwhile?>

				</table>
			</fieldset>
		</div>
	</div>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#sale_table').dataTable({
			"bPaginate": true,
			"bFilter": true
		}
		);
	} );
</script>