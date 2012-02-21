<?=$this->html->script('jquery-1.4.2.min.js');?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>

<div class="grid_16">
	<div class='block' id="forms">
		<h5>
		</h5>
		<fieldset>
			<?=$this->form->create(null, array('id' => 'search_form'));?>
				<label> Ticket Type: </label>
				<?=$this->form->select('issue_type' , $issue_list, array('id' => 'issue_type'));?>

				<label>Search By:</label>
				<?=$this->form->select('search_by',array('','date' => 'date range', 'month'=>'month', 'email' => 'email', 'keyword' => 'keyword'), array('id' => 'search_by'));?>
				<span id="search_by_field"></span>

				<label>Limit:</label>
				<?=$this->form->select('limit_by',array('10' => '10','25'=>'25','50' => '50', '100'=>'100', '500' => '500'), array('id' => 'limit_by'));?>
				<?=$this->form->submit('Search');?>
			<?=$this->form->end();?>
		</fieldset>
	</div>
</div>
<div class='grid_16'>
	<h5>
		Please note that when you send the tickets to Liveperson, it is sent to them in the background. DO NOT REFRESH THE PAGE or you will send the tickets twice to Liveperson.
	</h5>
	<p style="font-size:13px">
	<strong>Query:</strong>: 
		<?php 
			echo "Issue type: " . $search_criteria['issue_type'] . "  Search by: " . $search_criteria['search_by'] . " ";
			switch($search_criteria['search_by']) {
				case 'email':
				case 'keyword':
					echo $search_criteria['search_by_value'];
					break;
				case 'month':
					echo date('F', mktime(0,0,0,(int)$search_criteria['search_by_value'] + 1));
					break;
				case 'date':
					echo $search_criteria['search_by_value']['start_date'] . " - " . $search_criteria['search_by_value']['end_date'];
					break;
				default:
					echo "none";
					break;
			};
		?>
		</p>
	<?php 
		if ($tickets) {
			$count = $tickets->count();
			echo "<h4>Results Found: " . $tickets->count() . "</h4>";	
		} else {
			echo "<h4>Results Found: 0 </h4>";	
		}
	?>
	<?=$this->form->create(null, array('id' => 'ticket_form'));?>
		
		<?php
			if($count != 0) {
				echo $this->form->button('Send selected to LivePerson', array('id' => 'liveperson', 'name' => 'send_button', 'value' => 'selected'));
				echo $this->form->button("Send all {$count} to LivePerson", array('id' => 'liveperson', 'name' => 'send_button','value' => 'all'));
			}
		?>
		&nbsp;&nbsp;
		Sort By : <?=$this->form->select('sort_by',array('user.email' => 'email', 'date_created' => 'date', 'status' => 'status', 'issue.issue_type' => 'issue'), array('value' => $sort_by));?>
		<?=$this->form->select('order_by',array('1' => 'ascending', '-1' => 'descending'),array('value' => $order_by));?>
		<?=$this->form->button('sort',array('value' => 'sort', 'name' => 'sort'));?>
		<div style='float: right'>
			 <?php $limit = round($count/(int)$search_criteria['limit_by']); ?>
			  <?php 
				  if ($limit <= 0){
				  	$limit = 1;
				  }
			  ?>
			 Page : <?=$getNext;?> of <?php echo $limit; ?>
			 <?php if ($getNext > 1):?>
				 	 <?=$this->form->button('Prev batch', array('value' => $getNext, 'name' => 'goBack'));?>
			<?php endif; ?>
			<?php if ($getNext < $limit):?>
				 	 <?=$this->form->button('Next batch', array('value' => $getNext, 'name' => 'getNext'));?>
			<?php endif; ?>
		</div>
		<div style="clear:both;"></div>
		<table>
			<thead>
				<th><?=$this->form->checkbox("checkall", array('id' => 'checkall'));?></th>
				<th>Issue Date</th>
				<th>Issue Type</th>
				<th>Sent By</th>
				<th>Subject</th>
				<th>Message</th>
				<th>Status</th>
			</thead>
			<tbody>
				<?php foreach($tickets as $ticket):?>
					<tr>
						<td><?=$this->form->checkbox("send[]", array('value' => $ticket['_id'], 'class' => 'send_ticket'));?></td>
						<td><?=date('m/d/Y H:i:s', $ticket['date_created']->sec);?></td>
						<td><?=$ticket['issue']['issue_type'];?>
						<td><?=$ticket['user']['email'];?></td>
						<td><?=$ticket['issue']['type'];?></td>						
						<td><?=$ticket['issue']['message'];?></td>
						<?php if (array_key_exists('status', $ticket)):?>
							<td><?=$ticket['status'];?></td>
						<?php else:?>
							<td>Might have been sent originally</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?=$this->form->end();?>
</div>

<script type="text/javascript">
$().ready(function(){
	if($(".send_ticket").checked) {
		$('#liveperson').removeAttr('disabled');
	} else {
		$('#liveperson').attr('disabled','disabled');
	}

	$('#search_by').change(function(){
		if($(this).val() == 'email'){
			$('#search_by_field').html("");	
			$('#search_by_field').append("<input type='text' name='email' value='enter email' />");
		} else if ($(this).val() == 'month'){
			$('#search_by_field').html("");	
			html = "<select name='month'>" +
				"<option value='0'>January</option>" +
				"<option value='1'>February</option>" +
				"<option value='2'>March</option>" +
				"<option value='3'>April</option>" +
				"<option value='4'>May</option>" +
				"<option value='5'>June</option>" +
				"<option value='6'>July</option>" +
				"<option value='7'>August</option>" +
				"<option value='8'>September</option>" +
				"<option value='9'>October</option>" +
				"<option value='10'>November</option>" +
				"<option value='11'>December</option>" +
				"</select>" ;
			$('#search_by_field').append(html);		
		} else if ($(this).val() == 'keyword') {
			$('#search_by_field').html("");	
			$('#search_by_field').append("<label>Keyword</label> <input type='text' name='keyword'/>");
		}else {
			$('#search_by_field').html("");	
			$('#search_by_field').append("<label>Start Date</label> <input type='text' name='start_date' class='date'>  <label>End Date</label>  <input type='text' name='end_date' class='date'>");
			jQuery(function($){
			 	$(".date").mask("99/99/9999");
			});	
		}
		
	});
	$(".send_ticket").change(function(){
		if(this.checked) {
			$('#liveperson').removeAttr('disabled');
		} else {
			$('#liveperson').attr('disabled','disabled');
		}
	});

	$("#checkall").click(function() {
		var checked_status = this.checked;
		$(".send_ticket").each(function()
		{
			this.checked = checked_status;
		});

		if(this.checked) {
			$('#liveperson').removeAttr('disabled');
		} else {
			$('#liveperson').attr('disabled','disabled');
		}
	});
});
</script>