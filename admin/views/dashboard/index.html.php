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

<style type="text/css">
	td {
		font-size:12px;
		text-align: center;
	}
	th {
		font-size:12px;
		text-align: center;
	}
	.positive{
		font-size:12px;
		font-weight:bold;
		color:#00CC66;
	}
	.negative{
		font-size:12px;
		font-weight:bold;
		color:#FF0000;
	}
</style>

<div class="grid_16">
	<h2 id="page-heading">Totsy Dashboard - As of <?=date('m/d/Y g:i:s', $updateTime)?></h2>
</div>
<div class="clear"></div>

<div class="clear"></div>
<div class="grid_10">
	<?=$RevenueChart->renderChart()?>
</div>
<div class="grid_5">
	<?php
		$dayClass = $monthClass = 'positive';
		$lastRevenue = end($lastMonth['revenue'][0]);
		$currentRevenue = end($currentMonth['revenue'][1]);
		$dayDiff = $currentRevenue - $lastRevenue;
		$dayDiffPerct = 100 * $dayDiff/$currentRevenue;
		if ($dayDiffPerct < 1) {
			$dayClass = 'negative';
		}
		$lastMonthRevenue = array_sum($lastMonth['revenue'][0]);
		$currentMonthRevenue = array_sum($currentMonth['revenue'][1]);
		$monthDiff = $currentMonthRevenue - $lastMonthRevenue;
		$monthDiffPerct = 100 * $monthDiff/$currentMonthRevenue;
		if ($monthDiffPerct < 1) {
			$monthClass = 'negative';
		}
	?>
	<table id="revenue_summary" class="" border="1" style="margin:20px;width:500px">
		<thead>
			<tr>
				<th></th>
				<th><?=$lastMonthDesc?></th>
				<th><?=$currentMonthDesc?></th>
				<th>Diff ($)</th>
				<th>Diff (%)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Daily Total:</b></td>
				<td>$<?=number_format($lastRevenue, 2)?></td>
				<td>$<?=number_format($currentRevenue, 2)?></td>
				<td><font class='<?=$dayClass?>'><?=number_format($dayDiff, 2)?></font></td>
				<td><font class='<?=$dayClass?>'><?=number_format($dayDiffPerct, 2)?>%</font></td>
			</tr>
			<tr>
				<td><b>Monthly Total:</b></td>
				<td>$<?=number_format($lastMonthRevenue, 2)?></td>
				<td>$<?=number_format($currentMonthRevenue, 2)?></td>
				<td><font class='<?=$monthClass?>'><?=number_format($monthDiff, 2)?></font></td>
				<td><font class='<?=$monthClass?>'><?=number_format($monthDiffPerct, 2)?>%</font></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="clear"></div>
<div class="grid_10">
	<?=$RegChart->renderChart()?>
</div>
<div class="grid_5">
	<div style='margin:auto 0'>
		<?php
			$dayClass = $monthClass = 'positive';
			$lastRegistration= end($lastMonth['registration'][0]);
			$currentRegistration = end($currentMonth['registration'][1]);
			$dayDiff = $currentRegistration - $lastRegistration;
			$dayDiffPerct = 100 * $dayDiff/$currentRevenue;
			if ($dayDiffPerct < 1) {
				$dayClass = 'negative';
			}
			$lastMonthRegistration = array_sum($lastMonth['registration'][0]);
			$currentMonthRegistration = array_sum($currentMonth['registration'][1]);
			$monthDiff = $currentMonthRegistration - $lastMonthRegistration;
			$monthDiffPerct = 100 * $monthDiff/$currentMonthRegistration;
			if ($monthDiffPerct < 1) {
				$monthClass = 'negative';
			}
		?>
		<table id="registration_summary" class="" border="1" style="width:500px">
			<thead>
				<tr>
					<th></th>
					<th><?=$lastMonthDesc?></th>
					<th><?=$currentMonthDesc?></th>
					<th>Diff (#)</th>
					<th>Diff (%)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><b>Daily Total:</b></td>
					<td><?=number_format($lastRegistration, 0)?></td>
					<td><?=number_format($currentRegistration, 0)?></td>
					<td><font class='<?=$dayClass?>'><?=number_format($dayDiff, 0)?></font></td>
					<td><font class='<?=$dayClass?>'><?=number_format($dayDiffPerct, 2)?>%</font></td>
				</tr>
				<tr>
					<td><b>Monthly Total:</b></td>
					<td><?=number_format($lastMonthRegistration, 0)?></td>
					<td><?=number_format($currentMonthRegistration, 0)?></td>
					<td><font class='<?=$monthClass?>'><?=number_format($monthDiff, 0)?></font></td>
					<td><font class='<?=$monthClass?>'><?=number_format($monthDiffPerct, 2)?>%</font></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="clear"></div>
<div class="grid_10">
	<?=$MonthComboChart->renderChart()?>
</div>
<div class="grid_5">
	<table id="ytd_summary" class="" border="1" style="margin:20px;width:400px">
		<thead>
			<tr>
				<th></th>
				<th>Total Revenue</th>
				<th>Total Registration</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Year to Date:</b></td>
				<?php foreach ($yearToDate as $data): ?>
					<?php if ($data['type'] == 'revenue'): ?>
						<td>$<?=number_format($data['total'], 2)?></td>
					<?php endif ?>
				<?php endforeach ?>
				<?php foreach ($yearToDate as $data): ?>
					<?php if ($data['type'] == 'registration'): ?>
						<td><?=number_format($data['total'], 2)?></td>
					<?php endif ?>
				<?php endforeach ?>
			</tr>
		</tbody>
	</table>
</div>
<div class="clear"></div>
<div class="grid_16">
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