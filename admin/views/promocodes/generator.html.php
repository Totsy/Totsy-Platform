<?php $data = $this->_data;?>
<?php if(!array_key_exists('codes', $data)): ?>
    <?=$this->html->script('jquery-ui-timepicker.min.js');?>
    <?=$this->html->style('jquery_ui_blitzer.css')?>
    <?=$this->html->style('timepicker'); ?>

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
    <h2>Promocode Generator</h2>
    <div class= "grid_16">
        <h5>This functionality is used to generate large amounts of unique promocodes at one time.<br/>
        When the promocode is given to a certain individual, it can only be used by individual. </h5>
    </div>
     <br/>
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
                    <tr>
                        <td><?php echo $this->html->link('Generate Promocodes', 'promocodes/generator'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <div class="grid_7 box">

        <h2>Generate Promocodes</h2>
        <?=$this->form->create($promoCode);?>
                    <?=$this->form->label('Generate Amount:'); ?>
                    <?=$this->form->text('generate_amount');?> (must be more than 2)<br/>
                    <?=$this->form->error('generate_amount');?>
                    <?=$this->form->label('Enable:'); ?> <?=$this->form->checkbox('enabled', array('checked'=>'checked', 'value' => '1')); ?> <br>
                    <br>
                    <?=$this->form->label('Code:'); ?>
                    <?=$this->form->text('code'); ?><br>
                    <br>
                    <?=$this->form->label('Description:'); ?> <br>
                    <?=$this->form->textarea('description', array("width" =>50, "height" => 50 )); ?><br><br>
                   <?=$this->form->label('Code Type:'); ?>
                   <?=$this->form->select( 'type', array('percentage' => 'percent',  'dollar'=> 'dollar amount', 'shipping'=> 'shipping', 'free_shipping' => 'free shipping') ); ?><br><br>
                  <?=$this->form->label('Enter discount amount here:'); ?>
                   <?=$this->form->text( 'discount_amount'); ?><br>
                   <br>
                  <?=$this->form->label('Enter minimum purchase amount:'); ?>
                   <?=$this->form->text( 'minimum_purchase'); ?><br>
                    <br>
                  <?=$this->form->label('Enter maximum use:'); ?>
                   <?=$this->form->text( 'max_use'); ?><br>
                   <br>
                    <?=$this->form->hidden('max_total', array( 'value' => 1)); ?>
                  <?=$this->form->label('Enter start date:'); ?>
                  <?=$this->form->text( 'start_date', array('id' => 'start_date') ); ?><br>
                  <br>
                  <?=$this->form->label('Enter end date:'); ?>
                  <?=$this->form->text( 'end_date', array('id' => 'end_date') ); ?><br>
                  <br>
                  <?=$this->form->submit('Generate'); ?><br><br>
        <?=$this->form->end();?>
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
<?php else:
    foreach($data['codes'] as $code){
			print_r($code);
		    echo( "\n" );
	}
	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=\"generatedcodes.csv\"");
endif;
?>