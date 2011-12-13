<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>
<?php echo $this->html->script('FusionCharts.js')?>

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
	<h2 id="page-heading">Totsy Dashboard - As of <?php echo date('m/d/Y', $updateTime)?>*</h2>
</div>
<!--Gross Revenue Summary Begins Here-->
<div class="clear"></div>
<div class="grid_16">
	<h2>
		Gross Revenue Summary
	</h2>
	<p>(Promocodes and Credits included)</p>
	<center>
		<?php echo $GrossRevChart->renderChart()?>
	</center>
</div>
<div class="clear"></div>
<div class="grid_16">
	<?php
	    ini_set("display_errors", 0);
		$dayClass = $weekClass = $monthClass = 'positive';
		$lastRevenue = end($lastMonth['gross'][0]);
		$currentRevenue = end($currentMonth['gross'][1]);
		$dayDiff = $currentRevenue - $lastRevenue;
		$dayDiffPerct = 100 * $dayDiff/$lastRevenue;
		$lastMonthRevenue = array_sum($lastMonth['gross'][0]);
		$currentMonthRevenue = array_sum($currentMonth['gross'][1]);
		$monthDiff = $currentMonthRevenue - $lastMonthRevenue;
		$monthDiffPerct = 100 * $monthDiff/$lastMonthRevenue;
		if ($monthDiffPerct < 1) {
			$monthClass = 'negative';
		}
		if (count($lastMonth['gross'][0]) >= 7) {
			$lastWeek = array_splice($lastMonth['gross'][0], -7);
			$lastWeekTotal = array_sum($lastWeek);
			$currentWeek = array_splice($currentMonth['gross'][1], -7);
			$currentWeekTotal = array_sum($currentWeek);
			$weekDiff = $currentWeekTotal - $lastWeekTotal;
			$weekDiffPerct = 100 * $weekDiff/$lastWeekTotal;
			if ($weekDiffPerct < 1) {
				$weekClass = 'negative';
			}
		}
	?>
	<table id="gross_revenue_summary" class="" border="1">
		<thead>
			<tr>
				<th></th>
				<th><?php echo $lastMonthDesc?></th>
				<th><?php echo $currentMonthDesc?></th>
				<th>Diff ($)</th>
				<th>Diff (%)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Daily Total:</b></td>
				<td>$<?php echo number_format($lastRevenue, 2)?></td>
				<td>$<?php echo number_format($currentRevenue, 2)?></td>
				<td><?php echo number_format($dayDiff, 2)?></td>
				<td><?php echo number_format($dayDiffPerct, 2)?>%</td>
			</tr>
			<tr>
				<td><b>Last 7 Day Total:</b></td>
				<td>$<?php echo number_format($lastWeekTotal, 2)?></td>
				<td>$<?php echo number_format($currentWeekTotal, 2)?></td>
				<td><font class='<?php echo $weekClass?>'><?php echo number_format($weekDiff, 2)?></font></td>
				<td><font class='<?php echo $weekClass?>'><?php echo number_format($weekDiffPerct, 2)?>%</font></td>
			</tr>
			<tr>
				<td><b>Monthly Total:</b></td>
				<td>$<?php echo number_format($lastMonthRevenue, 2)?></td>
				<td>$<?php echo number_format($currentMonthRevenue, 2)?></td>
				<td><font class='<?php echo $monthClass?>'><?php echo number_format($monthDiff, 2)?></font></td>
				<td><font class='<?php echo $monthClass?>'><?php echo number_format($monthDiffPerct, 2)?>%</font></td>
			</tr>
		</tbody>
	</table>
</div>

<!--Net Revenue Summary Begins Here-->
<div class="clear"></div>
<br />
<hr />
<div class="grid_16">
	<h2>
		Net Revenue Summary
	</h2>
	<p>(After promocodes and credits are deducted)</p>
	<center>
		<?php echo $RevenueChart->renderChart()?>
	</center>
</div>
<div class="clear"></div>
<div class="grid_16">
	<?php
		$dayClass = $weekClass = $monthClass = 'positive';
		$lastRevenue = end($lastMonth['revenue'][0]);
		$currentRevenue = end($currentMonth['revenue'][1]);
		$dayDiff = $currentRevenue - $lastRevenue;
		$dayDiffPerct = 100 * $dayDiff/$lastRevenue;
		$lastMonthRevenue = array_sum($lastMonth['revenue'][0]);
		$currentMonthRevenue = array_sum($currentMonth['revenue'][1]);
		$monthDiff = $currentMonthRevenue - $lastMonthRevenue;
		$monthDiffPerct = 100 * $monthDiff/$lastMonthRevenue;
		if ($monthDiffPerct < 1) {
			$monthClass = 'negative';
		}
		if (count($lastMonth['revenue'][0]) >= 7) {
			$lastWeek = array_splice($lastMonth['revenue'][0], -7);
			$lastWeekTotal = array_sum($lastWeek);
			$currentWeek = array_splice($currentMonth['revenue'][1], -7);
			$currentWeekTotal = array_sum($currentWeek);
			$weekDiff = $currentWeekTotal - $lastWeekTotal;
			$weekDiffPerct = 100 * $weekDiff/$lastWeekTotal;
			if ($weekDiffPerct < 1) {
				$weekClass = 'negative';
			}
		}
	?>
	<table id="revenue_summary" class="" border="1">
		<thead>
			<tr>
				<th></th>
				<th><?php echo $lastMonthDesc?></th>
				<th><?php echo $currentMonthDesc?></th>
				<th>Diff ($)</th>
				<th>Diff (%)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Daily Total:</b></td>
				<td>$<?php echo number_format($lastRevenue, 2)?></td>
				<td>$<?php echo number_format($currentRevenue, 2)?></td>
				<td><?php echo number_format($dayDiff, 2)?></td>
				<td><?php echo number_format($dayDiffPerct, 2)?>%</td>
			</tr>
			<tr>
				<td><b>Last 7 Day Total:</b></td>
				<td>$<?php echo number_format($lastWeekTotal, 2)?></td>
				<td>$<?php echo number_format($currentWeekTotal, 2)?></td>
				<td><font class='<?php echo $weekClass?>'><?php echo number_format($weekDiff, 2)?></font></td>
				<td><font class='<?php echo $weekClass?>'><?php echo number_format($weekDiffPerct, 2)?>%</font></td>
			</tr>
			<tr>
				<td><b>Monthly Total:</b></td>
				<td>$<?php echo number_format($lastMonthRevenue, 2)?></td>
				<td>$<?php echo number_format($currentMonthRevenue, 2)?></td>
				<td><font class='<?php echo $monthClass?>'><?php echo number_format($monthDiff, 2)?></font></td>
				<td><font class='<?php echo $monthClass?>'><?php echo number_format($monthDiffPerct, 2)?>%</font></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="clear"></div>
<br />
<hr />
<div class="grid_16">
	<h2>
		Registration Summary
	</h2>
	<center>
		<?php echo $RegChart->renderChart()?>
	</center>
</div>
<div class="clear"></div>
<div class="grid_16">
	<?php
		$dayClass = $weekClass = $monthClass = 'positive';
		$lastRegistration= end($lastMonth['registration'][0]);
		$currentRegistration = end($currentMonth['registration'][1]);
		$dayDiff = $currentRegistration - $lastRegistration;
		$dayDiffPerct = 100 * $dayDiff/$lastRegistration;
		$lastMonthRegistration = array_sum($lastMonth['registration'][0]);
		$currentMonthRegistration = array_sum($currentMonth['registration'][1]);
		$monthDiff = $currentMonthRegistration - $lastMonthRegistration;
		$monthDiffPerct = 100 * $monthDiff/$lastMonthRegistration;
		if ($monthDiffPerct < 1) {
			$monthClass = 'negative';
		}
		if (count($lastMonth['registration'][0]) >= 7) {
			$lastWeek = array_splice($lastMonth['registration'][0], -7);
			$lastWeekTotal = array_sum($lastWeek);
			$currentWeek = array_splice($currentMonth['registration'][1], -7);
			$currentWeekTotal = array_sum($currentWeek);
			$weekDiff = $currentWeekTotal - $lastWeekTotal;
			$weekDiffPerct = 100 * $weekDiff/$lastWeekTotal;
			if ($weekDiffPerct < 1) {
				$weekClass = 'negative';
			}
		}
	?>
	<table id="registration_summary" class="" border="1">
		<thead>
			<tr>
				<th></th>
				<th><?php echo $lastMonthDesc?></th>
				<th><?php echo $currentMonthDesc?></th>
				<th>Diff (#)</th>
				<th>Diff (%)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Daily Total:</b></td>
				<td><?php echo number_format($lastRegistration, 0)?></td>
				<td><?php echo number_format($currentRegistration, 0)?></td>
				<td><?php echo number_format($dayDiff, 0)?></td>
				<td><?php echo number_format($dayDiffPerct, 2)?>%</td>
			</tr>
			<tr>
				<td><b>Last 7 Day Total:</b></td>
				<td><?php echo number_format($lastWeekTotal, 0)?></td>
				<td><?php echo number_format($currentWeekTotal, 0)?></td>
				<td><font class='<?php echo $weekClass?>'><?php echo number_format($weekDiff, 0)?></font></td>
				<td><font class='<?php echo $weekClass?>'><?php echo number_format($weekDiffPerct, 2)?>%</font></td>
			</tr>
			<tr>
				<td><b>Monthly Total:</b></td>
				<td><?php echo number_format($lastMonthRegistration, 0)?></td>
				<td><?php echo number_format($currentMonthRegistration, 0)?></td>
				<td><font class='<?php echo $monthClass?>'><?php echo number_format($monthDiff, 0)?></font></td>
				<td><font class='<?php echo $monthClass?>'><?php echo number_format($monthDiffPerct, 2)?>%</font></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="clear"></div>
<p>*Please note that dashboard is updated every 5 minutes.</p>
<br />
<hr />
<div class="grid_16">
	<h2>
		Monthly and YTD Summary
	</h2>
	<center>
	<table id="ytd_summary" class="" border="1" style="margin:20px;width:400px">
		<thead>
			<tr>
				<th></th>
				<th>Total Gross Revenue</th>
				<th>Total Registration</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Year to Date:</b></td>
				<?php foreach ($yearToDate as $data): ?>
					<?php if ($data['type'] == 'gross'): ?>
						<td>$<?php echo number_format($data['total'], 2)?></td>
					<?php endif ?>
				<?php endforeach ?>
				<?php foreach ($yearToDate as $data): ?>
					<?php if ($data['type'] == 'registration'): ?>
						<td><?php echo number_format($data['total'], 0)?></td>
					<?php endif ?>
				<?php endforeach ?>
			</tr>
		</tbody>
	</table>
	<?php echo $MonthComboChart->renderChart()?>
	</center>
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
						<td><?php echo date('m-d-Y', $registrationDetails[$i]->date->sec)?></td>
						<td><?php echo $registrationDetails[$i]->total?></td>
						<td>
						<?php if ($i <> 0): ?>
							<?php $percent = 100*($registrationDetails[$i]->total - $registrationDetails[$i-1]->total)/$registrationDetails[$i]->total;?>
							<?php echo number_format($percent, 2);?>%</td>
						<?php else: ?>
							-
						<?php endif?>
						</td>
						<td>$<?php echo number_format($revenuDetails[$i]->total, 2);?></td>
						<td>
						<?php if ($i <> 0): ?>
							<?php echo number_format(100*($revenuDetails[$i]->total - $revenuDetails[$i-1]->total)/$revenuDetails[$i]->total, 2);?>%</td>
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