<?php echo $this->html->script(array('tiny_mce/tiny_mce.js', 'swfupload.js', 'swfupload.queue.js', 'fileprogress.js', 'handlers.js', 'banner_upload.js', 'jquery.dataTables.js', 'jquery-ui-timepicker.min.js'));?>
<?php echo $this->html->style(array('swfupload', 'jquery_ui_blitzer', 'table', 'timepicker'));?>
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


<script type="text/javascript" charset="utf-8">
	$(function() {
		var dates = $('#end_date').datetimepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				var option = this.id == "end_date" ? "minDate" : "maxDate";
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


<div class="grid_16">
	<h2 id="page-heading">Add a Banner</h2>
</div>
<div id="banner_note">
	<p>
		This panel is for creating new banners that will go up that day.
	</p>
</div>
<h2 id="banner_description">Banner Description</h2>
<?php echo $this->form->create($banner, array('enctype' => "multipart/form-data")); ?>
    <?php echo $this->form->field('name', array('class' => 'general'));?>
	<div id="banner_status">
		<h2 id="banner_status">Banner Status</h2>
		<input type="checkbox" name="enabled" value="1" id="enabled"> Publish Banner <br>
		<p><b>Note:</b> If you publish this banner, the previous banners will be disabled. </p>
	</div>
	<div id="banner_duration">
		<h2 id="banner_duration">Banner End Date</h2>
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
	<?php echo $this->form->submit('Add Banner')?>
<?php echo $this->form->end(); ?>
