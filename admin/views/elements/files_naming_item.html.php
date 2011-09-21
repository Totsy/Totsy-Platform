<?php

extract(array(
	'item' => null
), EXTR_SKIP);

?>
<div class="box files naming">
	<h2>Item Image Naming Conventions</h2>
	<div class="block">
		<p>
			Item images can only be uploaded from an item-, events-edit-page or through WebDAV.
		</p>
		<?php if ($item): ?>
			<p>
				The following names are <strong>actual names</strong> and can be copied and pasted.
			</p>
			<p>
				Filenames with slashes cannot be uploaded. Please upload those
				files without a slash in the name first, then rename the file
				manually in the <em>pending files</em> section.
				Alternatively you can upload files using WebDAV without the
				need of renaming the files.
			</p>
			<dl>
				<dt>Primary</dt>
				<dd>items_<?=$item->vendor_style; ?>_p.jpg</dd>
				<dd>items_<?=$item->vendor_style; ?>_primary.jpg</dd>

				<dt>Zoom</dt>
				<dd>items_<?=$item->vendor_style; ?>_z.jpg</dd>
				<dd>items_<?=$item->vendor_style; ?>_zoom.jpg</dd>

				<dt>Alternate</dt>
				<dd>items_<?=$item->vendor_style; ?>_a.jpg</dd>
				<dd>items_<?=$item->vendor_style; ?>_aB.jpg</dd>
				<dd>items_<?=$item->vendor_style; ?>_a0.jpg <em>etc.</em></dd>
				<dd>items_<?=$item->vendor_style; ?>_alternate.jpg</dd>
				<dd>items_<?=$item->vendor_style; ?>_alternateB.jpg</dd>
				<dd>items_<?=$item->vendor_style; ?>_alternate0.jpg <em>etc.</em></dd>
			</dl>
		<?php else: ?>
			<p>
				<em>VENDOR_STYLE</em> values can contain a mixture of uppercase,
				lowercase letters, as well as underscores, spaces, and dashes.
			</p>
			<p>
				Filenames with slashes cannot be uploaded. Please upload those
				files without a slash in the name first, then rename the file
				manually in the <em>pending files</em> section.
				Alternatively you can upload files using WebDAV without the
				need of renaming the files.
			</p>
			<dl>
				<dt>Primary</dt>
				<dd>items_VENDOR_STYLE_p.jpg</dd>
				<dd>items_VENDOR_STYLE_primary.jpg</dd>

				<dt>Zoom</dt>
				<dd>items_VENDOR_STYLE_z.jpg</dd>
				<dd>items_VENDOR_STYLE_zoom.jpg</dd>

				<dt>Alternate</dt>
				<dd>items_VENDOR_STYLE_a.jpg</dd>
				<dd>items_VENDOR_STYLE_aB.jpg</dd>
				<dd>items_VENDOR_STYLE_a0.jpg <em>etc.</em></dd>
				<dd>items_VENDOR_STYLE_alternate.jpg</dd>
				<dd>items_VENDOR_STYLE_alternateB.jpg</dd>
				<dd>items_VENDOR_STYLE_alternate0.jpg <em>etc.</em></dd>
			</dl>
		<?php endif; ?>
	</div>
</div>