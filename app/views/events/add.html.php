<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->style('admin.css')?>
<?=$this->html->script('swfupload.js');?>
<?=$this->html->script('swfupload.queue.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('upload.js');?>
<?=$this->html->style('swfupload')?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>

<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "simple"
});

</script>

<script type="text/javascript"> 
	$(document).ready(function(){	
		$("#duplicate").dynamicForm("#plus", "#minus", {limit:15, createColor: 'yellow', removeColor: 'red'});
	});
</script>

<script type="text/javascript" charset="utf-8">
	$(function() {
		var dates = $('#from, #to').datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 2,
			onSelect: function(selectedDate) {
				var option = this.id == "from" ? "minDate" : "maxDate";
				var instance = $(this).data("datepicker");
				var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);
			}
		});
	});
</script>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#itemTable').dataTable();
	} );
</script>

<?=$this->html->link('See Event List','/items')?>

<h1>Add an Event</h1>

<?=$this->form->create(); ?>
    <?=$this->form->field('Name');?>
    <?=$this->form->field('Description', array('type' => 'textarea', 'name' => 'content'));?>
	<br>
	<fieldset>
		<legend>Event Duration</legend>
		<label for="from">From</label><input type="text" name="from" value="" id="from">
		<label for="to">To</label><input type="text" name="to" value="" id="to">
	</fieldset>
<br>
<h1>Upload an event image</h1>

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
<div id="fileInfo"></div>
<br>
<h1 id="add_event_items">Add Event Items</h1>
<?=$this->items->build($items); ?>
<?=$this->form->end(); ?>