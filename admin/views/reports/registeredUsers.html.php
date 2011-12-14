<?php $datas = $this->_data?>
<?php if(empty($datas[0]["email"])){ ?>
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
<script type="text/javascript" charset="utf-8">
	$(function() {
		var dates = $('#min_date, #max_date').datetimepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				var option = this.id == "min_date" ? "minDate" : "maxDate";
				var instance = $(this).data("datetimepicker");
				var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);
			}
		});
	});
</script>
<div class="grid_16">
	<h2 id="page-heading">Registered - Users</h2>
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
							<?php echo $this->form->label('Minimum Registration Date'); ?>
							<?php echo $this->form->text('min_date', array('id' => 'min_date'));?>
						</p>
						<p>
						<?php echo $this->form->label('Maxium Registration Date'); ?>
						<?php echo $this->form->text('max_date', array('id' => 'max_date'));?>
					<?php echo $this->form->submit('Search'); ?>
				<?php echo $this->form->end(); ?>
			</fieldset>
		</div>
	</div>
</div>
<?php }else {
	foreach($datas as $user){
		foreach($user as $info){
			print_r($info);
		}
		echo( "\n" );
	}
	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=\"registeredUsers.csv\"");
}
?>