<?php echo $this->html->script('jquery-1.4.2.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->style('TableTools');?>

<div class="grid_16">
	<h2 id="page-heading"> Promocode List</h2>
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
			<tr>
				<td><?php echo $this->html->link('Generate Promocodes', 'promocodes/generator'); ?></td>
			</tr>
		</tbody>
	</table>
</div>

<div class='grid_16 box'>

    <table id='codeSummary' class='datatable' >
        <thead>
            <tr>
                <th> Code </th>
                <th> Type </th>
                <th> Discount Amount</th>
                <th> Min. Purchase </th>
                <th>Start Date </th>
                <th> Expiration Date</th>
                <th> Created </th>
                <th> Created By: </th>
                <th> Enabled </th>
                 <th>   </th>
            </tr>
        </thead>
         <tbody>
            <?php foreach($promocodes as $promocode): ?>
                <tr>
                <td>
                   <?php echo $promocode->code; ?> <br>
                </td>
                <td>
                    <?php echo $promocode->type; ?><br>
                 </td>
                <td>
                    <?php if($promocode->type == 'percentage'): ?>
                        <?php echo ($promocode->discount_amount * 100); ?>%
                    <?php endif; ?>
                    <?php if($promocode->type == 'dollar'): ?>
                       $<?php echo $promocode->discount_amount; ?>
                    <?php endif; ?>
                     <?php if($promocode->type == 'shipping'): ?>
                       <?php echo $promocode->discount_amount; ?>
                    <?php endif; ?>
                </td>
                <td>
                    $<?php echo $promocode->minimum_purchase; ?>
                </td>
                <td>
                    <?php echo $promocode->start_date; ?>
                </td>
                <td>
                    <?php echo $promocode->end_date; ?>
                </td>
                <td>
                    <?php echo $promocode->date_created; ?>
                </td>

                <td>
                    <?php echo $promocode->created_by; ?>
                </td>
                <td>
                    <?php echo $promocode->enabled; ?>
                </td>

                <td>
                    <?php echo $this->html->link('edit', 'promocodes/edit/'.$promocode->_id); ?>
                </td>
                <tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#codeSummary').dataTable({
			"sDom": 'T<"clear">lfrtip',
			'bLengthChange' : false,
			"bPaginate": false
		}
		);
	} );
</script>
