<?=$this->html->script(array('tiny_mce/tiny_mce.js', 'fileprogress.js', 'handlers.js', 'jquery.dataTables.js', 'jquery-ui-timepicker.min.js'));?>
<?=$this->html->style(array('jquery_ui_blitzer', 'table', 'timepicker'));?>
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


<div class="grid_16">
	<h2 id="page-heading">Add a Banner</h2>
</div>
<div id="banner_note">
	<p>
		This panel is for creating new banners that will go up the day.
	</p>
</div>
<h2 id="banner_description">Banner Description</h2>
					<form id="BannerMedia">
						<?php
							// Without this banner_id being passed along with the files,
							// Item images could not be saved.
						?>
						<input type="hidden" name="banner_id" value="<?=$prospective_id?>" />
					</form>

<?=$this->form->create('', array('id' => 'banner_form' ,'enctype' => "multipart/form-data")); ?>
    <?=$this->form->field('name', array('class' => 'general'));?>
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
					<h2>Upload via Form</h2>
					<div id="agile_file_upload"></div>
					<script type="text/javascript">
						$('#agile_file_upload').agileUploader({
							flashSrc: "<?=$this->url('/swf/agile-uploader.swf'); ?>",
							submitRedirect: '<?=$this->url("/banners/edit/$prospective_id"); ?>',
							formId: 'BannerMedia',
							flashWidth: 70,
							removeIcon: "<?=$this->url('/img/agile_uploader/trash-icon.png'); ?>",
							flashVars: {
								button_up: "<?=$this->url('/img/agile_uploader/add-file.png?v=1'); ?>",
								button_down: "<?=$this->url('/img/agile_uploader/add-file.png'); ?>",
								button_over: "<?=$this->url('/img/agile_uploader/add-file.png'); ?>",
								//form_action: $('#bannerEdit').attr('action'),
								form_action: "<?=$this->url('/files/upload/all'); ?>",
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
						onClick="BeforeSubmit()"
					>
						Start Upload <?=$this->html->image('agile_uploader/upload-icon.png', array('height' => '24')); ?>
					</a>
				</div>
			</div>

			<div class="clear"></div>
		</div>
		<!-- End Tab -->

	<?=$this->form->submit('Add Banner')?>
<?=$this->form->end(); ?>

<script>
	function BeforeSubmit(){
		var bannerName = $("input:text[name='name']").val();
		var enddate = $("input:text[name='end_date']").val();
		var publish = $("input:text[name='enabled']").val();
		var banner_id = '<?=$prospective_id?>';
		var url = "<?=$this->url('/banners/add'); ?>";$.post(url,{name:bannerName,end_date:enddate,enabled:publish,banner_id:banner_id},function(data){
		    document.getElementById('agileUploaderSWF').submit();
		});

	}
</script>
