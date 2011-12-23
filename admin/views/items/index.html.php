<?php echo $this->html->script(array('jquery-1.4.2.min.js', 'jquery.dataTables.js'));?>
<?php echo $this->html->style('table');?>


<h1>Product Item Administration</h1>
<br>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
</script>
<div id="note">
	<p>Select from the list below to edit an item. To create new items please select <?php echo $this->html->link('an event','/events')?> or create <?php echo $this->html->link('a new event','/events/add')?> first.</p>	
</div>

<br>
<?php echo $this->items->build($items); ?>





