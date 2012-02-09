<?php echo $this->html->script(array('tiny_mce/tiny_mce.js', 'swfupload.js', 'swfupload.queue.js', 'fileprogress.js', 'handlers.js', 'event_upload.js', 'jquery.dataTables.js', 'jquery-ui-timepicker.min.js'));?>
<?php echo $this->html->style(array('swfupload', 'jquery_ui_blitzer', 'table', 'timepicker'));?>
<script type="text/javascript">
tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,preview,searchreplace,print,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras",
	editor_deselector : "mceNoEditor",
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


<script type="text/javascript" charset="utf-8">
	$(function() {
		var dates = $('#start_date, #end_date').datetimepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				var option = this.id == "start_date" ? "minDate" : "maxDate";
				//var instance = $(this).data("datetimepicker");
				//var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				//dates.not(this).datepicker("option", option, date);
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

		$('#eventForm').submit(function() {
			var eventName  = document.getElementById('Name').value,
				eventCount = 0;

			if (eventName.length > 32) {
				alert("The event name '" + eventName + "' is too long. Event names cannot exceed 32 characters.");
				return false;
			}

			$.ajax({
				url: 'find',
				dataType: 'json',
				data: {
					name: eventName
				},
				async: false,
				success: function(data, status) {
					if ('success' == status) {
						eventCount = data.total;
					}
				}
			});

			if (eventCount > 0) {
			 	alert("An event with the name '" + eventName + "' already exists. Please use another event name.");
			 	return false;
			 }

			return true;
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
<div class="grid_16">
	<h2 id="page-heading">Add an Event</h2>
</div>
<div id="event_note">
	<p>
		Hello administrator. Please add an event by filling in all the information below. Thank You!
	</p>
</div>
<h2 id="event_description">Event Description</h2>
<?php echo $this->form->create(null, array('enctype' => "multipart/form-data", 'id' => 'eventForm')); ?>
    <?php echo $this->form->field('name', array('class' => 'general'));?>
    <?php echo $this->form->field('blurb', array('type' => 'textarea', 'name' => 'content'));?>
    <div style="width:450px;">
    	<?php echo $this->form->field('short', array('type' => 'textarea', 'name' => 'short_description', 'class' => 'mceNoEditor shortDescription'));?>
    	<div id="short_description_characters_wrapper">Total: <span id="short_description_characters_counter">0</span>/<?php echo $shortDescLimit;?></div>
    </div>
	<div id="event_status">
		<h2 id="event_status">Event Status</h2>
		<input type="radio" name="enabled" value="1" id="enabled"> Enable Event <br>
		<input type="radio" name="enabled" value="0" id="enabled" checked> Disable Event
	</div>
	<div id="event_type">
		<h2 id="event_type">Event Type</h2>
		<input type="radio" name="tangible" value="1" id="tangible" checked> Tangible <br>
		<input type="radio" name="tangible" value="0" id="tangible"> Non Tangible
	</div>
	<div id="event_status_update">
		<h2 id="event_status_upadte">Event Status Update</h2>
		<input type="radio" name="status_update" value="none" id="status_update"> None <br>
		<input type="radio" name="status_update" value="stock_added" id="status_update"> Stock Added <br>
		<input type="radio" name="status_update" value="styles_added" id="status_update"> Styles Added <br>
		<input type="radio" name="status_update" value="blowout" id="status_update"> Blowout <br>
		<input type="radio" name="status_update" value="charity" id="status_update"> Charity Eevent <br>
		<input type="radio" name="status_update" value="sold_out" id="status_update"> Sold Out 
	</div>
	<div id="event_viewlive">
		<h2 id="event_type">View Live Anyway</h2>
		 (allows direct url access to event even if otherwise disabled)<br>
		<input type="radio" name="viewlive" value="1" id="viewlive"> Direct URL <br>
		<input type="radio" name="viewlive" value="0" id="viewlive" checked> Not Viewable
	</div>
	<div id="event_clearance">
		<h2 id="event_type">Clearance</h2>
		<input type="radio" name="clearance" value="1" id="clearance"> Clearance <br>
		<input type="radio" name="clearance" value="0" id="clearance" checked> Not Clearance
	</div>

	<div id="event_duration">
		<h2 id="event_duration">Event Duration</h2>
		<?php echo $this->form->field('start_date', array('class' => 'general', 'id' => 'start_date'));?>
		<?php echo $this->form->field('end_date', array('class' => 'general', 'id' => 'end_date'));?>
	</div>
	<br>
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
	<?php echo $this->form->submit('Add Event')?>
<?php echo $this->form->end(); ?>
