<?php echo $this->html->script('swfupload.js');?>
<?php echo $this->html->script('swfupload.queue.js');?>
<?php echo $this->html->script('fileprogress.js');?>
<?php echo $this->html->script('handlers.js');?>
<?php echo $this->html->script('upload.js');?>
<?php echo $this->html->style('swfupload')?>

<h1>Upload files for a particular item</h1>

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

