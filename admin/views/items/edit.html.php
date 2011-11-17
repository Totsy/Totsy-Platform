<?php

use lithium\util\Inflector;

?>
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
	<h2>Editing Item <em><?=$item->description; ?></em> (<?=$item->vendor_style; ?>)</h2>
</div>
<?=$this->html->link('See Item List','/events/edit/'.$item->event[0].'#event_items')?>
	<div id="tabs">
		<ul>
		    <li><a href="#item_info"><span>Info</span></a></li>
			<li><a href="#item_event_info"><span>Event</span></a></li>
			<li><a href="#item_images"><span>Media Upload</span></a></li>
			<li><a href="#item_media_status"><span>Media Status</span></a></li>
		</ul>
		<div id="item_info">
			<h3>Info</h3>
			<?=$this->form->create(); ?>
				<input type="hidden" name="_id" value="<?=$item->_id?>" id="_id">
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
					<div id="item_miss_christmas">
						<h2 id="item_status">Xmas Shipping Status</h2>
						<?php if ($item->miss_christmas == 1): ?>
							<p>Will item/product ship for Christmas?</p><br>
							<input type="radio" name="miss_christmas" value="0" id="enabled"> Yes, ships before 12.23 <br>
							<input type="radio" name="miss_christmas" value="1" id="enabled" checked> NO AFTER XMAS
						<?php else: ?>
							<p>Will item/product ship for Christmas?</p><br>
							<input type="radio" name="miss_christmas" value="0" id="enabled" checked> Yes, ships before 12.23 <br>
							<input type="radio" name="miss_christmas" value="1" id="enabled"> NO AFTER XMAS
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
									<?php echo $key?>
								</td>
								<td>
									<?=$this->form->text("details[$key]", array(
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
									<?=$this->form->text("item_new_size");
									?>
								</td>
							</tr>

					</table>
				</div>
			<br>
			<br>
			<?=$this->form->submit('Update Item'); ?>
			<?=$this->form->end(); ?>
		</div>

		<div id="item_event_info">
			<h1 id="event_information">Event Information</h1>
			<?php if (!empty($event)): ?>
				<p>This item is associated with the <strong><?=$event->name?> </strong>event</p>
				<?=$this->html->link("Edit - $event->name", array('Events::edit', 'args' => array("$event->_id"))); ?><br>
				<?=$this->html->link("View - $event->name", array('Events::preview', 'args' => array("$event->_id"))); ?><br>
				<?=$this->html->link("View - $item->description", array('Items::preview', 'args' => array("$item->url"))); ?>
			<?php endif; ?>
		</div>

		<div id="item_images">
			<div class="tab_region_left_col">
				<div class="box">
					<h2>Upload Via Form</h2>
					<div class="block">
						<form id="ItemMedia">
							<?php
								// Without this event_id being passed along with the files,
								// Item images could not be saved.
							?>
							<?php if (!empty($event)): ?>
								<input type="hidden" name="event_id" value="<?php echo (string)$event->_id; ?>" />
							<?php endif; ?>
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
				<?=$this->view()->render(array('element' => 'files_naming_item'), array('item' => $item)); ?>
			</div>
		</div>

		<div id="item_media_status">
			<div class="tab_region_right_col">
				<table>
					<thead>
						<tr>
							<th>Type</th>
							<th>Preview</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($item->images() as $name => $image): ?>
						<?php if ($name == 'alternate'): ?>
							<?php foreach ($image as $k => $i): ?>
							<tr>
								<th><?=Inflector::humanize($name); ?> (<?=($k + 1) ?>)</th>
								<th><?=$this->html->image($i ? $i->url() : '/img/no-image-small.jpeg'); ?>
							</tr>
							<?php endforeach; ?>
						<?php else: ?>
						<tr>
							<th><?=Inflector::humanize($name); ?></th>
							<th><?=$this->html->image($image ? $image->url() : '/img/no-image-small.jpeg'); ?>
						</tr>
						<?php endif; ?>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

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