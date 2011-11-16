<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('swfupload.js');?>
<?=$this->html->script('swfupload.queue.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('jquery.editable-1.3.3.js');?>
<?=$this->html->script('affiliate_upload.js');?>
<?=$this->html->style('swfupload')?>
<?=$this->html->style('jquery_ui_blitzer.css')?>

<script type="text/javascript">
var affiliateCategories = <?=json_encode($affiliateCategories)?>;
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
				<td><?php echo $this->html->link('Create Affiliate', 'affiliates/add'); ?> </td>
			</tr>
			<tr>
				<td><?php echo $this->html->link('View/Edit Affiliate', 'affiliates/index' ); ?></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="clear"></div>
<div class="grid_8 box">
	<div class="block forms">
		<?=$this->form->create(null,array("id"=>"affForm")); ?>
		Activate: <?=$this->form->checkbox('active', array('checked'=>'checked')); ?> <br>
		Affiliate Level: <?=$this->form->select('level',$packages); ?> <br><br>
		<!--
		Affiliate Category: 
		<input type="text" id="affiliate_category" name="affiliate_category" autocomplete="off" class="textbox"><br><br> -->
		Affiliate Name:
		<?=$this->form->text('affiliate_name'); ?> <br><br>
		Enter Code:
		<?=$this->form->text('code'); ?>  <input type="button" name="add_code" id="add_code" value="add"/>
		<br>
		Affiliate codes:<br>
		<?=$this->form->select('invitation_codes',array(),array('multiple'=>'multiple', 'size'=>5)); ?> <br>
		<input type="button" name="edit_code" id="edit_code" value="Edit code"/>
		<br><br>
		<div id="upload_panel">
			<h5 id="uploaded_media">Uploaded Media</h5>
			    	<div id="fileInfo"></div>
			<table>
			    <tr valign="top">
			    	<td>
			    		<div>
			    			<div class="fieldset flash" id="fsUploadProgress1">
			    				<span class="legend">Upload Status</span>
			    			</div>
			    			<div style="padding-left: 5px;">
			    				<span id="spanButtonPlaceholder1" onclick="isBackground(upload1);"></span>
			    				<input id="btnCancel1" type="button" value="Cancel Uploads" onclick="cancelQueue(upload1);" disabled="disabled" style="margin-left: 2px; height: 22px; font-size: 8pt;" />												
			    			<br />
			    			</div>
			    		</div>
			    	</td>
			    </tr>
			</table>
		</div>
	</div>
</div> <!--end of box-->
<div class ="grid_7 box">
	<div class="block forms">
		<div id="tabs">
			<ul>
				<li>
					<a href="#pixel">
					<span>Pixels</span>
					</a>
				</li>
				<!--<li><a href="#landing_page"><span>Landing Pages</span></a></li> -->
			</ul>
			<div id="pixel">
				<div id="pixel_activate"> Affiliate uses pixels: <?=$this->form->checkbox('active_pixel', array('value'=>'1')); ?> </div>
				<div id="pixel_panel">
					<br>
					<h5>Add Pixels</h5>
					<div id="pixel">
						Pixel:<br>
						<?=$this->form->textarea('pixel[0][pixel]', array('rows'=>6,'cols'=>50)); ?>
						<br>
						<input type="button" name="add_pixel" value="add pixel" id="add_pixel"/>
						<input type="button" name="remove_pixel" value="remove pixel" id="remove_pixel"/>
						<br>
						<br>
						Enable:
						<?=$this->form->checkbox('pixel[0][enable]', array('value'=>'1', 'checked'=>'checked')); ?> 
						<br>
						Select Page(s):
						<br>
						<?=$this->form->select('pixel[0][page]', $sitePages, array('multiple'=>'multiple', 'size'=>5)); ?>
						<br>
						<br>
						<input type="hidden" name="background_image" value="" id="background_image"/>
				</div>
				</div><!--end of pixel panel-->
			</div><!--end of pixel-->
		</div><!--end tabs-->
	</div>
</div>

<!--
<div id="page_preview" style="display:none; width:800px; width: 500px; height: auto; z-index: 1000000000 !important; border-width: 2px; border-style: solid; background-color: rgb(255, 255, 255); left:445px; top: 15px; position: absolute;">
	<img id="background_image">
</div>
-->
		<br>
		<br>
	<div class="clear"></div>
	<div id="submit button" class="grid_2">
		<div class="grid_2" >
			<?=$this->form->submit('Create', array('id'=>'create')); ?>
		</div>
	</div>
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
		
		$("#affiliate_category").autocomplete({source: affiliateCategories, minChars:0, minLength:0, mustMatch:false});		
		
		$("#affForm").submit(function(){
			validateCategory();
		});
			
		$('#templates').change(function(){
			template = $(this).val();
		});
		
		$('input[name=active_pixel]').change(function(){
			if( $('#ActivePixel:checked').val() == 1){
				$('#pixel_panel').show();
			}else{
				$('#pixel_panel').hide();
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
				$('#tabs').hide();
			}
		});
		//this jquery is for adding/removing pixel entry fields
		var counter =2;

		$('#add_pixel').click(function(){
			var newPixelDiv = $(document.createElement('div')).attr("id", "pixel_"+counter);
			newPixelDiv.html("<label> Pixel #" +counter + "</label> <br> Enable:"+
				'<?=$this->form->checkbox("pixel['+(counter-1)+'][enable]", array("value"=>"1", "checked"=>"checked")); ?> <br> Select:'+
				'<?=$this->form->select("pixel['+(counter-1)+'][page]", $sitePages, array("multiple"=>"multiple", "size"=>5)); ?><br> Pixel<br>'+
				'<?=$this->form->textarea("pixel['+(counter-1)+'][pixel]", array("rows"=>"5")); ?>'
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

	//multi select transfer transfer
	$(document).ready(function(){
	
		$('#add_code').click(function(){
			var value= $('#Code').val();
			if(value){
				$('#InvitationCodes').append("<option value="+value+">"+value+"</option>");
				$('#Code').attr('value', "");
			}
		});
		$('#edit_code').click(function(){
			var value=$('#InvitationCodes option:selected').val();
			$('#InvitationCodes option:selected').remove();
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
	$("#upload").click(function(){
		if($('#upload_panel').css('display') == 'none'){
			$('#upload_panel').show();
		}else{
			$('#upload_panel').hide();
		}
	});
	
	$("#background_select").click(function(){
		if($('#background_selection').css('display') == 'none'){
			loadBackgrounds();
			$('#background_selection').show();
		}else{
			$('#background_selection').hide();
		}
	});
	
</script>
<script type="text/javascript">

function validateCategory(){
    if($("#affiliate_category").val().length > 1 && $("#affiliate_category").val().indexOf(" ") > -1){
    	alert("The category field cannot contain spaces");
    	return false;
    }
} 

	//background
function backgroundChange(image){
	$("input:hidden[name='background_img']").val(image);
	$('#mb-bg').css('background-image', 'url(/image/' + image + '.png)');
}

function featureChange(){
	id = $(this).attr('id');
}
//Edit in place
$(function(){
    $('.editable').editable({
        onEdit:begin,
        onSubmit:submit,
        type:'textarea'
    });
    function begin(){
        this.append('Click anywhere to submit');
    }
    function submit(){
    	var id = $(this).attr('id');
    	var html = $('#' + id).html();
    	if (html == "") {
    		html = "empty";
    	}
        $("input:hidden[name='" + id +"']").val(html);
    }
});

function timer(){
 window.setTimeout("loadBackgrounds();", 10000);
}
</script>