
<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('swfupload.js');?>
<?=$this->html->script('swfupload.queue.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('banner_upload.js');?>
<?=$this->html->style('swfupload')?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-timepicker.min.js');?>
<?=$this->html->style('timepicker'); ?>
<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>

<?=$this->html->script('jquery.flash.min.js')?>
<?=$this->html->script('agile-uploader-3.0.js')?>
<?=$this->html->style('agile_uploader.css');?>
<?=$this->html->style('admin_common.css');?>

<?=$this->html->script('files.js');?>
<?=$this->html->style('files.css');?>


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

<script type="text/javascript">
	$(document).ready(function(){
		$("#duplicate").dynamicForm("#plus", "#minus", {limit:15, createColor: 'yellow', removeColor: 'red'});
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
		var bannerItems = document.getElementById('banner_items');
		bannerItems.innerHTML = bannerItems.innerHTML + aReturn;
		return aReturn;
	}


</script>

<?=$this->form->create($banner, array('enctype' => "multipart/form-data")); ?>
<div class="grid_16">
	<h2>Editing Banner - <?php echo $banner->name?></h2>
</div>

<div class="grid_16">
	<div id="tabs">
		<ul>
		    <li><a href="#banner_info"><span>Banner Info</span></a></li>
			<li><a href="#banner_images"><span>Banner Images</span></a></li>
		</ul>

		<div id="banner_info">
			<div id="banner_note">
				<p>
					This panel is for creating new banners that will go up that day.
				</p>
			</div>
			<div id="banner_preview">
				<p> To see a preview of the banner please <?php echo $this->html->link('click here.',"/banners/preview/$banner->_id")?></p>
			</div>
			<h4 id="article-heading">Banner Description</h4>
			    <?php echo $this->form->field('name', array('value' => $banner->name, 'class' => 'general'));?>

				<div id="banner_status">
					<h4 id="banner_status">Banner Status</h4>
					<?php if ($banner->enabled): ?>
						<p>The banner is currently published for viewing</p>
						<?php $checked = 'checked';?>
					<?php else: ?>
						<p>The banner is NOT published for viewing</p>
						<?php $checked = '';?>
					<?php endif ?>
					<?php echo $this->form->checkbox("enabled", array('value' => 1, 'checked'=>$checked)) ?>
					Publish <br/><br/>
				</div>
				<div id="banner_duration">
					<h4 id="banner_duration">Banner Duration</h4>
					<?php
						$end_date =  date('m/d/Y H:i', $banner->end_date->sec);
					?>
					<?php echo $this->form->field('end_date', array(
								'class' => 'general',
								'id' => 'end_date',
								'value' => "$end_date"
							));?>
				</div>

				<br>
			<?=$this->form->submit('Update banner')?>
		</div>
		<div id="banner_images">
			<h3 id="current_images">Current Images</h3>
			<strong>If you have add a url make sure the http:// is in the url.</strong>
            <hr />
				<table border="1" cellspacing="30" cellpadding="30">
				<tr>
					<th align="justify">
						Image
					</th>
					<th align="justify">
						URL
					</th>
					<th align="justify">
						Open New Page
					</th>
				</tr>
				<?php foreach($banner->img as $image):?>
                    <tr>
                        <td align="center">
                            <?php
                                    $bannerImage = "/image/{$image['_id']}.jpg";
                            ?>
                            <?php echo $this->html->image("$bannerImage", array('alt' => 'altText')); ?>
                        </td>

                        <td align="center">
                            <?php
                                    $bannerurl = "{$image['url']}";
                                    $id = "{$image['_id']}";
                                     if (array_key_exists('newPage', $image->data()) && $image['newPage']) {
                                        $checkbox = 'checked';
                                    } else {
                                        $checkbox = "";
                                    }
                            ?>
                            <input type="text" name="url[<?php echo $id; ?>]" value= "<?php echo  $bannerurl; ?>"/>
                        </td>
                        <td align="center">
                            <?php echo $this->form->checkbox("newPage", array("value" => "1", "checked" => $checkbox)) ?>
                        </td>
                         <td align="center">
                            <input type="hidden" name="img[]" value="<?php echo $id; ?>"/>
                        </td>
                    </tr>
				<?php endforeach;?>
				</table>
<?=$this->form->end(); ?>

			<h3 id="uploaded_media">Uploaded Media</h3>
            <hr />
					<h2>Upload via Form</h2>
					<form id="BannerMedia">
						<?php
							// Without this banner_id being passed along with the files,
							// Item images could not be saved.
						?>
						<input type="hidden" name="banner_id" value="<?=$banner->_id?>" />
					</form>
					<div id="agile_file_upload"></div>
					<script type="text/javascript">
						$('#agile_file_upload').agileUploader({
							flashSrc: '<?=$this->url('/swf/agile-uploader.swf'); ?>',
							submitRedirect: '<?=$this->url('/banners/edit/' . (string)$banner->_id); ?>',
							formId: 'BannerMedia',
							flashWidth: 70,
							removeIcon: '<?=$this->url('/img/agile_uploader/trash-icon.png'); ?>',
							flashVars: {
								button_up: '<?=$this->url('/img/agile_uploader/add-file.png?v=1'); ?>',
								button_down: '<?=$this->url('/img/agile_uploader/add-file.png'); ?>',
								button_over: '<?=$this->url('/img/agile_uploader/add-file.png'); ?>',
								//form_action: $('#bannerEdit').attr('action'),
								form_action: '<?=$this->url('/files/upload/all'); ?>',
								file_limit: 30,
								max_height: '1000',
								max_width: '1000',
								file_filter: '*.jpg;*.jpeg;*.gif;*.png;*.JPG;*.JPEG;*.GIF;*.PNG',
								resize: 'jpg,jpeg,gif',
								force_preview_thumbnail: 'true',
								firebug: 'true'
							}
						});
					</script>

					<a
						href="#"
						class="upload_files_link"
						onClick="document.getElementById('agileUploaderSWF').submit();"
					>
						Start Upload <?=$this->html->image('agile_uploader/upload-icon.png', array('height' => '24')); ?>
					</a>
				</div>
			</div>

			<div class="clear"></div>
			<?=$this->view()->render(array('element' => 'files_pending'), array('item' => $banner)); ?>
		</div>
		<!-- End Tab -->

		<!-- Start Tab -->
		<div id="banner_media_status">
			<div class="actions">
				<?=$this->html->link('refresh', array(
					'action' => 'media_status', 'id' => $banner->_id
				), array(
					'class' => 'refresh', 'target' => '#banner_media_status_data'
				)); ?>
			</div>
			<p>
				This tab show the status of media associated with the items of this banner.
			</p>
			<div id="banner_media_status_data"><!-- Populated through AJAX request. --></div>
		</div>
		<!-- End Tab -->





			<br>
			<?=$this->form->submit('Update banner')?>
		</div>
	</div>




</div>
<script type="text/javascript">
$(document).ready(function() {

	//create tabs
	$("#tabs").tabs();
});
</script>
<script type="text/javascript">
	jQuery(function($){
	   $("#ship_date").mask("99/99/9999");
	});
</script>