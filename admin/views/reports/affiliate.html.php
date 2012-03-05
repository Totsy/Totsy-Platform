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
	$(document).ready(function(){
		$('#ReportSearchType').change(function(){
			if($(this).val() == 'Bounces' && $('#subaffiliate_show_wrapper').is(':hidden') ){
				$('#subaffiliate_show_wrapper').show();
			} else if ($('#subaffiliate_show_wrapper').is(':visible')){
				$('#subaffiliate_show_wrapper').hide();
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
			<?php echo $this->form->create($search); ?>
				<p>
					<?php echo $this->form->label('Affiliate'); ?>
					<?php echo $this->form->text('affiliate'); ?>
				</p>
				    <?php
				        if(($criteria) && (bool)$criteria['subaffiliate']){
				            $checked = 'checked';
				        }else{
				            $checked = '';
				        }
				        if(($criteria) && (bool)$criteria['show_subaffiliate']){
				        	$show_checked = 'checked';
				        }else{
				        	$show_checked = '';
				        }
				    ?>
				    <?php echo $this->form->label('Subaffiliates included'); ?>  <?php echo $this->form->checkbox('subaffiliate', array('checked' => $checked, 'value' => '1'));?>
				    <br>
				<div style="display: inline" >
				    <div id="subaffiliate_show_wrapper" <?php if ($show_checked!='checked'){?>style="display: none;"<?php } ?>>
				    <?php echo $this->form->label('Show subaffiliate'); ?>  <?php echo $this->form->checkbox('show_subaffiliate', array('checked' => $show_checked, 'value' => '1'));?> <br/>
				    </div>
				</div>
				<p>
					<?php echo $this->form->label('Minimum Seach Date'); ?>
					<?php echo $this->form->text('min_date', array('id' => 'min_date'));?>
				</p>
				<p>
				<?php echo $this->form->label('Maximum Seach Date'); ?>
				<?php echo $this->form->text('max_date', array('id' => 'max_date'));?>
				</p>
				<p>
					<?php echo $this->form->label('Search Type'); ?>
					<?php echo $this->form->select('search_type', array(
						'Revenue' => 'Total Revenue',
						'Registrations' => 'Total Registrations',
						'Bounces' => 'Total Bounces',
						'Effective' => 'Effective Co-Reg'
						));
					?>
				</p>
				<?php echo $this->form->submit('Search'); ?>
			<?php echo $this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>
<div class="clear"></div>
<div class="grid_16">
    <?php if ($searchType == "Effective") :?>
    <p style="font-size:12px"><strong>Number in parentheses show number of people made at least one purchase</strong></p>
    <?php endif;?>
    <?=$this->affiliates->build($results,array("type" => $searchType,'criteria'=> $criteria)); ?>
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