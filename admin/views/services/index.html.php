<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->style('table');?>
<h3>Service/Offers </h3>

<table id="services"class="dataTable">
    <th>Name</th>
    <th>Trigger Type</th>
    <th>Start Date</th>
    <th>End Date</th>
    <th>Enable</th>
    <th></th>
<?php
    foreach($services as $service):
?>
    <tr>
        <td><?php echo $service->name;?></td>
        <td><?php echo $triggers[$service->eligible_trigger->trigger_type];?></td>
        <td><?php echo date("m/d/Y", $service->start_date->sec);?></td>
        <td><?php echo date("m/d/Y", $service->end_date->sec);?></td>
        <td><?php echo $service->enabled;?></td>
        <td><?php echo $this->html->link('Edit', '/services/edit/'.$service->_id);?></td>
    </tr>
<?php endforeach; ?>
</table>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#services').dataTable({
			"sDom": 'T<"clear">lfrtip',
			'bLengthChange' : false,
			"bPaginate": true,
			"bFilter": true
		});
	} );
</script>