<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('jquery.editable-1.3.3.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<!--This is the image upload tool js and css-->
<?=$this->html->script('jquery.flash.min.js')?>
<?=$this->html->script('agile-uploader-3.0.js')?>
<?=$this->html->style('agile_uploader.css');?>
<?=$this->html->style('admin_common.css');?>
<?=$this->html->script('files.js');?>
<?=$this->html->style('files.css');?>

<script type="text/javascript">
var affiliateCategories = <?=json_encode($affiliateCategories)?>;
var affiliateCodes = "";
</script>

<div class="grid_16">
	<h2 id="page-heading">Affiliate Add Panel</h2>
</div>
<div class="grid_3 menu">
	<table>
		<thead>
			<tr>
				<th>Affiliate Navigation </th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?=$this->html->link('Create Affiliate', 'affiliates/add'); ?> </td>
			</tr>
			<tr>
				<td><?=$this->html->link('View/Edit Affiliate', 'affiliates/index' ); ?></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="clear"></div>
<div>
	<form id = "AffiliateId">
		<input type="hidden" name="affiliate_id" value="<?=(string)$prospective_id?>">
	</form>
</div>

<?=$this->form->create(null,array("id"=>"affForm")); ?>
    <div id="submit button" class="grid_16">
		<div class="grid_2" >
			<?=$this->form->submit('Create', array('id'=>'create')); ?>
		</div>
	</div>
<div class="grid_7 box">
	<div class="block forms">
		<input type="hidden" name="affiliate_id" value="<?=(string)$prospective_id?>">
		Activate: <?=$this->form->checkbox('active', array('checked'=>'checked')); ?> <br/>
		Affiliate Level: <?=$this->form->select('level',$packages); ?> <br/><br/>
		Affiliate Name:
		<?=$this->form->text('affiliate_name'); ?> <br/><br/>
		Enter Code:
		<?=$this->form->text('code'); ?>  <input type="button" name="add_code" id="add_code" value="add"/>
		<br/>
		Affiliate codes:<br/>
		<?=$this->form->select('invitation_codes',array(),array('multiple'=>'multiple', 'size'=>5)); ?> <br/>
		<input type="button" name="edit_code" id="edit_code" value="Edit code"/>
		<br><br>

	</div>
</div> <!--end of box-->
<div class ="grid_8 box">
	<div class="block forms">
		<div id="tabs">
			<ul>
				<li id="pixel_tab"><a href="#pixel"><span>Pixels</span></a></li>
				<li id="landing_tab"><a href="#landing_page"><span>Dynamic Pages</span></a></li>
				<li id="pending_tab"><a href="#pending_page"><span>Pending Backgrounds</span></a></li>
			</ul>
			<div id="pixel">
				<div id="pixel_activate">
				    Affiliate uses pixels: <?=$this->form->checkbox('active_pixel', array('value'=>'1')); ?>
				</div>
				<div id="pixel_panel">
					<br/>
					<h5>Add Pixels</h5>
					<div id="pixel_1">
						Pixel:<br>
						Enable:
						<?=$this->form->checkbox('pixel[0][enable]', array('value'=>'1', 'checked'=>'checked')); ?>
						<br>
						Select Page(s):
						<br/>
						<?=$this->form->select('pixel[0][page]', $sitePages, array('multiple'=>'multiple', 'size'=>5)); ?>
						<br/>
						<br/>
						Select code(s) pixel applies to:
						<br/>
						<?=$this->form->select('pixel[0][codes]',array('all'=>'all'), array('multiple'=>'multiple', 'size'=>5, 'class' => 'relevantCodes')); ?>
						<br/>
						<br/>
						<?=$this->form->textarea('pixel[0][pixel]', array('rows'=>6,'cols'=>50)); ?>
				    </div>
				</div><!--end of pixel panel-->
			</div><!--end of pixel-->
			<div id="landing_page">
			    <div id="landing_activate">			        Affiliate uses dynamic landing Pages:
			        <?=$this->form->checkbox('active_landing', array('value'=>'1'));?>
			    </div>
			    <p>
					<strong> Upload backgroud images for landing pages.  You can associate the images in the edit view.</strong>
			    </p>
			    <div id="landing_panel">
					<div id="agile_file_upload"></div>
					<script type="text/javascript">
						$('#agile_file_upload').agileUploader({
							flashSrc: "<?=$this->url('/swf/agile-uploader.swf'); ?>",
							formId: 'AffiliateId',
							flashWidth: 70,
							removeIcon: "<?=$this->url('/img/agile_uploader/trash-icon.png'); ?>",
							flashVars: {
								button_up: "<?=$this->url('/img/agile_uploader/add-file.png?v=1'); ?>",
								button_down: "<?=$this->url('/img/agile_uploader/add-file.png'); ?>",
								button_over: "<?=$this->url('/img/agile_uploader/add-file.png'); ?>",
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
						onClick="document.getElementById('agileUploaderSWF').submit();"
					>
						Start Upload <?=$this->html->image('agile_uploader/upload-icon.png', array('height' => '24')); ?>
					</a>
			    </div><!--end of landing_panel-->
			</div><!--end of landing_page-->
			<div id="pending_page">
				<?=$this->view()->render(array('element' => 'files_pending'), array('item' => $affiliate,'search_type' => 'affiliate')); ?>
			</div>
		</div><!--end tabs-->
	</div>
</div>
	<div class="clear"></div>
	<?=$this->form->end(); ?>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		//create tabs
		$("#tabs").tabs();
	});
</script>
<script type="text/javascript">

	$(document).ready(function(){
		//Initial setup
		$('#pixel_panel').hide();
		$('#landing_panel').hide();
		$('#tabs').hide();
		//Activating pixel panel
		$('input[name=active_pixel]').change(function(){
			if( $('#ActivePixel:checked').val() == 1){
				$('#pixel_panel').show();
			}else{
				$('#pixel_panel').hide();
			}
		});
		//Activating landing page panel
		$('input[name=active_landing]').change(function(){
			if( $('#ActiveLanding:checked').val() == 1){
				$('#landing_panel').show();
			}else{
				$('#landing_panel').hide();
			}
		});

		if( $('#Level').val() != 'regular' ){
			$('#tabs').show();
		}else{
			$('#tabs').hide();
		}

		$('#Level').change(function(){
			if( $('#Level').val() != 'regular' ){
				$('#tabs').show();
			}else{
				$('#pixel_panel').hide();
				$('#landing_panel').hide();
				$('#tabs').hide();
			}
		});
		//this jquery is for adding/removing pixel entry fields
		var counter = 2;
		$('#add_pixel').click(function(){
		    codes = getCodes();
			var newPixelDiv = $(document.createElement('div')).attr("id", "pixel_"+counter);
			newPixelDiv.html("<label> Pixel #" +counter + "</label> <br/> Enable:"+
				'<?=$this->form->checkbox("pixel['+(counter-1)+'][enable]", array("value"=>"1", "checked"=>"checked")); ?> <br/> Select Page:'+
				'<?=$this->form->select("pixel['+(counter-1)+'][page]", $sitePages, array("multiple"=>"multiple", "size"=>5)); ?><br/> Select code:'+
				'<select name="pixel['+(counter-1)+'][codes][]" multiple="multiple"  size=5 class = "relevantCodes">' + codes+'</select><br/>Pixel<br/>'+
				'<?=$this->form->textarea("pixel['+(counter-1)+'][pixel]", array("rows"=>"6", "cols" => "50")); ?>'
				);
			newPixelDiv.appendTo('#pixel_panel');
			counter++;
		});

		$('#remove_pixel').click(function(){
			counter--;
			if(counter==1){
				alert('No more textbox to remove');
				return false;
			}
			$('#pixel_' + counter).remove();
		});
	});

	//multi select transfer
	$(document).ready(function(){
        //add affiliate code to select box
		$('#add_code').click(function(){
			var value= $('#Code').val();
			if(value){
				$('#InvitationCodes').append("<option value="+value+">"+value+"</option>");
				$('.relevantCodes').append("<option value="+value+">"+value+"</option>");
				$('#Code').attr('value', "");
			}
		});
		//removes affiliate code to select box to text box
		$('#edit_code').click(function(){
			var value = $('#InvitationCodes option:selected').val();
			$('.relevantCodes').val(value);
			$('#InvitationCodes option:selected').remove();
			$('.relevantCodes option:selected').remove();
			$('#Code').attr('value',value);
		});
	});
	//select all codes upon submit
	$(document).ready(function(){
		$('#create').click(function(){
			$('#InvitationCodes').each(function(){
				$('#InvitationCodes option').attr('selected','selected');
			});
		});
	});

</script>
<script type="text/javascript">
function getCodes() {
	var tmp = "<option value='all'>all</option>";

	$('#InvitationCodes option').each(function(index,val){
		tmp = tmp + "<option value=" + $(val).text() + ">" + $(val).text() + "</option>";
	});
	return tmp;
}
</script>
