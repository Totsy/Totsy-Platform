<?php ini_set("display_erros", 0);

use admin\models\Event;
use lithium\util\Inflector;

?>

<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<?=$this->html->script('http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js')?>
<?=$this->html->script('jquery.iframe-transport.js')?>
<?=$this->html->script('jquery.fileupload.js')?>
<?=$this->html->script('jquery.fileupload-ui.js')?>
<?=$this->html->script('uploader.js')?>
<?=$this->html->style('jquery.fileupload-ui.css');?>
<?=$this->html->style('selectlist.css');?>
<?=$this->html->script('jquery.selectlist.min.js')?>
<?=$this->html->script('jquery.selectlist.pack.js')?>

<?=$this->html->script('jquery.flash.min.js')?>
<?=$this->html->script('agile-uploader-3.0.js')?>
<?=$this->html->style('agile_uploader.css');?>
<?=$this->html->style('admin_common.css');?>
<?=$this->html->script('files.js');?>
<?=$this->html->style('files.css');?>

<style type="text/css">
.selectlist-list {
    list-style: none outside none;
    margin: 0;
    padding: 0;
}

.selectlist-list {
    width: 12em;
}
</style>

<script type="text/javascript">

$(document).ready(function(){

tinyMCE.init({
	// General options
	mode : "exact",
	elements: "Blurb,ShipMessage,"+allitemids,
	theme : "advanced",
	plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,preview,searchreplace,print,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras",
	editor_deselector : "mceNoEditor",
	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull",

	theme_advanced_buttons2: "styleselect,formatselect,fontselect,fontsizeselect",

	theme_advanced_buttons3 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,indent,blockquote,|,anchor,code,|,forecolor,backcolor",
	/* theme_advanced_button3:
	 theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,charmap,iespell,advhr",
	 theme_advanced_buttons4 : "spellchecker,|,cite,abbr,acronym,del,ins,|,visualchars,nonbreaking,blockquote,pagebreak", */

	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : false,

});

$('.table_link').click(function() {
        $('tr').hide();
      $('tr .').toggle('slow');
    });

$('.related_items').selectList({
	addAnimate: function (item, callback) {
	$(item).slideDown(500, callback);
	},
	removeAnimate: function (item, callback) {
	$(item).slideUp(500, callback);
	}
});

$('.related_items').change(function() {

//parse out the current item's id
var item_id = this.id.substring(9, this.id.length);
var list_position = this.id.substring(7,8);

//create strings of the dropdown id's
for ( i=1; i<6; i++ ) {

	var related_item_id = 'related'+ i + '_' + item_id;
	//if its not the current dropdown
	//and its value is the same as the current dropdown's value AND
	//the item's value isnt an empty string
	//than throw an alert message
	if(i!=list_position && $("#" + related_item_id + " option:selected").val()!=="" ) {

		if( $("#" + related_item_id + " option:selected").val() == $("#" + this.id + " option:selected").val() ) {
			$("#" + this.id).val(0);
			alert("please select a different item");
			break;
		}
	}
}

});

});

</script>

<script type="text/javascript">
	$(document).ready(function(){
		$("#duplicate").dynamicForm("#plus", "#minus", {limit:15, createColor: 'yellow', removeColor: 'red'});
		});
		</script>

<script type="text/javascript" charset="utf-8">
	$(function() {
		var dates = $('#start_date, #end_date').datetimepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				var option = this.id == "start_date" ? "minDate" : "maxDate";
				var instance = $(this).data("datetimepicker");
				var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);
			}
		});
	});
</script>

<script type="text/javascript" charset="utf-8">

	var oTable;

	$(document).ready(function() {
		/* Add a click handler to the rows - this could be used as a callback */
		$('#itemTable tr').click( function() {
			if ( $(this).hasClass('row_selected') )
				$(this).removeClass('row_selected');
			else
				$(this).addClass('row_selected');
		} );

		/* Init the table */
		oTable = $('#itemTable').dataTable();

	} );

	function fnGetSelected( oTableLocal )
	{
		var aReturn = new Array();
		var aTrs = oTableLocal.fnGetNodes();

		for ( var i=0 ; i<aTrs.length ; i++ )
		{
			if ( $(aTrs[i]).hasClass('row_selected') )
			{
				aReturn.push( aTrs[i].id );
			}
		}
		var eventItems = document.getElementById('event_items');
		eventItems.innerHTML = eventItems.innerHTML + aReturn;
		return aReturn;
	}


</script>
<script type="text/javascript" charset="utf-8">
	var limit = <?php echo $shortDescLimit;?>;
	$(document).ready(function() {

		$('#Short').keyup(function(){
			return limitTextArea($(this),$('#short_description_characters_counter'),limit);
		});

		$('#Short').focusout(function(){
			return limitTextArea($(this),$('#short_description_characters_counter'),limit);
		});

		//this loads the event/inventory iframe src when the tab is clicked
		$("#inventoryLink").click(function(){
			$("#inventoryIframe").attr('src', "/events/inventory/<?=$event->_id; ?>");
		});




	});

	function limitTextArea(text,info,limiter){
		var len = text.val().length;
		if (len>limiter){
			text.val(text.val().substr(0,limiter));
			$('#short_description_characters_counter').text(limiter);
			return false;
		} else {
			$('#short_description_characters_counter').text(len);
			return true;
		}
	}
</script>
<!-- Sorting of primary/secondary event item images -->
<script>
$(function() {
	$(".images.sortable").sortable({
		opacity: 0.7,
		placeholder: "placeholder",
		containment: 'document',
		dropOnEmpty: false,
		revert: 100,
		scrollSensitivity: 50,
		scrollSpeed: 70,
		update: function(event, ui) {
			data = $(this).sortable("serialize");

			$.ajax({
				type: "POST",
				url: $(this).attr('target'),
				data: data
			});
		}
	});
});
</script>
<?=$this->form->create(null, array('id' => "events_edit", 'enctype' => "multipart/form-data")); ?>
<div class="grid_16">
	<h2>Editing Event <em><?=$event->name; ?></em></h2>
</div>

<div class="grid_16">
	<!-- Start Tabs -->
	<div id="tabs">
		<ul>
		    <li><a href="#event_info"><span>Info</span></a></li>
		    <li><a href="#event_items"><span>Items</span></a></li>
		    <li><a href="#event_history"><span>History</span></a></li>
		    <li><a href="#event_inventory" id="inventoryLink"><span>Event Inventory</span></a></li>
			<li><a href="#event_media_upload"><span>Media Upload</span></a></li>
			<li><a href="#event_media_status"><span>Media Status</span></a></li>
		</ul>

		<!-- Start Tab -->
		<div id="event_info">
			<div id="event_note">
				<p>
					Hello administrator. Please edit an event by filling in
					all the information below. Thank You!
				</p>
			</div>
			<div id="event_preview">
				<p>
					To see a preview of the event please
					<?=$this->html->link('click here.',"/events/preview/$event->_id")?>
				</p>
			</div>
			<div class="tab_region_left_col">
				<h4 id="article-heading">Event Description</h4>
				<?=$this->form->field('name', array('value' => $event->name, 'class' => 'general'));?>

				<div id="blurb_div">
					<?=$this->form->field('blurb', array(
						'type' => 'textarea', 'name' => 'content', 'value' => $event->blurb
					));?>
					<br>
				</div>
			    <div style="width:450px;">
			    	<?php echo $this->form->field('short', array(
			    	    'type' => 'textarea',
			    	    'name' => 'short_description',
			    	    'class' => 'mceNoEditor shortDescription',
			    		'value' => isset($event->short)?$event->short:''
			    	));?>
			    	<div id="short_description_characters_wrapper">
			    		Total:
			    		<span id="short_description_characters_counter">
			    			<?php if(isset($event->short)) {
			    			   		echo strlen($event->short);
			    			   } else {
			    			   		echo '0';
			    			   }?>
			    		</span>/<?php echo $shortDescLimit;?>
			    	</div>
			    </div>
				<div id="event_status">
					<h4 id="event_status">Event Status</h4>
					<?php if ($event->enabled == 1): ?>
						<p>The event is currently published for viewing</p><br>
						<input type="radio" name="enabled" value="1" id="enabled" checked> Enable Event <br>
						<input type="radio" name="enabled" value="0" id="enabled"> Disable Event
					<?php else: ?>
						<p>The event is NOT published for viewing</p><br>
						<input type="radio" name="enabled" value="1" id="enabled"> Enable Event <br>
						<input type="radio" name="enabled" value="0" id="enabled" checked> Disable Event
					<?php endif ?>
				</div>
				<div id="event_type">
					<h2 id="event_type">Event Type</h2>
					<input type="radio" name="tangible" value="1" id="tangible" <?php if ($event->tangible == 1) echo 'checked'; ?> > Tangible <br>
					<input type="radio" name="tangible" value="0" id="tangible" <?php if ($event->tangible == 0) echo 'checked'; ?> > Non Tangible
				</div>
		<div id="event_viewlive">
			<h2 id="event_type">View Live Anyway</h2>
		 (allows direct url access to event even if otherwise disabled)<br>
			<input type="radio" name="viewlive" value="1" id="viewlive" <?php if ($event->viewlive == 1) echo 'checked'; ?>> Direct URL <br>
			<input type="radio" name="viewlive" value="0" id="viewlive" <?php if ($event->viewlive == 0) echo 'checked'; ?>> Not Viewable
		</div>

		<div id="event_clearance">
			<h2 id="event_type">Clearance</h2>
			<input type="radio" name="clearance" value="1" id="clearance" <?php if ($event->clearance == 1) echo 'checked'; ?>> Clearance <br>
			<input type="radio" name="clearance" value="0" id="clearance" <?php if ($event->clearance == 0) echo 'checked'; ?>> Not Clearance
		</div>
		<div id="event_duration">
            <h4 id="event_duration">Event Duration</h4>
            <?php
                $start_date = date('m/d/Y H:i', $event->start_date->sec);
                $end_date =  date('m/d/Y H:i', $event->end_date->sec);
                echo $this->form->field('start_date', array(
                        'class' => 'general',
                        'id' => 'start_date',
                        'value' => "$start_date"
                ));
                echo $this->form->field('end_date', array(
                        'class' => 'general',
                        'id' => 'end_date',
                        'value' => "$end_date"
                ));
            ?>
        </div>
				<?=$this->form->label('Departments')?><br />

				<table>
					<?=$this->form->select('departments',$all_filters,array('multiple'=>'multiple')); ?>
				</table>

				<div id="tags">
					<?=$this->form->label('Tags'); ?>
					<?php if ($event->tags): ?>
						<select name="tags[]" id="tags" multiple="multiple" size="5">
							<?php foreach (Event::$tags as $tag): ?>
								<?php if (is_array($event->tags) && in_array($tag, $event->tags)): ?>
									<option value="<?=$tag?>" selected><?=$tag?> </option>
								<?php else: ?>
									<option value="<?=$tag?>"><?=$tag?> </option>
								<?php endif ?>
							<?php endforeach ?>
						</select>
					<?php else: ?>
						<?=$this->form->select('tags', Event::$tags, array('size' => 5, 'multiple' => 'multiple')); ?>
					<?php endif;?>
				</div>
				<br>

				<div id="shipMessage">
					<?=$this->form->label('Shipping Message'); ?>
					<?=$this->form->textarea('ship_message', array('value' => $event->ship_message)); ?>
				</div>

				<div id="shipDateOverride">
					<?=$this->form->label('Estimated Ship Date'); ?>
					<p>This date will override the calcualted ship date for orders.</p>
					<?=$this->form->text('ship_date', array('id' => 'ship_date', 'value' => $event->ship_date)); ?>
				</div>

<<<<<<< HEAD
				<br />
				<?=$this->form->submit('Update Event')?>
			</div>

			<div class="tab_region_right_col">
				<!-- Start Event Images -->
				<div id="event_images">
					<h3>Media</h3>
					<table>
						<thead>
							<tr>
								<th>Type</th>
								<th>Preview</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($event->images() as $name => $image): ?>
							<tr>
								<th><?=Inflector::humanize(str_replace('_image', '', $name)); ?></th>
								<th><?=$this->html->image($image ? $image->url() : '/img/no-image-small.jpeg'); ?>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<!-- End Event Images -->
			</div>
		</div>
		<!-- End Tab -->

		<!-- Start Tab -->
		<div id="event_items">
			<h3 id="">Item Management</h3>
			<hr />
			<div style="width:300px; height:500px; float:left">
				<h3 id="">Upload Items</h3>
	            <hr />
				<p>Please select default option for all items uploaded:</p>
					<input type="radio" name="enable_items" value="1" id="enable_items"> Enable All <br>
					<input type="radio" name="enable_items" value="0" id="enable_items" checked> Disable All <br><br>
				<p>Add "Final Sale" to the item description?:</p>
					<input type="radio" name="enable_finalsale" value="1" id="enable_finalsale" checked>Yes <br>
					<input type="radio" name="enable_finalsale" value="0" id="enable_finalsale">No<br><br>
				<p>Will item/product ship for Christmas?:</p>
					<input type="radio" name="miss_christmas" value="0" id="miss_christmas">Yes, ships before 12.23<br>
					<input type="radio" name="miss_christmas" value="1" id="miss_christmas" checked>NO AFTER XMAS<br><br>

				<p>Will item/product ship for Christmas?:</p>
					<input type="radio" name="miss_christmas" value="0" id="miss_christmas" checked>Yes, ships before 12.23<br>
					<input type="radio" name="miss_christmas" value="1" id="miss_christmas">NO AFTER XMAS<br><br>
					<!--
					<?php echo $this->form->label('Upload Event (Excel Files): '); ?>
					<?php echo $this->form->file('upload_file'); ?>
					-->
				<?=$this->form->field('items_submit', array('type' => 'textarea', 'rows' => '7', 'cols' => '50', 'name' => 'ItemsSubmit'));?><br>
=======
			</div>

			<div class="clear"></div>

			<div class="tab_bottom_submit">
				<div class="submit_button"><?=$this->form->submit('Update Event', array('class' => 'submit_event'))?></div><div class="cancel"><a href="/admin/select/event">Cancel</a></div>
			</div>
		</div>

		<div id="event_items">
			<h3 id="">Item Management</h3>
			<hr />
			<h3 id="">Upload Items</h3>
            <hr />
			<p>Please select the default option for all items being uploaded:</p>
				<input type="radio" name="enable_items" value="1" id="enable_items"> Enable All <br>
				<input type="radio" name="enable_items" value="0" id="enable_items" checked> Disable All <br><br>
			<p>Add "Final Sale" to the item description?:</p>
				<input type="radio" name="enable_finalsale" value="1" id="enable_finalsale" checked>Yes <br>
				<input type="radio" name="enable_finalsale" value="0" id="enable_finalsale">No<br><br>
				<?=$this->form->file('upload_file'); ?>
				<?=$this->form->submit('Update Event')?>
				<?=$this->form->label('Upload Event (Excel Files): '); ?>
<!--
		<iframe id="upload_frame" name="upload_frame" src="/events/upload/<?=$event->_id?>" frameborder=0 scrolling=no width=400 height=250></iframe>
		<div id="upload_error" name="upload_error" style="color:#ff0000; width:400px; float:right; height:250px; overflow:scroll;">(spreadsheet upload errors will appear here)</div>

-->
			<br><br>

>>>>>>> uploader. event images.

            <hr />
			<br><br>
			<?=$this->form->end(); ?>
			</div>

			<div id="items_errors" name="items_errors" style="float:right; width:500px; height:400px;overflow:scroll;"></div>

			<div style="clear:both; height:30px;"></div>
			<h3 id="current_items">Current Items</h3>

            <hr />
			<?php echo $this->form->create(null, array('url' => 'Items::itemUpdate', 'name' => 'item-update')); ?>
				<?php echo $this->form->hidden('id', array('value' => $event->_id)); ?>
				<div style="float:left; font: bold; font-size: 18px;">
					Total Items:
					<?php
						echo count($eventItems);
					?>

				</div>

				<div style="float:right; font: bold; font-size: 18px;">
			<?=$this->form->submit('Update Event')?>
							</div>
				<br \>
				<br \>

				<?php echo $this->items->build($eventItems);?>

				<div style="float:right; font: bold; font-size: 18px;">
					<?=$this->form->submit('Update Event')?>
				</div>

			<br><br>
			<h2 id="">Delete Items</h2>
				<p>Click the button below to delete all items from this event. <strong>WARNING - This action cannot be undone. All items associated with this event will be deleted!!!!!!</strong></p>
				<?=$this->form->create(null, array('url' => 'Items::removeItems', 'name' => 'item-delete')); ?>
					<?=$this->form->hidden('event', array('value' => $event->_id)); ?>
					<?=$this->form->submit('Delete All Items'); ?>
				<?=$this->form->end(); ?>
		</div>
		<div id="event_history">
				<?php
					if (sizeof($event->modifications) > 0) {
				?>

				<table>
					<tr>
						<td>User</td>
						<td>Date</td>
						<td>Changed</td>
					</tr>
				<?php
						$i = 0;
						while ($i < sizeof($event->modifications)) {

				?>
				<tr>
					<td><?php echo $event->modifications[$i]->author;?></td>
					<td>
					<?php
							$date_changed = $event->modifications[$i]->date;
							print date('Y-M-d h:i:s', $date_changed->sec);
					?>
					</td>
					<td><?php echo $event->modifications[$i]->changed;?></td>
				</tr>
				<?php
							$i++;
						}
				?>
				</table>
				<?php
					} else {
						print 'No event modifications have been made.';
					}
				?>
		</div>
		<div id="event_inventory">
			<iframe id="inventoryIframe" src="" style="width:900px; height:400px;"></iframe>
		</div>
<<<<<<< HEAD
		<!-- Tab -->
		<div id="event_media_upload">
			<p>
				Upload all event media here.
				This includes event images <em>as well as item images</em>.
				Please ensure all event images follow the naming conventions below.

				This page also allows to manage any pending files binded to this event.

				For more information and other methods to upload files please see <?=$this->html->link('File Management', 'Files::index'); ?>.
			</p>
			<div class="tab_region_left_col">
				<div class="box">
					<h2>Upload via Form</h2>
					<form id="EventMedia">
						<?php
							// Without this event_id being passed along with the files,
							// Item images could not be saved.
						?>
						<input type="hidden" name="event_id" value="<?php echo (string)$event->_id; ?>" />
					</form>
					<div id="agile_file_upload"></div>
					<script type="text/javascript">
						$('#agile_file_upload').agileUploader({
							flashSrc: '<?=$this->url('/swf/agile-uploader.swf'); ?>',
							submitRedirect: '<?=$this->url('/events/edit/' . (string)$event->_id); ?>',
							formId: 'EventMedia',
							flashWidth: 70,
							removeIcon: '<?=$this->url('/img/agile_uploader/trash-icon.png'); ?>',
							flashVars: {
								button_up: '<?=$this->url('/img/agile_uploader/add-file.png?v=1'); ?>',
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
								firebug: 'true'
							}
						});
					</script>

					<a
						href="#"
						class="upload_files_link"
						onClick="document.getElementById('agileUploaderSWF').submit();"
					>
						Start Upload <?=$this->html->image('agile_uploader/upload-icon.png', array('height' => '24')); ?>
					</a>
				</div>
			</div>
			<div class="tab_region_right_col">
				<?=$this->view()->render(array('element' => 'files_naming_event'), array('item' => $event)); ?>
				<?=$this->view()->render(array('element' => 'files_naming_item')); ?>
			</div>

			<div class="clear"></div>
			<?=$this->view()->render(array('element' => 'files_pending'), array('item' => $event)); ?>
		</div>
		<!-- End Tab -->

		<!-- Start Tab -->
		<div id="event_media_status">
			<div class="actions">
				<?=$this->html->link('refresh', array(
					'action' => 'media_status', 'id' => $event->_id
				), array(
					'class' => 'refresh', 'target' => '#event_media_status_data'
				)); ?>
			</div>
			<p>
				This tab show the status of media associated with the items of this event.
			</p>
			<div id="event_media_status_data"><!-- Populated through AJAX request. --></div>
		</div>
		<!-- End Tab -->
=======
		<div id="event_media">
			<h3 id="uploaded_media">Upload Media</h3>
			<p>
				Upload all event media here. This includes event images as well as item images. Please ensure all filenames follow proper naming convention.
			</p>
			<p>
				The event URL is: <?=$event->url; ?><br />
				So for example, files names would be; events_<?=$event->url; ?>.jpg, events_<?=$event->url; ?>_logo.jpg, events_<?=$event->url; ?>_small_splash.jpg, events_<?=$event->url; ?>_big_splash.jpg
			</p>
			<p>
				You can also upload event item images here as well. These also follow a naming convention, for example:<br />
				items_shirt_primary.jpg, items_shirt_zoom.jpg<br />
				...and for various colors: items_shirt-yellow_primary.jpg, items_shirt-yellow_zoom.jpg<br />
				...and alternate images: items_shirt-blue_alternate.jpg, items_shirt-blue_alternateB.jpg, items_shirt-blue_alternate0.jpg
			</p>
            <hr />

			<form id="EventMedia">
			</form>
			<div id="agile_file_upload"></div>
			<script type="text/javascript">

				$('#agile_file_upload').agileUploader({
					flashSrc: '/admin/swf/agile-uploader.swf',
					submitRedirect: $('#EventEdit').attr('action'),
					//formId: 'EventEdit',
					formId: 'EventMedia',
					removeIcon: '/admin/img/agile_uploader/trash-icon.png',
					flashVars: {
						button_up: '/admin/img/agile_uploader/add-file.png',
						button_down: '/admin/img/agile_uploader/add-file.png',
						button_over: '/admin/img/agile_uploader/add-file.png',
						//form_action: $('#EventEdit').attr('action'),
						form_action: '/admin/files/upload/event',
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

			<a href="#" onClick="document.getElementById('agileUploaderSWF').submit();">Upload</a>
		</div>
>>>>>>> uploader. event images.
	</div>
	<!-- End Tabs -->
</div>

<script type="text/javascript">
$(document).ready(function() {

	//create tabs
	$("#tabs").tabs();
});
</script>
<script type="text/javascript">
	jQuery(function($){
	   $("#ship_date").mask("99/99/9999");
	});
</script>