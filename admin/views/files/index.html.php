<?=$this->html->script('swfupload.js');?>
<?=$this->html->script('swfupload.queue.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('upload.js');?>
<?=$this->html->style('swfupload')?>

<h1>Upload files for a particular item</h1>
<p>
	You may also use WebDAV for uploading files.
	Open your WebDAV client and connect to
	<?=$this->html->link($this->url('Files::dav', array('absolute' => true)), 'Files::dav'); ?>.
</p>
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

