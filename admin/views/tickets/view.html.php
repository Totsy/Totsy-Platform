<div class="grid_16">
	<div class='block' id="forms">
		<h5>
		</h5>
		<fieldset>
			<?=$this->form->create(null, array('id' => 'search_form'));?>
				<label> Ticket Type: </label>
				<?=$this->form->select('issue_type' , $issue_list, array('id' => 'issue_type'));?>

				<label>Search By:</label>
				<?=$this->form->select('search_by',array('','date' => 'date range', 'month'=>'month', 'email' => 'email'), array('id' => 'search_by'));?>
				<span id="search_by_field"></span>

				<label>Limit:</label>
				<?=$this->form->select('limit_by',array('10' => '10','25'=>'25','50' => '50', '100'=>'100'), array('id' => 'limit_by'));?>
				<?=$this->form->submit('Search');?>
			<?=$this->form->end();?>
		</fieldset>
	</div>
</div>
<div class='grid_16'>
	<strong>Search Criteria</strong>: 
	<?php 
		echo "Issue type: " . $search_criteria['issue_type'] . "  Search by: " . $search_criteria['search_by'] . " ";
		switch($search_criteria['search_by']) {
			case 'email':
				echo $search_criteria['search_by_value'];
				break;
				case 'month':
				
				echo date('F', mktime(0,0,0,(int)$search_criteria['search_by_value'] + 1));
				break;
			case 'date':
				echo $search_criteria['search_by_value'];
				break;
		};
	?>
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
				echo $this->form->submit('Send selected to LivePerson', array('id' => 'liveperson'));
				echo $this->form->submit("Send selected to all {$count} LivePerson", array('id' => 'liveperson'));
			}
		?>
		
		<table>
			<thead>
				<th><?=$this->form->checkbox("checkall", array('id' => 'checkall'));?></th>
				<th>Issue Date</th>
				<th>Issue Type</th>
				<th>Sent By</th>
				<th>Subject</th>
				<th>Message</th>
			</thead>
			<tbody>
				<?php foreach($tickets as $ticket):?>
					<tr>
						<td><?=$this->form->checkbox("send[]", array('value' => $ticket['_id'], 'class' => 'send_ticket'));?></td>
						<td><?=date('m/d/Y H:i:s', $ticket['date_created']->sec);?></td>
						<td><?=$ticket['issue']['issue_type'];?>
						<td><?=$ticket['user']['email'];?></td>
						<?php if($ticket['issue']['issue_type'] == "order"):?>
							<td><?=$this->html->link($ticket['issue']['type'],"/orders/view/{$ticket['issue']['type']}");?></td>
						<?php else:?>
							<td><?=$ticket['issue']['type'];?></td>
						<?php endif;?>
						
						<td><?=$ticket['issue']['message'];?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?=$this->form->end();?>
</div>

<script type="text/javascript">
$().ready(function(){
	if($(".send_ticket").checked) {
	//	$('#liveperson').removeAttr('disabled');
	} else {
	//	$('#liveperson').attr('disabled','disabled');
	}
});
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
	} else {
		$('#search_by_field').html("");	
		$('#search_by_field').append("<label>Start Date</label> <input type='text' name='start_date' class='date'>  <label>End Date</label>  <input type='text' name='start_date' class='date'>");	
	}
	
});
$(".send_ticket").change(function(){
	if($(this).checked) {
	//	$('#liveperson').removeAttr('disabled');
	} else {
	//	$('#liveperson').attr('disabled','disabled');
	}
});

$("#checkall").click(function() {
	var checked_status = this.checked;
	$(".send_ticket").each(function()
	{
		this.checked = checked_status;
	});
	if($(this).checked) {
	//	$('#liveperson').removeAttr('disabled');
	} else {
	//	$('#liveperson').attr('disabled','disabled');
	}
});
</script>