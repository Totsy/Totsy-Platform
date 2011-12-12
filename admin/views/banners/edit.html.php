
<?php echo $this->html->script('tiny_mce/tiny_mce.js');?>
<?php echo $this->html->script('jquery-1.4.2');?>
<?php echo $this->html->script('jquery-dynamic-form.js');?>
<?php echo $this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?php echo $this->html->script('swfupload.js');?>
<?php echo $this->html->script('swfupload.queue.js');?>
<?php echo $this->html->script('fileprogress.js');?>
<?php echo $this->html->script('handlers.js');?>
<?php echo $this->html->script('banner_upload.js');?>
<?php echo $this->html->style('swfupload')?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->script('jquery.countdown.min');?>
<?php echo $this->html->style('jquery.countdown');?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>

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

<?php echo $this->form->create($banner, array('enctype' => "multipart/form-data")); ?>
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
			<?php echo $this->form->submit('Update Event')?>
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

			<h3 id="uploaded_media">Uploaded Media</h3>
            <hr />
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
			<?php echo $this->form->submit('Update Event')?>
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