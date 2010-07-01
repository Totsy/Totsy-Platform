<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('tiny_mce/tiny_mce.js');?>
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
	theme : "simple",
	width : "500"
});

</script>
<script type="text/javascript"> 
	$(document).ready(function(){	
		$("#itemDetails").dynamicForm("#plus", "#minus", {limit:15, createColor: 'yellow', removeColor: 'red'});
	});
</script>

<?=$this->html->link('See Item List','/items')?>
<h1>Edit Item</h1>
<?=$this->form->create(); ?>
	<?=$this->form->field('', array('value' => $item->_id, 'type' => 'hidden', 'id' => '_id', 'name' => '_id'));?>    
	<br>
	<div id="item_description">
		<h2 id="">Product Description</h2>
		<?=$this->form->field('description', array('type' => 'text', 'class' => 'desc', 'value' => $item->description));?>
		<?=$this->form->field('blurb', array('type' => 'textarea', 'class' => 'general','name' => 'content', 'value' => $item->blrub));?>
		<?=$this->form->field('vendor', array('type' => 'text', 'class' => 'general','value' => $item->vendor));?>
		<?=$this->form->field('vendor_style', array('type' => 'text', 'class' => 'general','value' => $item->vendor_style));?>
		<?=$this->form->field('age', array('type' => 'text', 'class' => 'general','value' => $item->age));?>
		<?=$this->form->field('color', array('type' => 'text', 'class' => 'general', 'value' => $item->color));?>
		<input type="radio" name="active" value="1" id="Yes" checked ="checked">Yes
		<input type="radio" name="active" value="0" id="No">No
	</div>
	<div id="item_activity">
		
	</div>
	<div id="item_pricing">
		<h2 id="">Pricing</h2>
		<?=$this->form->field('sale_retail', array('type' => 'text', 'class' => 'general','value' => $item->sale_retail));?>
		<?=$this->form->field('msrp', array('type' => 'text', 'class' => 'general','value' => $item->msrp));?>
		<?=$this->form->field('total_quantity', array('type' => 'text', 'class' => 'general','value' => $item->total_quantity));?>

		<?=$this->form->field('orig_whol', array('type' => 'text', 'class' => 'general','value' => $item->orig_whol));?>
		<?=$this->form->field('sale_whol', array('type' => 'text', 'class' => 'general','value' => $item->sale_whol));?>
	</div>
	<div id="item_properties">
		<h2 id="">Weight and Dimensions</h2>
		<?=$this->form->field('product_weight', array('type' => 'text', 'class' => 'general','value' => $item->product_weight));?>
		<?=$this->form->field('product_dimensions', array('type' => 'text', 'class' => 'general','value' => $item->product_dimensions));?>
		<?=$this->form->field('shipping_weight', array('type' => 'text', 'class' => 'general','value' => $item->shipping_weight));?>
		<?=$this->form->field('shipping_dimensions', array('type' => 'text', 'class' => 'general','value' => $item->shipping_dimensions));?>
	</div>
	<div id="item_details">
		<h2 id="">Item Details</h2>	
		<table border="0" cellspacing="5" cellpadding="5">
			<tr>
				<?php
					foreach ($item->details->data() as $key => $value) {
					echo "<th>$key</th>";
				}?>
			</tr>
			<tr id='itemDetails'>
				<?php
					foreach ($item->details->data() as $key => $value) {
					echo '<td>'. $this->form->text($key, array('value' => $value, 'class' => 'details')).'</td>';
				}?>
			</tr>
		</table>
	</div>
	<br>
	<br>
	<div id="fileInfo"></div>
	<h1>Upload files for a particular item</h1>

	<br>
	<table>
		<tr valign="top">
			<td>
				<div>
					<div class="fieldset flash" id="fsUploadProgress1">
						<span class="legend">Small File Upload Site</span>
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
	
	<br>
	<table border="1" cellspacing="30" cellpadding="30">
	<tr>
		<th align="justify">
			Image Preview
		</th>
		<th align="center">
			Primary Product Image
		</th>
		<th align="center">
			Secondary Product Image
		</th>
	</tr>


	<?php
		if (!empty($item->primary_images) && !empty($item->secondary_images)) {

			$intersect = array_intersect_key($item->secondary_images, $item->primary_images);

			
		}

		if (!empty($item->primary_images)):
			foreach ($item->primary_images as $value) : ?>
			<tr>
				<td align="center">
					<?=$this->html->image("/image/$value.jpg", array('alt' => 'altText')); ?>
				</td>
				<td align="center">
					<input type="checkbox" name="primary-<?=$value;?>" value="<?=$value;?>" checked>
				</td>
				<td align="center">
					<input type="checkbox" name="secondary-<?=$value;?>" value="<?=$value;?>">
				</td>
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php
			if (!empty($item->secondary_images)):
				foreach ($item->secondary_images as $value): ?>
				<tr>
					<td align="center">
						<?=$this->html->image("/image/$value.jpg", array('alt' => 'altText')); ?>
					</td>
					<td align="center">
						<input type="checkbox" name="primary-<?=$value;?>" value="<?=$value;?>">
					</td>
					<td align="center">
						<input type="checkbox" name="secondary-<?=$value;?>" value="<?=$value;?>" checked>
					</td>
				</tr>
				<?php endforeach; ?>
			<?php endif; ?>
	</table>
		


	<?=$this->form->submit('Update Item'); ?>
<?=$this->form->end(); ?>