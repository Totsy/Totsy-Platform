<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>


<h1>Product Item Administration</h1>
<br>
<p>Edit items using the table below. Administrators are allowed to edit, delete and append media to items.</p>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
</script>
<br>
<p>
	Todo: <br>
		Add link for modal item addition.<br>
		Add deletion of items.<br>
		Add media add and delete.<br>
	
</p>
<br>

<?php echo $htmlTable?>




