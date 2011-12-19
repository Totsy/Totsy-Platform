<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->script('jquery-ui-1.8.2.custom.min.js');?>


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
<div class='grid_4 box' >
    <h2>
		<a href="#" id="toggle-forms">Search Panel</a>
	</h2>
    <div class='block forms'>
	<p>For a detailed breakdown of the promocodes on the right please enter the promocode below. Use a date range to narrow down results.</p>
        <fieldset>
        <?php echo $this->form->create(); ?>
             code:
                <?php echo $this->form->text('search' , array('id'=>'start_end' , 'style' => 'width:280')); ?>
            <nbsp>  OR
                 <nbsp> <nbsp>
                start date range:
                <?php echo $this->form->text('start_date', array('id'=>'start_date')); ?>
                end date range:
                <?php echo $this->form->text('end_date', array('id'=>'end_date' ) ); ?>

           <?php echo $this->form->submit('find'); ?><br><br>
        <?php echo $this->form->end(); ?>

        </fieldset>
    </div>
</div>
<!--resut table starts here-->
<?php if (!empty($promocodes)): ?>

	<div class='grid_11 box'>
		<h2>
			<a href="#" id="toggle-forms">PromoCode Summary</a>
		</h2>
	    <div class='block forms'>
	    <table id='promo_list' class='datatable'>
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
						<?php if (!empty($promocode->code)): ?>
							<tr>
								<td> <?php echo $promocode->code; ?></td>
								<td> <?php echo $promocode->times_used; ?></td>
								<td> <?php echo $promocode->total_discounts; ?></td>
								<td> $<?php echo number_format($promocode->total_revenue,2); ?></td>
							</tr>
						<?php endif ?>

	                <?php endforeach; ?>

	        </tbody>
	    </table>
		</div>
	</div>
<?php endif ?>
<div class="clear"></div>
<?php if (!empty($promocodeDetail)): ?>

	<div class='grid_16 box'>
		<h2>
			<a href="#" id="toggle-forms">PromoCode Summary</a>
		</h2>
	    <div class='block forms'>
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
		               <?php foreach($promocodeDetail as $promocode): ?>
							<?php if (!empty($promocode->code)): ?>
								<tr>
									<td> <?php echo $promocode->code; ?></td>
									<td> <?php echo $promocode->times_used; ?></td>
									<td> <?php echo $promocode->total_discounts; ?></td>
									<td> $<?php echo number_format($promocode->total_revenue,2); ?></td>
								</tr>
							<?php endif ?>

		                <?php endforeach; ?>

		        </tbody>
		    </table>
		</div>
	</div>
<?php endif ?>
<?php if (!empty($promotions)): ?>

	<div class='grid_16 box'>
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
	                        <?php echo $this->html->link($promotion->code, 'promocodes/view/'.$promotion->code ); ?>
	                     </td>
	                     <td>
	                        <?php echo $this->html->link($promotion->user_id, 'users/view/'.$promotion->user_id, array('target' => '_blank')); ?>
	                     </td>
	                     <td>
	                        <?php echo $this->html->link($promotion->order_id, 'orders/view/'.$promotion->order_id, array('target' => '_blank') );?>
	                     </td>
	                     <td>
	                        $<?php echo number_format($promotion->saved_amount, 2 ); ?>
	                    </td>
	                    <td>
	                    <?php echo $promotion->date_created; ?>
	                    </td>
	                </tr>
	            <?php endforeach; ?>
	        </table>
	    </table>
	</div>
<?php endif ?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#promoSummary').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": true,
			"bFilter": false
		}
		);
		$('#promo_list').dataTable({
			"sDom": 'T<"clear">lfrtip',
			"bPaginate": true,
			"bFilter": false
		}
		);
	} );
</script>
