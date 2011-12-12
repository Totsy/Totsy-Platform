<?php echo $this->html->script('jquery-1.4.2.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->style('table');?>

<div class="grid_16">
	<h2 id="page-heading">Select Banners for Administration</h2>
</div>
<div class='clear'></div>
<div class="grid_16">
	<?php echo $this->banners->build($banners)?>
</div>
<div class='clear'></div>


<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
</script>