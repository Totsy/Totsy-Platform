<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>

<div class='block' id="forms">

	<fieldset>
	<?=$this->form->create(); ?>
		Order Id:
			<?=$this->form->text('search' , array('id'=>'search')); ?>
			 &nbsp;&nbsp;&nbsp;
		Start date:
			<?=$this->form->text('start_date', array('id'=>'start_date','class'=>'date', 'value' => '07/01/2011')); ?>
			&nbsp;
		End date:
			<?=$this->form->text('end_date', array('id'=>'end_date','class'=>'date') ); ?>
			&nbsp;&nbsp;
		Show Todays:
			<?=$this->form->checkbox('todays',array('value' => '1', 'id' => 'todays_checkbox'))?>
		<br><br>
		<strong>I want to see : </strong>

		<?=$this->form->radio('type',array('value' => 'error'))?> Payment Errors

		<?=$this->form->radio('type',array('value' => 'processed'))?> Payments Successes

		 <?=$this->form->radio('type',array('value' => 'expired'))?> Overdue Payments

	   <?=$this->form->submit('Find', array('class' => 'float-right')); ?>
	<?=$this->form->end(); ?>
	</fieldset>
</div>
<br/>

<div class="grid_16">
    <?php echo $this->orders->build($payments, array('type' => $type)); ?>
</div>

<script type="text/javascript">
jQuery(function($){
 	$(".date").mask("99/99/9999");
});
</script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#paymentTable').dataTable();
	} );

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