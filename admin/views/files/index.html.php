<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>

<?=$this->html->script('files.js')?>
<?=$this->html->script('jquery.flash.min.js')?>
<?=$this->html->script('agile-uploader-3.0.js')?>
<?=$this->html->style('files.css');?>
<?=$this->html->style('agile_uploader.css');?>
<?=$this->html->style('admin_common.css');?>

<?php

$url = null;
$user = lithium\storage\Session::read('userLogin');

if (!empty($user['token'])) {
	try {
		$url = $this->url(
			array('library' => 'li3_dav', 'Files::dav', 'token' => $user['token']),
			array('absolute' => true)
		);
	} catch (\Exception $e) {
		$url = null;
	}
}

?>

<h1>File Management</h1>
<div class="tab_region_left_col">

	<div class="box">
		<h2>Upload via WebDAV</h2>
		<div class="block">
			<?php if (!$url): ?>
			<p>
				You currently have <strong>no token</strong>,
				you can <?=$this->html->link('generate one', 'Users::token'); ?> now.
				A token is needed to authenticate when using WebDAV.
			</p>
			<?php else: ?>
			<p>
				Open your <abbr title="Web-based Distributed Authoring and Versioning">WebDAV</abbr>
				client and connect to the following URL:
				<pre><?=$this->html->link($url, $url); ?></pre>
			</p>
			<?php endif; ?>
			<p>
				<?=$this->html->link('Cyberduck', 'http://cyberduck.ch/', array('target' => 'new')); ?>
				is the recommended WebDAV client and works under both Windows and OSX.
				Following a quick explanation of the directory structure.
				<dl>
					<dt>events</dt>
					<dd>
						Contains events and event items. Dropping files into image folders
						will associate them with the item. Deleting files from the folders
						will cause the files to be <em>disassociated</em>.

						Please note that in contrast to uploading files via form,
						files uploaded via WebDAV don't need to stick to the naming conventions
						unless these files are added as pending.
					</dd>

					<dt>orphaned</dt>
					<dd>
						Items in this folder can be deleted only.
					</dd>

					<dt>pending</dt>
					<dd>
						Drop files here that belong to future events/items that don't exist yet.
						Files can be added to and deleted from this folder.
						Files added should stick to the naming conventions in order to enable <em>auto-association</em>.
					</dd>
				</dl>
			</p>
		</div>
	</div>

	<div class="box">
		<h2>Upload via Form</h2>
		<div class="block">
			<p>
				Use this form to upload new files. These files will be marked as <em>pending</em> as long they are not
				associated with an event or item.
				<form id="Media"></form>
				<div id="agile_file_upload"></div>
				<script type="text/javascript">
					$('#agile_file_upload').agileUploader({
						flashSrc: '<?=$this->url('/swf/agile-uploader.swf'); ?>',
						submitRedirect: '<?=$this->url('/files'); ?>',
						formId: 'Media',
						removeIcon: '<?=$this->url('/img/agile_uploader/trash-icon.png'); ?>',
						flashVars: {
							button_up: '<?=$this->url('/img/agile_uploader/add-file.png'); ?>',
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
							firebug: 'false'
						}
					});
				</script>

				<a href="#" class="upload_files_link" onClick="document.getElementById('agileUploaderSWF').submit();">Start Upload <?=$this->html->image('agile_uploader/upload-icon.png', array('height' => '24')); ?></a>
				<br style="clear: left;" />
			</p>
		</div>
	</div>

</div>

<div class="tab_region_right_col">
	<div class="box">
		<h2>Token</h2>
		<?php if (empty($user['token'])): ?>
		<p>
			You currently have no token,
			you can <?=$this->html->link('generate one', 'Users::token'); ?> now.
		</p>
		<?php else: ?>
		<div class="actions">
			<?=$this->html->link('regenerate', 'Users::token'); ?>
		</div>
		<p>
			A token is needed to authenticate i.e. for <em>uploading via WebDAV</em>.
			You may regenerate your token any time causing the old token to expire immediately.
		</p>
		<pre><?=$user['token']; ?></pre>
		<?php endif; ?>
	</div>

	<div class="box files naming">
		<h2>Event Image File Naming Conventions</h2>
		<div class="block">
		<dl>
			<dt>Image</dt>
			<dd>events_the-name.jpg <em>or</em></dd>
			<dd>events_the-name_image.jpg</dd>

			<dt>Logo</dt>
			<dd>events_the-name_logo.jpg</dd>

			<dt>Small Splash Image</dt>
			<dd>events_the-name_small_splash.jpg <em>or</em></dd>
			<dd>events_the-name_splash_small.jpg</dd>

			<dt>Big Splash Image</dt>
			<dd>events_the-name_big_splash.jpg <em>or</em></dd>
			<dd>events_the-name_splash_big.jpg</dd>
		</dl>
		</div>
	</div>
	<div class="box files naming">
		<h2>Item Image File Naming Conventions</h2>
		<div class="block">
			<p><em>Note: Item images can only be uploaded from an events/edit page or through WebDAV where there is a reference to the event. VENDOR_STYLE values can contain a mixture of uppercase, lowercase letters, as well as underscores, spaces, and dashes. These values are found in the uploaded excel file for each event.</em></p>
		<dl>
			<dt>Primary Image</dt>
			<dd>items_VENDOR_STYLE_primary.jpg</dd>

			<dt>Zoom Image</dt>
			<dd>items_VENDOR_STYLE_zoom.jpg</dd>

			<dt>For Alternate Versions</dt>
			<dd>items_VENDOR_STYLE_alternate.jpg</dd>
			<dd>items_VENDOR_STYLE_alternateB.jpg</dd>
			<dd>items_VENDOR_STYLE_alternate0.jpg <em>etc.</em></dd>
		</dl>
		</div>
	</div>
</div>

<div class="clear"></div>
<?=$this->view()->render(array('element' => 'files_pending'), compact('item')); ?>

<div class="clear"></div>
<div id="orphaned" class="box">
	<h2>Manage Orphan Files</h2>
	<div class="actions">
		<?=$this->html->link('refresh', 'Files::orphaned', array(
			'class' => 'refresh', 'target' => '#orphaned-data'
		)); ?>
	</div>
	<div class="block">
		<p>
			Files that have been associated with an item or event but are <em>not in use</em> anymore.
			These files can probably be deleted in order to free space.<br/>
			Files must be flagged as orphaned, to do so run the <em>file-orphaned command</em>.
		</p>
		<div id="orphaned-data"><!-- Populated through an AJAX request. --></div>
	</div>
</div>