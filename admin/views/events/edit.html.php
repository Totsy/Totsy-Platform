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
<?=$this->html->style('admin');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->style('timepicker'); ?>

<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "simple",
	width : "600"
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


<h1 id="event">Editing Event - <?=$event->name?> </h1>
<?=$this->html->link('Return to Event List','/events')?>
<div id="tabs">
	<ul>
	    <li><a href="#event_info"><span>Event Info</span></a></li>
		<li><a href="#event_images"><span>Event Images</span></a></li>
	    <li><a href="#event_items"><span>Event Items</span></a></li>
	    <!--<li><a href="#video"><span>Video</span></a></li>-->
	</ul>
	
	<div id="event_info">
		<div id="event_note">
			<p>
				Hello administrator. Please edit an event by filling in all the information below. Thank You!
			</p>
		</div>
		<div id="event_preview">
			<p> To see a preview of the event please <?=$this->html->link('click here.',"/events/view/$event->_id")?></p>
		</div>
		<h2 id="event_description">Event Description</h2>

		<?=$this->form->create(null, array('enctype' => "multipart/form-data")); ?>
		    <?=$this->form->field('name', array('value' => $event->name, 'class' => 'general'));?>
		    <?=$this->form->field('blurb', array('type' => 'textarea', 'name' => 'content', 'value' => $event->blurb));?>
			<div id="event_status">
				<h2 id="event_status">Event Status</h2>
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
			<div id="event_duration">
				<h2 id="event_duration">Event Duration</h2>
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
		<?=$this->form->submit('Update Event')?>
	</div>
	<div id="event_images">
		<h1 id="current_images">Current Images</h1>
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

		<h1 id="uploaded_media">Uploaded Media</h1>
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
		<h1 id="">Item Management</h1>
		<br>
		<h2 id="">Upload Items</h2>
		<br>
			<?=$this->form->label('Upload Event CSV: '); ?>
			<?=$this->form->file('upload_file'); ?>
		<br><br>
		<h2 id="current_items">Current Items</h2><br>
		<?=$this->items->build($eventItems);?>
		<br><br>
		<?=$this->form->submit('Update Event')?>
	</div>
</div>

<?=$this->form->end(); ?>
<script type="text/javascript">
$(document).ready(function() {

	//create tabs
	$("#tabs").tabs();
});
</script>