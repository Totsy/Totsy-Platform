<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>



<div class="grid_16">
	<h2 id="page-heading">Logistic Administration</h2>
</div>
<div class='clear'></div>
<div class="grid_16">
	<?=$this->events->build($events, array('type' => 'logistics'))?>
</div>
<div class='clear'></div>


<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
</script>