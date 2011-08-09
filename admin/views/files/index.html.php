<?php echo $this->html->script('swfupload.js');?>
<?php echo $this->html->script('swfupload.queue.js');?>
<?php echo $this->html->script('fileprogress.js');?>
<?php echo $this->html->script('handlers.js');?>
<?php echo $this->html->script('upload.js');?>
<?php echo $this->html->style('swfupload')?>

<h1>Files</h1>
<h2>Upload via WebDAV</h2>
<p>
	Open your WebDAV client and connect to
	<?=$this->html->link($this->url('Files::dav', array('absolute' => true)), 'Files::dav'); ?>.
	<?=$this->html->link('Cyberduck', 'http://cyberduck.ch/', array('target' => 'new')); ?>
	is the recommended WebDAV client and works under both Windows and OSX.
</p>

<h2>Upload via Form</h2>
<p>
	Use this form to upload new files. These files will be marked as <em>pending</em> as long they are not
	associated with an event or item.
</p>
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

<h2>Manage Pending</h2>
<p>
	Files not yet associated with any item or event.
</p>
<div id="pending">
	<!-- This holds all pending files and is populated through an AJAX request. -->
</div>
