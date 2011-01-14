<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>


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

<br>
<br>
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


<div class="grid_16">
	<h2 id="page-heading">Promotions Report</h2>
</div>

<!--search starts here-->
<div class='grid_6 box' >
    <h2>
		<a href="#" id="toggle-forms">Search Panel</a>
	</h2>
    <div class='block forms'>
        <fieldset>
        <?=$this->form->create(); ?>
             code:
                <?=$this->form->text('search' , array('id'=>'start_end' , 'style' => 'width:280')); ?>
            <nbsp>  OR 
                 <nbsp> <nbsp>
                start date range:
                <?=$this->form->text('start_date', array('id'=>'start_end' , 'style' => 'width:180')); ?> 
                end date range:
                <?=$this->form->text('end_date', array('id'=>'end_date' , 'style' => 'width:180') ); ?>
            
           <?=$this->form->submit('find'); ?><br><br>
        <?=$this->form->end(); ?>
        
        </fieldset>
    </div>
</div>

<br>

<!--resut table starts here-->

<div class = 'grid_16'>
    <table>
        <thead>
            <tr>
                <th> Promocode </th>
                <th> No. Of Uses </th>
                <th> Total Discounts</th>
                <th> Total Revenue </th>
            </tr>
        </thead>
        <tbody>
            
               <?php foreach($promocodes as $promocode): ?>
                    <tr>
                        <td> <?=$promocode->code; ?></td>
                        <td> <?=$promocode->times_used; ?></td>
                        <td> <?=$promocode->total_discounts; ?></td>
                        <td> $<?=$promocode->total_revenue; ?></td>
                    </tr>
                <?php endforeach; ?>
            
        </tbody>
    </table>
</div>


<div class='grid_16'>
    <table id='promoSummary' class='datatable'>
        <thead>
            <tr>
                <th> Promocode </th>
                <th> Userid</th>
                <th> Order </th>
                <th> Amount Saved </th>
                <th> Created on </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $promotions as $promotion ): ?>
                <tr>
                    <td>
                        <?=$this->html->link($promotion->code, 'promocodes/view/'.$promotion->code ); ?>
                     </td>
                     <td>
                        <?=$this->html->link($promotion->user_id, 'users/view/'.$promotion->user_id, array('target' => '_blank')); ?>
                     </td>
                     <td>
                        <?=$this->html->link($promotion->order_id, 'orders/view/'.$promotion->order_id, array('target' => '_blank') );?>
                     </td>
                     <td>
                        $<?php echo round($promotion->saved_amount, 2 ); ?>
                    </td>
                    <td>
                    <?=$promotion->date_created; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </table>
</div>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#promoSummary').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": true,
			"bFilter": false
		}
		);
	} );
</script>
