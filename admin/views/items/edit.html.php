<?php echo $this->html->script(array('tiny_mce/tiny_mce.js', 'jquery-1.4.2', 'jquery-dynamic-form.js', 'jquery-ui-1.8.2.custom.min.js', 'swfupload.js', 'swfupload.queue.js', 'fileprogress.js', 'handlers.js', 'item_upload.js'));?>
<?php echo $this->html->style(array('swfupload', 'jquery_ui_blitzer.css', 'jquery.dataTables.js', 'table'))?>

<script type="text/javascript">
tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,preview,searchreplace,print,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras",

	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,code,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,charmap,iespell,advhr",
	theme_advanced_buttons4 : "spellchecker,|,cite,abbr,acronym,del,ins,|,visualchars,nonbreaking,blockquote,pagebreak",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : false,

});
</script>
<div class="grid_16">
	<h2 id="page-heading">Editing Item - <?php echo $item->description?></h2>
</div>
<?php echo $this->html->link('See Item List','/events/edit/'.$item->event[0].'#event_items')?>
<?php echo $this->form->create(); ?>
	<div id="tabs">
		<ul>
		    <li><a href="#item_info"><span>Item Info</span></a></li>
			<li><a href="#item_images"><span>Item Images</span></a></li>
			<li><a href="#item_event_info"><span>Item Event Info</span></a></li>
		</ul>
		<div id="item_info">
				<input type="hidden" name="_id" value="<?php echo $item->_id?>" id="_id">
				<br>
				<div id="item_description">
					<h2 id="">Product Description</h2>
					<?php echo $this->form->field('description', array(
						'type' => 'text',
						'class' => 'desc',
						'value' => $item->description
					));?>
					<?php echo $this->form->label('Copy'); ?>
					<?php echo $this->form->textarea('blurb', array(
						'class' => 'general',
						'name' => 'blurb',
						'value' => $item->blurb
					));?>
					<?php echo $this->form->field('vendor', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->vendor
					));?>
					<?php echo $this->form->field('vendor_style', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->vendor_style
					));?>
					<?php echo $this->form->field('color', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->color
					));?>
					<?php echo $this->form->field('age', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->age
					));?>
					<?php echo $this->form->label('Ages')?><br />
					<table>
						<?php echo $this->form->select('ages',$ages,array('multiple'=>'multiple','value' => $age_filters)); ?>
					</table>
					<?php echo $this->form->label('Categories')?><br />
					<table>
						<?php echo $this->form->select('categories',$categories,array('multiple'=>'multiple','value' => $category_filters)); ?>
					</table>
					<?php echo $this->form->label('Departments')?><br />
					<table>
						<?php echo $this->form->select('departments',$all_filters,array('multiple'=>'multiple','value' => $sel_filters)); ?>
					</table>

					<div id="item_status">
						<h2 id="item_status">Item Status</h2>
						<?php if ($item->enabled == 1): ?>
							<p>The item is currently published for viewing</p><br>
							<input type="radio" name="enabled" value="1" id="enabled" checked> Enable Item <br>
							<input type="radio" name="enabled" value="0" id="enabled"> Disable Item
						<?php else: ?>
							<p>The item is NOT published for viewing</p><br>
							<input type="radio" name="enabled" value="1" id="enabled"> Enable Item <br>
							<input type="radio" name="enabled" value="0" id="enabled" checked> Disable Item
						<?php endif ?>
					</div>

						<div id="item_tax">
						<h2 id="item_tax">Item Tax</h2>
						<?php if ($item->taxable == 1): ?>
							<input type="radio" name="taxable" value="1" id="taxable" checked> Taxable Item <br>
							<input type="radio" name="taxable" value="0" id="taxable"> Not Taxable Item
						<?php else: ?>
							<input type="radio" name="taxable" value="1" id="taxable"> Taxable Item <br>
							<input type="radio" name="taxable" value="0" id="taxable" checked> Not Taxable Item
						<?php endif ?>
					</div>
					<div id="item_shipping">
						<h2 id="item_shipping">Shipping Exemption</h2>
						<?php if ($item->shipping_exempt == 1): ?>
							<input type="radio" name="shipping_exempt" value="1" id="shipping_exempt" checked> Shipping Exempt Item <br>
							<input type="radio" name="shipping_exempt" value="0" id="shipping_exempt"> Shipping Applied Item
						<?php else: ?>
							<input type="radio" name="shipping_exempt" value="1" id="shipping_exempt"> Shipping Exempt Item <br>
							<input type="radio" name="shipping_exempt" value="0" id="shipping_exempt" checked> Shipping Applied Item<br>
						<?php endif ?>
					</div>
					<div id="item_oversize" style="margin-left:20px">
						<?php if ($item->shipping_oversize == 1): ?>
							<input type="radio" name="shipping_oversize" value="0" id="shipping_oversize" > Shipping Normal size Item <br>
							<input type="radio" name="shipping_oversize" value="1" id="shipping_oversize" checked> Shipping OverSize Item
						<?php else: ?>
							<input type="radio" name="shipping_oversize" value="0" id="shipping_oversize" checked> Shipping Normal size Item <br>
							<input type="radio" name="shipping_oversize" value="1" id="shipping_oversize" > Shipping Oversize Item<br>
						<?php endif ?>
						<?php echo $this->form->text("shipping_rate", array('value'=>$item->shipping_rate, 'id'=>"shipping_rate")) ?>
					</div>

					<div id="discount">
						<h2 id="discount">Allow/Disallow Discounts on Order</h2>
						<p>Note: By setting this property the <b>entire</b> discounts (credits or promotions) can be disallowed.</p>
						<?php if ($item->discount_exempt == 1): ?>
							<input type="radio" name="discount_exempt" value="1" id="discount_exempt" checked> Disallow Credits/Promos on Order<br>
							<input type="radio" name="discount_exempt" value="0" id="discount_exempt"> Allow Credits/Promos on Order
						<?php else: ?>
							<input type="radio" name="discount_exempt" value="1" id="discount_exempt"> Disallow Credits/Promos on Order <br>
							<input type="radio" name="discount_exempt" value="0" id="discount_exempt" checked> Allow Credits/Promos on Order
						<?php endif ?>
					</div>
				</div>
				<div id="item_pricing">
					<h2 id="">Pricing</h2>
					<?php echo $this->form->field('sale_retail', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->sale_retail
					));?>
					<?php echo $this->form->field('msrp', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->msrp
					));?>
					<?php echo $this->form->field('total_quantity', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->total_quantity
					));?>

					<?php echo $this->form->field('orig_whol', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->orig_whol
					));?>
					<?php echo $this->form->field('sale_whol', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->sale_whol
					));?>
					<?php echo $this->form->field('percent_off', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->percent_off
					));?>
				</div>
				<div id="item_properties">
					<h2 id="">Weight and Dimensions</h2>
					<?php echo $this->form->field('product_weight', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->product_weight
					));?>
					<?php echo $this->form->field('product_dimensions', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->product_dimensions
					));?>
					<?php echo $this->form->field('shipping_weight', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->shipping_weight
					));?>
					<?php echo $this->form->field('shipping_dimensions', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->shipping_dimensions));?>
				</div>
				<div id="item_details">
					<h2 id="">Item Details</h2>
					<table border="0" cellspacing="5" cellpadding="5">
						<?php foreach ($item->details as $key => $value): ?>
							<tr>
								<td>
									<?php echo $key?>
								</td>
								<td>
									<?php echo $this->form->text("details[$key]", array(
										'value' => $value
										));
									?>
								</td>
							</tr>
						<?php endforeach ?>


							<tr>
								<td>
									add a new size:
								</td>
								<td>
									<?php echo $this->form->text("item_new_size");
									?>
								</td>
							</tr>

					</table>
				</div>
				<br>
				<br>
			<?php echo $this->form->submit('Update Item'); ?>
		</div>
		<div id="item_images">
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
			<?php if ($item->primary_image || $item->zoom_image || $item->alternate_images): ?>

					<table border="1" cellspacing="30" cellpadding="30">
						<th align="justify">
							Image Preview
						</th>
						<th align="center">
							Zoom Image
						</th>
						<th align="center">
							Primary Image
						</th>
						<th align="center">
							Alternate Image
						</th>
						<?php if ($item->zoom_image): ?>
							<tr>
								<td align="center">
									<?php echo $this->html->image("/image/$item->zoom_image.jpg", array('alt' => 'altText')); ?>
								</td>
								<td align="center">
									<input type="radio" name="zoom_image" value="<?php echo $item->zoom_image;?>" checked>
								</td>
								<td></td>
								<td></td>
							</tr>
						<?php endif ?>
						<?php if ($item->primary_image): ?>
							<tr>
								<td align="center">
									<?php echo $this->html->image("/image/$item->primary_image.jpg", array(
										'alt' => 'altText'));
									?>
								</td>
								<td></td>
								<td align="center">
									<input type="radio" name="primary_image" value="<?php echo $item->primary_image;?>" checked>
								</td>
								<td></td>
							</tr>
						<?php endif ?>
						<?php
							if (!empty($item->alternate_images)):
								foreach ($item->alternate_images as $value): ?>
								<tr>
									<td align="center">
										<?php echo $this->html->image("/image/$value.jpg", array('alt' => 'altText')); ?>
									</td>
									<td></td>
									<td></td>
									<td align="center">
										<input type="checkbox" name="alternate-<?php echo $value;?>" value="<?php echo $value;?>" checked>
									</td>
								</tr>
								<?php endforeach; ?>
							<?php endif; ?>
					</table>
			<?php endif; ?>
			<?php echo $this->form->submit('Update Item'); ?>
		</div>
		<div id="item_event_info">
			<h1 id="event_information">Event Information</h1>
			<?php if (!empty($event)): ?>
				<p>This item is associated with the <strong><?php echo $event->name?> </strong>event</p>
				<?php echo $this->html->link("Edit - $event->name", array('Events::edit', 'args' => array("$event->_id"))); ?><br>
				<?php echo $this->html->link("View - $event->name", array('Events::preview', 'args' => array("$event->_id"))); ?><br>
				<?php echo $this->html->link("View - $item->description", array('Items::preview', 'args' => array("$item->url"))); ?>
			<?php endif ?>
		</div>
	</div>
<?php echo $this->form->end(); ?>

<script type="text/javascript">
$(document).ready(function() {

	//create tabs
	$("#tabs").tabs();
});
</script>
<script type='text/javascript'>
$(document).ready(function(){
	($('#shipping_exempt:checked').val() == 0)? $('#item_oversize').show() : $('#item_oversize').hide();
	($('#shipping_oversize:checked').val() == 1)? $('#shipping_rate').show() : $('#shipping_rate').hide();
});

$(document).ready(function(){
	$('input[name=shipping_exempt]').change(function(){
		if ( $('#shipping_exempt:checked').val() == 0)
			$('#item_oversize').show();
		else
			$('#item_oversize').hide();
	});
	$('input[name=shipping_oversize]').change(function(){
		if ( $('#shipping_oversize:checked').val() == 1)
			$('#shipping_rate').show();
		else
			$('#shipping_rate').hide();
	});
});


</script>