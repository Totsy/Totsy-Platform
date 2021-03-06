<?php echo $this->html->script('jquery-1.4.2.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>

<div class="grid_16">
	<h2 id="page-heading">Select Event for <?php echo $type?> Administration
	<?php if ($environment == 'local'): ?>
		 - Dev Environment - Only Last 3 Months Events
	<?php endif ?>
	</h2>
</div>
<div class='clear'></div>
<div class='block' id="forms">
	<h5>Search for event(s) by name OR starting from a start date OR starting from a end date.  You can
		Also click "Show todays" to show events starting today and future events.
	</h5>
	<fieldset>
	<?php echo $this->form->create(); ?>
		Event Name:
			<?php echo $this->form->text('search' , array('id'=>'search')); ?>
			 &nbsp;&nbsp;&nbsp;
		Start date:
			<?php echo $this->form->text('start_date', array('id'=>'start_date','class'=>'date')); ?>
			&nbsp;
		End date:
			<?php echo $this->form->text('end_date', array('id'=>'end_date','class'=>'date') ); ?>
			&nbsp;&nbsp;
		Show Todays:
			<?php echo $this->form->checkbox('todays',array('value' => '1', 'id' => 'todays_checkbox'))?>
	   <?php echo $this->form->submit('Find', array('class' => 'float-right')); ?>
	<?php echo $this->form->end(); ?>
	</fieldset>
</div>
<br/>
<?php if(!empty($events)) :?>
	<div class="grid_16">
		<?php echo $this->events->build($events, array('type' => $type))?>
	</div>
<?php endif ?>
<div class='clear'></div>

<script type="text/javascript">
jQuery(function($){
 	$(".date").mask("99/99/9999");
});
</script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
	function filter() {
		$('#monthform').submit();
	};

	$('#todays_checkbox').change(function(){
		if ($('#todays_checkbox:checked').val() == '1'){
			$('#end_date').attr('disabled', 'disabled');
            $('#start_date').attr('disabled', 'disabled');
            $("#search").attr('disabled', 'disabled');

        }else{
            $('#end_date').removeAttr('disabled');
            $('#start_date').removeAttr('disabled');
            $("#search").removeAttr('disabled');
        }
	});
</script>