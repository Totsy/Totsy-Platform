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
	<h2 id="page-heading">Promocode Edit Panel</h2>
</div>
<?php if ($promocode->parent): ?>
    <div class="grid_16 box">
        <h2><a href="#" id="toggle-tables">Directions</a></h2>
        <p style="font-size:13px">Editing this will change all associated Unique promocode created using the generator.<br/>
        <strong> Note: </strong> uniqueness has not been affected.</p>
        Use the <strong>Promo Search</strong> to search and disable individual promocodes.
    </div>
<?php endif;?>

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
<div class='grid_6 box'>
    <?php if ($promocode->parent): ?>
        <h2>
            <a href="#" id="toggle-forms">Edit Panel (Special)</a>
        </h2>
	<?php else: ?>
        <h2>
            <a href="#" id="toggle-forms">Edit Panel</a>
        </h2>
	<?php endif;?>
    <div class='block' id='forms'>
        <fieldset>
        <?php echo $this->form->create(); ?>
            <?php  $enable= (($promocode->enabled))? 'checked' : '' ?>
			Enable: <?php echo $this->form->checkbox( 'enabled', array( 'checked'=>$enable, 'value' => '1' ) ); ?> <br>
			<?php if ($promocode->parent): ?>
                Number of associated promocodes: <?php echo $promocode->no_of_promos;?> <br/>
                Retrieve promocodes :  <?=$this->html->link('Retrieve Promocodes',"promocodes/massPromocodes/{$promocode->_id}");?>
                <br/>
            <?php endif; ?>
           Code: <?php echo $this->form->text('code', array( 'value' => $promocode->code ) ); ?><br>

          Description: <br>
          <?php echo $this->form->textarea('description', array( 'value' => $promocode->description ) ); ?><br>

          Code Type:
           <?php echo $this->form->select('type', array('percentage' => 'percent',  'dollar'=> 'dollar amount', 'shipping'=> 'shipping', 'free_shipping'=> 'free shipping'), array('id' => 'type' , 'value' => $promocode->type) ); ?><br>

			<div id="discount"
				<?php if($promocode->type == 'free_shipping') : ?>
					style="display: none;"
				<?php endif ?>>
				<?php echo $this->form->label('Discount Amount:'); ?>
				<?php echo $this->form->text('discount_amount', array( 'value' => $promocode->discount_amount)); ?><br>
			</div>
           <?php echo $this->form->label('Minimum Purchase:'); ?>
           <?php echo $this->form->text('minimum_purchase', array( 'value' => $promocode->minimum_purchase)); ?><br>

           <?php  $enable = (($promocode->limited_use))? 'checked' : '' ?>
			<?php echo $this->form->label('Assign by email:'); ?>
			<?php echo $this->form->checkbox( 'limited_use', array( 'checked'=> $enable, 'value' => '1' ) ); ?> <br>

           <?php echo $this->form->label('Enter maximum individual use:'); ?>
           <?php echo $this->form->text( 'max_use', array( 'value' => $promocode->max_use) ); ?><br><br>

            <?php echo $this->form->label('Enter maximum number people who can use it (if unlimited type in UNLIMITED)');?>
            <?php echo $this->form->text('max_total', array( 'value' => $promocode->max_total)); ?> <br>

           <?php echo $this->form->label('Start Date:'); ?>
           <?php echo $this->form->text('start_date', array('value' => $promocode->start_date, 'id'=>'start_date')); ?><br>

           <?php echo $this->form->label('Expiration Date:'); ?>
           <?php echo $this->form->text('end_date', array('value' => $promocode->end_date, 'id'=>'end_date')); ?><br>

           <?php echo $this->form->submit('update'); ?>

        <?php echo $this->form->end(); ?>
        </fieldset>
    </div>
</div>
<?php if ($promocode->parent): ?>
<div class="grid_6 box">
    <h2>
        <a href="#" id="toggle-forms">Promo Search</a>
    </h2>
    <div class="block">
        This section is used to deactivate SPECIFIC promocodes related to <?=$promocode->code?>.
        <?=$this->form->create(null, array('id' => 'searchForm'));?>
            <?=$this->form->label("Search Codes");?>
            <?=$this->form->text('code_search');?>
            <?=$this->html->link('Find',"#", array('id' => 'search', 'target' => '#codes'));?>
        <?=$this->form->end();?>
    </div>
    <div class="block">
        <span id="codes"></span>
    </div>
</div>
<?php endif; ?>
<script type="text/javascript" >
$('#type').change(function() {
	if($('#type').val() == 'free_shipping') {
		$("#discount").hide("slow");
	} else {
		$("#discount").show("slow");
	};
});

$('#search').click(function(){
    var code_search = $("input[name=code_search]").val();
    var parent_id = "<?=$promocode->_id;?>";
    var dataString = 'code_search='+ code_search + '&parent_id=' + parent_id;
    var item = $(this);
	var target = $(item.attr('target'));

    $.ajax({
      type: "POST",
      url: "/promocodes/findPromo",
      data: dataString,
      success: function(data) {
        target.html(data);
      }
    });
    return false;
});
</script>