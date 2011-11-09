<?php ini_set("display_erros", 0); ?>
<?php use admin\models\Event; ?>
<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('swfupload.js');?>
<?=$this->html->script('swfupload.queue.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('event_upload.js');?>
<?=$this->html->style('swfupload')?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<?=$this->html->style('selectlist.css');?>
<?=$this->html->script('jquery.selectlist.min.js')?>
<?=$this->html->script('jquery.selectlist.pack.js')?>

<style type="text/css">

.selectlist-list {
    list-style: none outside none;
    margin: 0;
    padding: 0;
}

selectlist.css (line 1)
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
	var limit = <?=$shortDescLimit;?>;
	$(document).ready(function() {
		
		$('#Short').keyup(function(){
			return limitTextArea($(this),$('#short_description_characters_counter'),limit);
		});

		$('#Short').focusout(function(){
			return limitTextArea($(this),$('#short_description_characters_counter'),limit);
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


<?=$this->form->create(null, array('id' => "events_edit", 'enctype' => "multipart/form-data")); ?>
<div class="grid_16">
	<h2>Editing Event - <?=$event->name?></h2>
</div>

<div class="grid_16">
	<div id="tabs">
		<ul>
		    <li><a href="#event_info"><span>Event Info</span></a></li>
			<li><a href="#event_images"><span>Event Images</span></a></li>
		    <li><a href="#event_items"><span>Event Items</span></a></li>
		    <li><a href="#event_history"><span>Event History</span></a></li>
		</ul>

		<div id="event_info">
			<div id="event_note">
				<p>
					Hello administrator. Please edit an event by filling in all the information below. Thank You!
				</p>
			</div>
			<div id="event_preview">
				<p> To see a preview of the event please <?=$this->html->link('click here.',"/events/preview/$event->_id")?></p>
			</div>
			<h4 id="article-heading">Event Description</h4>
			    <?=$this->form->field('name', array('value' => $event->name, 'class' => 'general'));?>
				<div id="blurb_div">
					<?=$this->form->field('blurb', array('type' => 'textarea', 
														 'name' => 'content', 
														 'value' => $event->blurb ));?><br>
				</div>
			    <div style="width:450px;">
			    	<?=$this->form->field('short', array('type' => 'textarea', 
			    										 'name' => 'short_description', 
			    										 'class' => 'mceNoEditor shortDescription', 
			    										 'value' => isset($event->short)?$event->short:'' ));?>
			    	<div id="short_description_characters_wrapper">
			    		Total: 
			    		<span id="short_description_characters_counter">
			    			<? if(isset($event->short)) { 
			    			   		echo strlen($event->short);
			    			   } else { 
			    			   		echo '0'; 
			    			   }?>
			    		</span>/<?=$shortDescLimit;?></div>
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
							));?>
				</div>
				<?=$this->form->label('Departments')?><br />
				
				<?=$event->departments?>
				
				<br><br>
				
				<table>
					<?=$this->form->select('departments',$all_filters,array('multiple'=>'multiple')); ?> 
				</table>
				<div id="tags">
					<?=$this->form->label('Tags'); ?>
					<?php if ($event->tags): ?>
						<select name="tags[]" id="tags" multiple="multiple" size="5">
							<?php foreach (Event::$tags as $tag): ?>
								<?php if (in_array($tag, $event->tags)): ?>
									<option value="<?=$tag?>" selected><?=$tag?> </option>
								<?php else: ?>
									<option value="<?=$tag?>"><?=$tag?> </option>
								<?php endif ?>
							<?php endforeach ?>
						</select>
					<?php else: ?>
						<?=$this->form->select('tags', Event::$tags, array('size' => 5, 'multiple' => 'multiple')); ?>
					<?php endif ?>
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
				<br>
			<?=$this->form->submit('Update Event')?>
		</div>
		<div id="event_images">
			<h3 id="current_images">Current Images</h3>
            <hr />
				<table border="1" cellspacing="30" cellpadding="30">
				<tr>
					<th align="justify">
						Image Location
					</th>
					<th align="justify">
						Image
					</th>
				</tr>
				<tr>
					<td>Big Splash Image</td>
					<td align="center">
						<?php
							if (!empty($event->images->splash_big_image)) {
								$eventImage = "/image/{$event->images->splash_big_image}.jpg";
							} else {
								$eventImage = "/img/no-image-large.jpeg";
							}
						?>
						<?=$this->html->image("$eventImage", array('alt' => 'altText')); ?>
					</td>
				</tr>
				<tr>
					<td>Small Splash Image</td>
					<td align="center">
						<?php
							if (!empty($event->images->splash_small_image)) {
								$eventImage = "/image/{$event->images->splash_small_image}.jpg";
							} else {
								$eventImage = "/img/no-image-small.jpeg";
							}
						?>
						<?=$this->html->image("$eventImage", array('alt' => 'altText')); ?>
					</td>
				</tr>
				<tr>
					<td>Event Image</td>
					<td align="center">
						<?php
							if (!empty($event->images->event_image)) {
								$eventImage = "/image/{$event->images->event_image}.jpg";
							} else {
								$eventImage = "/img/no-image-small.jpeg";
							}
						?>
						<?=$this->html->image("$eventImage", array('alt' => 'altText')); ?>
					</td>
				</tr>
				<tr>
					<td>Logo Image</td>
					<td align="center">
						<?php
							if (!empty($event->images->logo_image)) {
								$eventImage = "/image/{$event->images->logo_image}.jpg";
							} else {
								$eventImage = "/img/no-image-small.jpeg";
							}
						?>
						<?=$this->html->image("$eventImage", array('alt' => 'altText')); ?>
					</td>
				</tr>
				</table>

			<h3 id="uploaded_media">Uploaded Media</h3>
            <hr />
			<div id="fileInfo"></div>
			<br>

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

			<br>
			<?=$this->form->submit('Update Event')?>
		</div>
		<div id="event_items">
			<h3 id="">Item Management</h3>
			<hr />

			<div style="width:300px; height:400px; float:left">
				<h3 id="">Upload Items</h3>
	            <hr />
				<p>Please select the default option for all items being uploaded:</p>
					<input type="radio" name="enable_items" value="1" id="enable_items"> Enable All <br>
					<input type="radio" name="enable_items" value="0" id="enable_items" checked> Disable All <br><br>
				<p>Add "Final Sale" to the item description?:</p>
					<input type="radio" name="enable_finalsale" value="1" id="enable_finalsale" checked>Yes <br>
					<input type="radio" name="enable_finalsale" value="0" id="enable_finalsale">No<br><br>

					<!--
					<?=$this->form->label('Upload Event (Excel Files): '); ?>
					<?=$this->form->file('upload_file'); ?>
					-->

				<?=$this->form->field('items_submit', array('type' => 'textarea', 'rows' => '7', 'cols' => '50', 'name' => 'ItemsSubmit'));?><br>


			<?=$this->form->submit('Update Event')?>
			<?=$this->form->end(); ?>
			</div>

			<div id="items_errors" name="items_errors" style="float:right; width:500px; height:400px;overflow:scroll;"></div>

			<div style="clear:both; height:30px;"></div>



			<h3 id="current_items">Current Items</h3>

            <hr />
			<?=$this->form->create(null, array('url' => 'Items::itemUpdate', 'name' => 'item-update')); ?>
				<?=$this->form->hidden('id', array('value' => $event->_id)); ?>
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

				<?=$this->items->build($eventItems);?>

				<div style="float:right; font: bold; font-size: 18px;">
					<?=$this->form->submit('Update Event')?>
				</div>
			<?=$this->form->end(); ?>

			<br><br>


			<h2 id="">Delete Items</h2>
				<p>Click the button below to delete all items from this event. <strong>WARNING - This action cannot be undone. All items associated with this event will be deleted!!!!!!<strong></p>
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
					<td><?=$event->modifications[$i]->author;?></td>
					<td>
					<?php
							$date_changed = $event->modifications[$i]->date;
							print date('Y-M-d h:i:s', $date_changed->sec);
					?>
					</td>
					<td><?=$event->modifications[$i]->changed;?></td>
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
	</div>
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