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
	<h2 id="page-heading">Promocode Edit Panel</h2>
</div>

<div class='grid_6 box'>
    <h2>
		<a href="#" id="toggle-forms">Edit Panel</a>
	</h2>
    <div class='block' id='forms'>
        <fieldset>
        <?=$this->form->create(); ?>
         Enable: <?=$this->form->checkbox( 'enabled', array( 'checked'=>'checked', 'value' => '1' ) ); ?> <br>
                    
           Code: <?=$this->form->text('code', array( 'value' => $promocode->code ) ); ?><br>
           
          Description: <br>
          <?=$this->form->textarea('description', array( 'value' => $promocode->description ) ); ?><br>
          
          Code Type:
           <?=$this->form->select('type', array('percentage' => 'percent',  'dollar'=> 'dollar amount', 'shipping'=> 'shipping'), array('id' => 'type' , 'value' => $promocode->type) ); ?><br>
           
           <?=$this->form->label('Discount Amount:'); ?>
           <?=$this->form->text('discount_amount', array( 'value' => $promocode->discount_amount)); ?><br>
           
           <?=$this->form->label('Minimum Purchase:'); ?>
           <?=$this->form->text('minimum_purchase', array( 'value' => $promocode->minimum_purchase)); ?><br>
           
           <?=$this->form->label('Enter maximum uses:'); ?>
           <?=$this->form->text( 'max_use', array( 'value' => $promocode->max_use) ); ?><br><br>   
           
           <?=$this->form->label('Start Date:'); ?>
           <?=$this->form->text('start_date', array('value' => $promocode->start_date, 'id'=>'start_date')); ?><br>
           
           <?=$this->form->label('Expiration Date:'); ?>
           <?=$this->form->text('end_date', array('value' => $promocode->end_date, 'id'=>'end_date')); ?><br>
              
           <?=$this->form->submit('update'); ?>
        
        <?=$this->form->end(); ?>
        </fieldset>
    </div>
</div>