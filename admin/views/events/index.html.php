<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>



<div class="grid_16">
	<h2 id="page-heading">Event Administration</h2>
</div>
<br>
<p>Edit events using the table below.</p>

<?=$this->html->link('Create New Event','/events/add')?> 

<?=$this->events->build($events)?>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
</script>