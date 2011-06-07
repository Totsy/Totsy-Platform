<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>

<script type="text/javascript" charset="utf-8">
	$(function() {
		var dates = $('#start_date, #end_date').datetimepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				var option = this.id == "start_date" ? "minDate" : "maxDate";
				var instance = $(this).data("datetimepicker");
				var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);
			}
		});
	});
</script>

<div class="grid_16">
	<h2 id="page-heading">Promocode Add Panel</h2>
</div>
<div class='grid_3 menu'>
	<table>
		<thead>
			<tr>
				<th>Promo Navigation </th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td> <?php echo $this->html->link('Create Promocode', 'promocodes/add'); ?> </td>
			</tr>
			<tr>
				<td><?php echo $this->html->link('View/Edit Promocodes', 'promocodes/index' ); ?></td>
			</tr>
			<tr>
				<td><?php echo $this->html->link('View Promotions', 'promocodes/report'); ?></td>
			</tr>
		</tbody>
	</table>
</div>

<div class='grid_6 box '>
    <h2>
		<a href="#" id="toggle-forms">Add Panel</a>
	</h2>
    <div class= 'block forms' >
        <fieldset>
            <?=$this->form->create(); ?>
                <?=$this->form->label('Enable:'); ?> <?=$this->form->checkbox('enabled', array('checked'=>'checked', 'value' => '1')); ?> <br>
                <br>
                <?=$this->form->label('Code:'); ?>
                <?=$this->form->text('code', array('value' => 'Enter code')); ?><br>
                <br>
                <?=$this->form->label('Description:'); ?> <br>
                <?=$this->form->textarea('description', array('value' => 'Enter description here')); ?><br><br>

               <?=$this->form->label('Code Type:'); ?>
               <?=$this->form->select( 'type', array('percentage' => 'percent',  'dollar'=> 'dollar amount', 'shipping'=> 'shipping', 'free_shipping' => 'free shipping') ); ?><br><br>
				<div id="discount">
					<?=$this->form->label('Enter discount amount here:'); ?>
					<?=$this->form->text( 'discount_amount', array( 'value' => 'Enter discount amount here') ); ?><br><br>
				</div>
              <?=$this->form->label('Enter minimum purchase amount:'); ?>
               <?=$this->form->text( 'minimum_purchase', array( 'value' => 'Enter minimum purchase') ); ?><br><br>

               <?=$this->form->label('Assign by email:'); ?> <?=$this->form->checkbox('limited_use', array('value' => '1')); ?> <br>
                <br>
              <?=$this->form->label('Enter maximum individual use:'); ?>
              <?=$this->form->text( 'max_use', array( 'value' => 'Enter max use') ); ?><br><br>

              <?=$this->form->label('Enter maximum number people who can use it (if unlimited type in UNLIMITED)');?>
              <?=$this->form->text('max_total', array( 'value' => 'UNLIMITED')); ?>

              <?=$this->form->label('Enter start date:'); ?>
              <?=$this->form->text( 'start_date', array('value' => 'Enter start date here', 'id' => 'start_date') ); ?><br><br>

              <?=$this->form->label('Enter end date:'); ?>
              <?=$this->form->text( 'end_date', array('value' => 'Enter end date here', 'id' => 'end_date') ); ?><br><br>

              <?=$this->form->submit('create'); ?><br><br>

            <?=$this->form->end(); ?>
        </fieldset>
    </div>
</div>
<script type="text/javascript" >
$('#Type').change(function() {
	if($('#Type').val() == 'free_shipping') {
		$("#discount").hide("slow");
	} else {
		$("#discount").show("slow");
	};
});
</script>
