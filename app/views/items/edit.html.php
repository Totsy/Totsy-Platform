<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->style('admin.css')?>
<?=$this->html->script('swfupload.js');?>
<?=$this->html->script('swfupload.queue.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('upload.js');?>
<?=$this->html->style('swfupload')?>

<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "simple"
});

</script>

<script type="text/javascript"> 
	$(document).ready(function(){	
		var mainDynamicForm = $("#itemDetails").dynamicForm("#plus", "#minus", {data:<?php echo $details; ?>, limit:15, createColor: 'yellow', removeColor: 'red'});
		mainDynamicForm.inject(data);
	});
</script>

<?=$this->html->link('See Item List','/items')?>
<h1>Edit Item</h1>
<?=$this->form->create(); ?>
	<?=$this->form->field('', array('value' => $item->_id, 'type' => 'hidden', 'id' => '_id', 'name' => '_id'));?>
	<input type="hidden" name="_id" value="<?=$item->_id;?>" id="_id">
    <?=$this->form->field('name', array('value' => $item->name));?>
    <?=$this->form->field('description', array('type' => 'textarea', 'name' => 'content', 'value' => $item->description));?>
	<br>
	<fieldset>
		<legend>Price Details</legend>
	<?=$this->form->field('original_price', array('type' => 'text', 'value' => $item->original_price, 'id' => ''));?>
	<?=$this->form->field('sale_price', array('type' => 'text', 'value' => $item->sale_price));?>
	<?=$this->form->label('active')?>
	<input type="radio" name="active" value="Yes" id="Yes" checked ="checked">Yes
	<input type="radio" name="active" value="No" id="No">No
	<?=$this->form->field('vendor', array('type' => 'text','value' => $item->Vendor));?>
	</fielset>
	<br>
	<fieldset>
		<span class="legend">Item Details</span> 		
		<table border="0" cellspacing="5" cellpadding="5">
			<tr>
				<th>SKU</th>
				<th>Color</th>
				<th>Weight</th>
				<th>Size</th>
				<th>Inventory</th>
			</tr>
			<tr id='itemDetails'>
				<td><?=$this->form->text('sku');?></td>
				<td><?=$this->form->text('color');?></td>
				<td><?=$this->form->text('weight');?></td>
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