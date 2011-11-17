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
<div class="grid_6">
    <br/>
	<div class="box">
	<h2>
		<a href="#" id="toggle-forms">Query for Affiliate Order/Count Totals</a>
	</h2>
	<div class="block" id="forms">
		<fieldset>
			<?=$this->form->create($search); ?>
				<p>
					<?=$this->form->label('Affiliate'); ?>
					<?=$this->form->text('affiliate'); ?>
				</p>
				    <?php
				        if(($criteria) && (bool)$criteria['subaffiliate']){
				            $checked = 'checked';
				        }else{
				            $checked = '';
				        }
				    ?>
				    <?=$this->form->label('Subaffiliates included'); ?>  <?=$this->form->checkbox('subaffiliate', array('checked' => $checked, 'value' => '1'));?> <br/>
				<p>
					<?=$this->form->label('Minimum Seach Date'); ?>
					<?=$this->form->text('min_date', array('id' => 'min_date'));?>
				</p>
				<p>
				<?=$this->form->label('Maximum Seach Date'); ?>
				<?=$this->form->text('max_date', array('id' => 'max_date'));?>
				</p>
				<p>
					<?=$this->form->label('Search Type'); ?>
					<?=$this->form->select('search_type', array(
						'Revenue' => 'Total Revenue',
						'Registrations' => 'Total Registrations',
						'Bounces' => 'Total Bounces',
						'Effective' => 'Effective Co-Reg'
						));
					?>
				</p>
				<?=$this->form->submit('Search'); ?>
			<?=$this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>
<div class="clear"></div>
<div class="grid_16">
    <?php if ($searchType == "Effective") :?>
    <p style="font-size:12px"><strong>Number in parentheses show number of people made at least one purchase</strong></p>
    <?php endif;?>
    <?=$this->affiliates->build($results,array("type" => $searchType)); ?>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#report').dataTable({
			"sDom": 'T<"clear">lfrtip',
			'bLengthChange' : false,
			"bPaginate": false
		}
		);
	} );
</script>