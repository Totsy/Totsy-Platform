<?=$this->html->script('swfupload.js');?>
<?=$this->html->script('swfupload.queue.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('jquery.flash.min.js')?>
<?=$this->html->script('agile-uploader-3.0.js')?>
<?=$this->html->style('agile_uploader.css');?>
<?=$this->html->style('admin_common.css');?>

<h1>Files</h1>

<div class="tab_region_left_col">

	<div class="box">
		<h2>Upload via WebDAV</h2>
		<div class="block">
			<p>
				Open your WebDAV client and connect to
				<?=$this->html->link($this->url('Files::dav', array('absolute' => true)), 'Files::dav'); ?>.
			</p>
			<p>
				<?=$this->html->link('Cyberduck', 'http://cyberduck.ch/', array('target' => 'new')); ?>
				is the recommended WebDAV client and works under both Windows and OSX.
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
						flashSrc: '/admin/swf/agile-uploader.swf',
						submitRedirect: '/admin/files',
						formId: 'Media',
						removeIcon: '/admin/img/agile_uploader/trash-icon.png',
						flashVars: {
							button_up: '/admin/img/agile_uploader/add-file.png',
							button_down: '/admin/img/agile_uploader/add-file.png',
							button_over: '/admin/img/agile_uploader/add-file.png',
							//form_action: $('#EventEdit').attr('action'),
							form_action: '/admin/files/upload/all',
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

				<a href="#" onClick="document.getElementById('agileUploaderSWF').submit();">Upload</a>
			</p>
		</div>
	</div>

</div>

<div class="tab_region_right_col">

	<div class="box">
		<h2>Event Image File Naming Conventions</h2>
		<div class="block">
			<p>
				<strong>Event Image</strong><br />
				events_the-name.jpg <em>or</em> events_the-name_image.jpg<br />
				<strong>Event Logo</strong><br />
				events_the-name_logo.jpg<br />
				<strong>Event Small Splash Image</strong><br />
				events_the-name_small_splash.jpg <em>or</em> events_the-name_splash_small.jpg<br />
				<strong>Event Big Splash Image</strong><br />
				events_the-name_big_splash.jpg <em>or</em> events_the-name_splash_big.jpg<br />
			</p>
		</div>
	</div>
	<div class="box">
		<h2>Item Image File Naming Conventions</h2>
		<div class="block">
			<p>

				<strong>Primary Image</strong><br />
				items_shirt_primary.jpg<br />
				<strong>Zoom Image</strong><br />
				items_shirt_zoom.jpg<br />
				<strong>For Various Colors <em>(colors are a part of the url)</em></strong><br />
				items_shirt-yellow_primary.jpg, items_shirt-yellow_zoom.jpg<br />
				<strong>For Alternate Versions</strong><br />
				items_shirt-blue_alternate.jpg, items_shirt-blue_alternateB.jpg, items_shirt-blue_alternate0.jpg, etc.
			</p>
		</div>
	</div>

</div>

<div class="clear"></div>

<div class="box">
	<h2>Manage Pending &amp; Orphan Files</h2>
	<div class="block">
		<p>
			Files not yet associated with any item or event.
		</p>
		<div id="pending">
			<?=$this->html->link('Pending', 'Files::pending'); ?>
			<!-- This holds all pending files and is populated through an AJAX request. -->
		</div>
	</div>
</div>