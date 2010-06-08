<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->style('admin.css')?>

<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "simple"
});

</script>

<script type="text/javascript"> 
	$(document).ready(function(){	
		$("#duplicate").dynamicForm("#plus", "#minus", {limit:15, createColor: 'yellow', removeColor: 'red'});
	});
</script>
<h1>Add an Item</h1>

<?=$this->form->create(); ?>
    <?=$this->form->field('Name');?>
    <?=$this->form->field('Description', array('type' => 'textarea', 'name' => 'content'));?>
	<br>
	<fieldset>
		<legend>Price Details</legend>
	<?=$this->form->field('Original Price', array('type' => 'text'));?>
	<?=$this->form->field('Sale Price', array('type' => 'text'));?>
	<input type="radio" name="Active" value="Yes" id="Yes" checked ="checked">Yes
	<input type="radio" name="Active" value="No" id="No">No
	</fielset>
	<br>
	<fieldset> 
		<legend>Item Details</legend> 		
		<table border="0" cellspacing="5" cellpadding="5">
			<tr>
				<th>SKU</th>
				<th>Color</th>
				<th>Weight</th>
				<th>Size</th>
				<th>Inventory</th>
			</tr>
			<tr id='duplicate'>
				<td><?=$this->form->text('SKU');?></td>
				<td><?=$this->form->text('Color');?></td>
				<td><?=$this->form->text('Weight');?></td>
				<td><?=$this->form->text('Size');?></td>
				<td><?=$this->form->text('Inventory');?></td>
				<td><a id="minus" href="">[-]</a> <a id="plus" href="">[+]</a></td>
			</tr>
		</table>
	</fieldset>
<br>
	
	<?=$this->form->submit('Add/Update Item'); ?>
<?=$this->form->end(); ?>