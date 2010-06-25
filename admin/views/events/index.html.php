<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>


<h1>Event Administration</h1>
<br>
<p>Edit events using the table below.</p>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
</script>
<br>
<?=$this->html->link('Create New Event','/events/add')?> 

<?=$this->events->build($events)?>