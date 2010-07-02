<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>


<h1>Product Item Administration</h1>
<br>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
</script>
<div id="note">
	<p>Select from the list below to edit an item. To create new items please select <?=$this->html->link('an event','/events')?> or create <?=$this->html->link('a new event','/events/add')?> first.</p>	
</div>

<br>
<?=$this->items->build($items); ?>





