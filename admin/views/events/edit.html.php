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

<?=$this->html->link('See Event List','/events')?>


<h1>Edit an Event</h1>
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
		<input type="radio" name="enabled" value="1" id="enabled"> Enable Event <br>
		<input type="radio" name="enabled" value="0" id="enabled" checked> Disable Event
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
<h1 id="current_images">Current Images</h1>
<?php 
	$preview_image = (empty($event->images->preview_image)) ? null : $event->images->preview_image;
	$banner_image = (empty($event->images->banner_image)) ? null : $event->images->banner_image; 
	$logo_image = (empty($event->images->logo_image)) ? null : $event->images->logo_image; 
?>


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
		<td>Preview Image</td>
		<td align="center">
			<?=$this->html->image("/image/$preview_image.jpg", array('alt' => 'altText')); ?>
		</td>
	</tr>
	<tr>
		<td>Banner Image</td>
		<td align="center">
			<?=$this->html->image("/image/$banner_image.jpg", array('alt' => 'altText')); ?>
		</td>
	</tr>
	<tr>
		<td>Logo Image</td>
		<td align="center">
			<?=$this->html->image("/image/$logo_image.jpg", array('alt' => 'altText')); ?>
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
<h1 id="">Upload Event Items</h1>
<br>
<div id="event_items">
		<?=$this->form->label('Upload Event CSV: '); ?>
		<?=$this->form->file('upload_file'); ?>
</div>
<h1 id="current_items">Current Items</h1>
<?=$this->items->build($eventItems);?>

<br>
<br>
<?=$this->form->submit('Update Event')?>
<?=$this->form->end(); ?>