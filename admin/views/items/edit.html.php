<?=$this->html->script(array('tiny_mce/tiny_mce.js', 'jquery-1.4.2', 'jquery-dynamic-form.js', 'jquery-ui-1.8.2.custom.min.js', 'handlers.js', 'item_upload.js'));?>
<?=$this->html->style(array('jquery_ui_blitzer.css', 'jquery.dataTables.js', 'table'))?>

<?=$this->html->script('jquery.flash.min.js')?>
<?=$this->html->script('agile-uploader-3.0.js')?>
<?=$this->html->style('agile_uploader.css');?>
<?=$this->html->style('admin_common.css');?>
<?=$this->html->style('files.css');?>

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
	<h2 id="page-heading">Editing Item - <?=$item->description?></h2>
</div>
<?=$this->html->link('See Item List','/events/edit/'.$item->event[0].'#event_items')?>
<?=$this->form->create(); ?>
	<div id="tabs">
		<ul>
		    <li><a href="#item_info"><span>Item Info</span></a></li>
			<li><a href="#item_images"><span>Item Images</span></a></li>
			<li><a href="#item_event_info"><span>Item Event Info</span></a></li>
		</ul>
		<div id="item_info">
				<input type="hidden" name="_id" value="<?=$item->_id?>" id="_id">
				<br>
				<div id="item_description">
					<h2 id="">Product Description</h2>
					<?=$this->form->field('description', array(
						'type' => 'text',
						'class' => 'desc',
						'value' => $item->description
					));?>
					<?=$this->form->label('Copy'); ?>
					<?=$this->form->textarea('blurb', array(
						'class' => 'general',
						'name' => 'blurb',
						'value' => $item->blurb
					));?>
					<?=$this->form->field('vendor', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->vendor
					));?>
					<?=$this->form->field('vendor_style', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->vendor_style
					));?>
					<?=$this->form->field('color', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->color
					));?>
					<?=$this->form->field('age', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->age
					));?>
					<?=$this->form->label('Departments')?><br />
					<table>
						<?=$this->form->select('departments',$all_filters,array('multiple'=>'multiple','value' => $sel_filters)); ?> 
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
						<?=$this->form->text("shipping_rate", array('value'=>$item->shipping_rate, 'id'=>"shipping_rate")) ?>
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
					<?=$this->form->field('sale_retail', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->sale_retail
					));?>
					<?=$this->form->field('msrp', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->msrp
					));?>
					<?=$this->form->field('total_quantity', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->total_quantity
					));?>

					<?=$this->form->field('orig_whol', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->orig_whol
					));?>
					<?=$this->form->field('sale_whol', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->sale_whol
					));?>
				</div>
				<div id="item_properties">
					<h2 id="">Weight and Dimensions</h2>
					<?=$this->form->field('product_weight', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->product_weight
					));?>
					<?=$this->form->field('product_dimensions', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->product_dimensions
					));?>
					<?=$this->form->field('shipping_weight', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->shipping_weight
					));?>
					<?=$this->form->field('shipping_dimensions', array(
						'type' => 'text',
						'class' => 'general',
						'value' => $item->shipping_dimensions));?>
				</div>
				<div id="item_details">
					<h2 id="">Item Details</h2>
					<table border="0" cellspacing="5" cellpadding="5">
						<?php foreach ($item->details->data() as $key => $value): ?>
							<tr>
								<td>
									<?=$key?>
								</td>
								<td>
									<?=$this->form->text("details[$key]", array(
										'value' => $value
										));
									?>
								</td>
							</tr>
						<?php endforeach ?>
					</table>
				</div>
				<br>
				<br>
			<?=$this->form->submit('Update Item'); ?>
		</div>
		<div id="item_images">
			<br />
			
			<div class="tab_region_left_col">
				<div class="box">
					<h2>Upload Media for Item</h2>
					<div class="block">
						<form id="ItemMedia">
						</form>
						<div id="agile_file_upload"></div>
						<script type="text/javascript">
							$('#agile_file_upload').agileUploader({
								flashSrc: '<?=$this->url('/swf/agile-uploader.swf'); ?>',
								submitRedirect: '<?=$this->url('/items/edit/' . (string)$item->_id); ?>',
								formId: 'ItemMedia',
								removeIcon: '<?=$this->url('/img/agile_uploader/trash-icon.png'); ?>',
								flashVars: {
									button_up: '<?=$this->url('/img/agile_uploader/add-file.png'); ?>',
									button_down: '<?=$this->url('/img/agile_uploader/add-file.png'); ?>',
									button_over: '<?=$this->url('/img/agile_uploader/add-file.png'); ?>',
									//form_action: $('#EventEdit').attr('action'),
									form_action: '<?=$this->url('/files/upload/all'); ?>',
									file_limit: 30,
									max_height: '1000',
									max_width: '1000',
									file_filter: '*.jpg;*.jpeg;*.gif;*.png;*.JPG;*.JPEG;*.GIF;*.PNG',
									resize: 'jpg,jpeg,gif',
									force_preview_thumbnail: 'true',
									firebug: 'false'
								}
							});	
						</script>

						<a href="#" class="upload_files_link" onClick="document.getElementById('agileUploaderSWF').submit();">Start Upload <?=$this->html->image('agile_uploader/upload-icon.png', array('height' => '24')); ?></a>
					</div>
				</div>
			</div>
			
			<div class="tab_region_right_col">
				<div class="box">
					<h2>Item Image File Naming Conventions</h2>
					<div class="block">
					<dl>
						<dt>Primary Image</dt>
						<dd>items_shirt_primary.jpg</dd>

						<dt>Zoom Image</dt>
						<dd>items_shirt_zoom.jpg</dd>

						<dt>For Various Colors <em>(colors are a part of the url)</em></dt>
						<dd>items_shirt-yellow_primary.jpg</dd>
						<dd>items_shirt-yellow_zoom.jpg</dd>

						<dt>For Alternate Versions</dt>
						<dd>items_shirt-blue_alternate.jpg</dd>
						<dd>items_shirt-blue_alternateB.jpg</dd>
						<dd>items_shirt-blue_alternate0.jpg <em>etc.</em></dd>
					</dl>
					</div>
				</div>
			</div>

			<div class="clear"></div>
			<br style="clear: left;" />
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
									<?=$this->html->image("/image/$item->zoom_image.jpg", array('alt' => 'altText')); ?>
								</td>
								<td align="center">
									<input type="radio" name="zoom_image" value="<?=$item->zoom_image;?>" checked>
								</td>
								<td></td>
								<td></td>
							</tr>
						<?php endif ?>
						<?php if ($item->primary_image): ?>
							<tr>
								<td align="center">
									<?=$this->html->image("/image/$item->primary_image.jpg", array(
										'alt' => 'altText'));
									?>
								</td>
								<td></td>
								<td align="center">
									<input type="radio" name="primary_image" value="<?=$item->primary_image;?>" checked>
								</td>
								<td></td>
							</tr>
						<?php endif ?>
						<?php
							if (!empty($item->alternate_images)):
								foreach ($item->alternate_images as $value): ?>
								<tr>
									<td align="center">
										<?=$this->html->image("/image/$value.jpg", array('alt' => 'altText')); ?>
									</td>
									<td></td>
									<td></td>
									<td align="center">
										<input type="checkbox" name="alternate-<?=$value;?>" value="<?=$value;?>" checked>
									</td>
								</tr>
								<?php endforeach; ?>
							<?php endif; ?>
					</table>
			<?php endif; ?>
			<?=$this->form->submit('Update Item'); ?>
		</div>
		<div id="item_event_info">
			<h1 id="event_information">Event Information</h1>
			<?php if (!empty($event)): ?>
				<p>This item is associated with the <strong><?=$event->name?> </strong>event</p>
				<?=$this->html->link("Edit - $event->name", array('Events::edit', 'args' => array("$event->_id"))); ?><br>
				<?=$this->html->link("View - $event->name", array('Events::preview', 'args' => array("$event->_id"))); ?><br>
				<?=$this->html->link("View - $item->description", array('Items::preview', 'args' => array("$item->url"))); ?>
			<?php endif ?>
		</div>
	</div>
<?=$this->form->end(); ?>

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