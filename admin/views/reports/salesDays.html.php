<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>
<?php echo $this->html->script('FusionCharts.js')?>
<div class="grid_16">
	<h2 id="page-heading">Reports - Sales By Days</h2>
</div>
<div class="clear"></div>
<div class="grid_6">
	<div class="box">
		<h2>
			<p>Search By Date</p>
		</h2>
		<div class="block" id="forms">
			<fieldset>
				<?php echo $this->form->create(); ?>
						<p>
							<?php echo $this->form->label('Minimum Order Date:'); ?>
							<?php echo $this->form->text('min_date', array(
								'id' => 'min_date',
								'class' => 'date',
								'style' => 'width:100px; margin: 0px 40px 0px 0px;'
								));
							?>
						</p>
						<p>
							<?php echo $this->form->label('Maximum Order Date:'); ?>
							<?php echo $this->form->text('max_date', array(
								'id' => 'max_date',
								'class' => 'date',
								'style' => 'width:100px; margin: 0px 40px 0px 0px;'
								));
							?>
						</p>
					<?php echo $this->form->submit('Search'); ?>
				<?php echo $this->form->end(); ?>
			</fieldset>
		</div>
	</div>
</div>
<?php if(!empty($DailyCharts)) : ?>
	
<?php $i = 0; ?>
<div class="grid_16">
	<center>
		<h3>Showing Events Sales by Days from <?php echo date('Y-m-d',$start_date)?> GMT to <?php echo date('Y-m-d',$end_date)?> GMT</h3>
	</center>
	<br /><br />
</div>
<div class="clear"></div>
<?php foreach($DailyCharts as $chart) : ?>
<div class="grid_16">
<h4>
	Sales starting on <?php echo $days[$i]?>
	<?php $i++; ?>
</h4>
<center>
	<?php echo $chart->renderChart()?>
</center></div>
<?php endforeach ?>
<?php endif ?>
<div class="clear"></div>
<script type="text/javascript">
jQuery(function($){
 	$(".date").mask("99/99/9999");
	$(".money").mask("999.99");
});
</script>