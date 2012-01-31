<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('timepicker'); ?>

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
        <?php echo $this->form->create($promoCode, array('id' => 'generateForm'));?>
                    <?php echo $this->form->label('Generate Amount:'); ?>
                    <?php echo $this->form->text('generate_amount');?> (must be more than 2)<br/>
                    <?php echo $this->form->error('generate_amount');?>
                    <?php echo $this->form->label('Enable:'); ?> <?php echo $this->form->checkbox('enabled', array('checked'=>'checked', 'value' => '1')); ?> <br>
                    <br>
                    <?php echo $this->form->label('Code:'); ?>
                    <?php echo $this->form->text('code'); ?><br>
                    <br>
                    <?php echo $this->form->label('Description:'); ?> <br>
                    <?php echo $this->form->textarea('description', array("width" =>50, "height" => 50 )); ?><br><br>
                   <?php echo $this->form->label('Code Type:'); ?>
                   <?php echo $this->form->select( 'type', array('percentage' => 'percent',  'dollar'=> 'dollar amount', 'shipping'=> 'shipping', 'free_shipping' => 'free shipping') ); ?><br><br>
                  <?php echo $this->form->label('Enter discount amount here:'); ?>
                   <?php echo $this->form->text( 'discount_amount'); ?><br>
                   <br>
                  <?php echo $this->form->label('Enter minimum purchase amount:'); ?>
                   <?php echo $this->form->text( 'minimum_purchase'); ?><br>
                    <br>
                  <?php echo $this->form->label('Enter maximum use:'); ?>
                   <?php echo $this->form->text( 'max_use'); ?><br>
                   <br>
                    <?php echo $this->form->hidden('max_total', array( 'value' => '1')); ?>

                  <?php echo $this->form->label('Enter start date:'); ?>
                  <?php echo $this->form->text( 'start_date', array('id' => 'start_date') ); ?><br>
                  <br>
                  <?php echo $this->form->label('Enter end date:'); ?>
                  <?php echo $this->form->text( 'end_date', array('id' => 'end_date') ); ?><br>
                  <br>
                  <?php echo $this->form->button('Generate', array('id' => 'submit_generate')); ?><br><br>
        <?php echo $this->form->end();?>
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