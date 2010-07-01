<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->style('admin.css')?>
<?=$this->html->script('swfupload.js');?>
<?=$this->html->script('swfupload.queue.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('item_upload.js');?>
<?=$this->html->style('swfupload')?>

<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "simple"
});

</script>

<script type="text/javascript"> 
	$(document).ready(function(){	
		$("#itemDetails").dynamicForm("#plus", "#minus", {limit:15, createColor: 'yellow', removeColor: 'red'});
	});
</script>

<?=$this->html->link('See Item List','/items')?>

<h1>Add an Item</h1>

<?=$this->form->create(); ?>
    <?=$this->form->field('name', array('id' => 'name'));?>
    <?=$this->form->field('description', array('type' => 'textarea', 'id' => 'description'));?>
	<br>
	<fieldset>
		<legend>Price Details</legend>
	<?=$this->form->field('msrp', array('type' => 'text'));?>
	<?=$this->form->field('sale_retail', array('type' => 'text'));?>
	<?=$this->form->field('percent_off', array('type' => 'text'));?>
	<?=$this->form->field('original_wholesale', array('type' => 'text'));?>
	<?=$this->form->field('sale_wholesale', array('type' => 'text'));?>
	<?=$this->form->label('active')?>
	<input type="radio" name="active" value="Yes" id="Yes" checked ="checked">Yes
	<input type="radio" name="active" value="No" id="No">No
	<?=$this->form->field('vendor', array('type' => 'text'));?>
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
			<tr id='itemDetails'>
				<td><?=$this->form->text('vendor_sku');?></td>
				<td><?=$this->form->text('Category');?></td>
				<td><?=$this->form->text('color');?></td>
				<td><?=$this->form->text('size');?></td>
				<td><?=$this->form->text('inventory');?></td>
				<td><a id="minus" href="">[-]</a> <a id="plus" href="">[+]</a></td>
			</tr>
		</table>
	</fieldset>
<br>
<h1>Upload files for a particular item</h1>

<br>
<table>
	<tr valign="top">
		<td>
			<div>
				<div class="fieldset flash" id="fsUploadProgress1">
					<span class="legend">Upload Status</span>
				</div>
				<div style="padding-left: 5px;">
					<span id="spanButtonPlaceholder1"></span>
					<input id="btnCancel1" type="button" value="Cancel Uploads" onclick="cancelQueue(upload1);" disabled="disabled" style="margin-left: 2px; height: 22px; font-size: 8pt;" />
					<br />
				</div>
			</div>
		</td>
	</tr>
</table>
<div id="fileInfo"></div>
<br>

	
<?=$this->form->submit('Add/Update Item'); ?>
<?=$this->form->end(); ?>