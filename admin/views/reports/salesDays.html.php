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
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<?=$this->html->script('FusionCharts.js')?>
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
				<?=$this->form->create(); ?>
						<p>
							<?=$this->form->label('Minimum Order Date:'); ?>
							<?=$this->form->text('min_date', array(
								'id' => 'min_date',
								'class' => 'date',
								'style' => 'width:100px; margin: 0px 40px 0px 0px;'
								));
							?>
						</p>
						<p>
							<?=$this->form->label('Maximum Order Date:'); ?>
							<?=$this->form->text('max_date', array(
								'id' => 'max_date',
								'class' => 'date',
								'style' => 'width:100px; margin: 0px 40px 0px 0px;'
								));
							?>
						</p>
					<?=$this->form->submit('Search'); ?>
				<?=$this->form->end(); ?>
			</fieldset>
		</div>
	</div>
</div>
<?php if(!empty($DailyCharts)) : ?>
	
<?php $i = 0; ?>
<div class="grid_16">
	<center>
		<h3>Showing Events Sales by Days from <?=date('Y-m-d',$start_date)?> GMT to <?=date('Y-m-d',$end_date)?> GMT</h3>
	</center>
	<br /><br />
</div>
<div class="clear"></div>
<?php foreach($DailyCharts as $chart) : ?>
<div class="grid_16">
<h4>
	Sales starting on <?=$days[$i]?>
	<?php $i++; ?>
</h4>
<center>
	<?=$chart->renderChart()?>
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