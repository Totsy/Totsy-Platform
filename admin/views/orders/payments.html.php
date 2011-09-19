<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->style('TableTools');?>

<div>
<h2>Order Payment Status</h2>
<p style="font-size:12px">
    This feature allows you to see which orders payment captures failed, succeeded, or
	expiring within the next 3 days.  Search by Order Id <strong>OR</strong>
	by date range (end range defaults to current date) <strong>OR</strong> click
	<span style="text-decoration:underline">"Show Today's"</span> to see which orders
	failed to be captured today.  When using date range, select what you want to see in the
	<span style="text-decoration:underline">"I want to see"</span> section.
</p>
</div>

<div class='block' id="forms">
	<fieldset>
	<?=$this->form->create(null); ?>
		Order Id:
			<?=$this->form->text('search' , array('id'=>'search')); ?>
			 &nbsp;&nbsp;&nbsp;
		Start Range:
			<?=$this->form->text('start_date', array('id'=>'start_date','class'=>'date')); ?>
			&nbsp;
		End Range:
			<?=$this->form->text('end_date', array('id'=>'end_date','class'=>'date', 'value' => date('m/d/Y'))); ?>
			&nbsp;&nbsp;
		Show Today's:
			<?=$this->form->checkbox('todays',array('value' => '1', 'id' => 'todays_checkbox'))?>
		<br><br>
		<hr/>
		<strong>I want to see : </strong>

		<?=$this->form->radio('type',array('value' => 'error', 'id' => 'error'))?> Payment Errors (<strong>Requires Date Range</strong>)

		<?=$this->form->radio('type',array('value' => 'processed','id' => 'processed'))?> Payments Successes (<strong>Requires Date Range</strong>)

		<?=$this->form->radio('type',array('value' => 'expired', 'id' => 'expired'))?> Expiring (<strong>in 3 days</strong>)

	   <?=$this->form->submit('Find', array('class' => 'float-right')); ?>
	<?=$this->form->end(); ?>
	</fieldset>
</div>
<br/>

<div class="grid_16">
    <?php
        if ($type == 'expired') {
            echo $this->form->create(null, array('id' => "capture_form"));
            echo $this->form->submit('Capture',array('value' => 'Capture', 'id' => 'capture_button'));
        }
    ?>
    <?php echo $this->orders->build($payments, array('type' => $type)); ?>
     <?php
        if ($type == 'expired') {
          echo $this->form->end();
        }
    ?>
</div>

<script type="text/javascript">
jQuery(function($){
 	$(".date").mask("99/99/9999");
});
</script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
	    TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#paymentTable').dataTable({
			"sDom": 'T<"clear">lfrtip',
			'bLengthChange' : false,
			"bPaginate": false
		});
	});

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
	$('#expired').change(function(){
		if ($('#expired:checked').val()){
			$('#end_date').attr('disabled', 'disabled');
            $('#start_date').attr('disabled', 'disabled');
            $("#search").attr('disabled', 'disabled');
        }
	});
	$('#error').change(function(){
		if ($('#error:checked').val()){
			$('#end_date').removeAttr('disabled');
            $('#start_date').removeAttr('disabled');
            $("#search").removeAttr('disabled');
        }
	});
	$('#processed').change(function(){
		if ($('#processed:checked').val()){
			$('#end_date').removeAttr('disabled');
            $('#start_date').removeAttr('disabled');
            $("#search").removeAttr('disabled');
        }
	});
	$("#capture_all").click(function()
		{
			var checked_status = this.checked;
			$(".capture").each(function()
			{
				this.checked = checked_status;
			});
		});
	$(".capture").click(function() {
        var checked_status = this.checked;
        if ( $("#capture_all:checked").val()){
            $("#capture_all:checked").removeAttr("checked")
        }
    });
</script>