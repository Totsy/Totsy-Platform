<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>

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
            <?php echo $this->form->create(); ?>
                <?php echo $this->form->label('Enable:'); ?> <?php echo $this->form->checkbox('enabled', array('checked'=>'checked', 'value' => '1')); ?> <br>
                <br>
                <?php echo $this->form->label('Code:'); ?>
                <?php echo $this->form->text('code', array('value' => 'Enter code')); ?><br>
                <br>
                <?php echo $this->form->label('Description:'); ?> <br>
                <?php echo $this->form->textarea('description', array('value' => 'Enter description here')); ?><br><br>

               <?php echo $this->form->label('Code Type:'); ?>
               <?php echo $this->form->select( 'type', array('percentage' => 'percent',  'dollar'=> 'dollar amount', 'shipping'=> 'shipping', 'free_shipping' => 'free shipping') ); ?><br><br>
				<div id="discount">
					<?php echo $this->form->label('Enter discount amount here:'); ?>
					<?php echo $this->form->text( 'discount_amount', array( 'value' => 'Enter discount amount here') ); ?><br><br>
				</div>
              <?php echo $this->form->label('Enter minimum purchase amount:'); ?>
               <?php echo $this->form->text( 'minimum_purchase', array( 'value' => 'Enter minimum purchase') ); ?><br><br>

               <?php echo $this->form->label('Assign by email:'); ?> <?php echo $this->form->checkbox('limited_use', array('value' => '1')); ?> <br>
                <br>
              <?php echo $this->form->label('Enter maximum individual use:'); ?>
              <?php echo $this->form->text( 'max_use', array( 'value' => 'Enter max use') ); ?><br><br>

              <?php echo $this->form->label('Enter maximum number people who can use it (if unlimited type in UNLIMITED)');?>
              <?php echo $this->form->text('max_total', array( 'value' => 'UNLIMITED')); ?>

              <?php echo $this->form->label('Enter start date:'); ?>
              <?php echo $this->form->text( 'start_date', array('value' => 'Enter start date here', 'id' => 'start_date') ); ?><br><br>

              <?php echo $this->form->label('Enter end date:'); ?>
              <?php echo $this->form->text( 'end_date', array('value' => 'Enter end date here', 'id' => 'end_date') ); ?><br><br>

              <?php echo $this->form->submit('create'); ?><br><br>

            <?php echo $this->form->end(); ?>
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
