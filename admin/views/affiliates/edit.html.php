<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery-dynamic-form.js');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->script('jquery.editable-1.3.3.js');?>
<?=$this->html->script('swfupload.js');?>
<?=$this->html->style('swfupload')?>
<?=$this->html->script('swfupload.queue.js');?>
<?=$this->html->script('fileprogress.js');?>
<?=$this->html->script('handlers.js');?>
<?=$this->html->script('swfupload.queue.js');?>


<script type="text/javascript">
	//this is for keeping ALL affiliate categories
	var allAffiliateCategories = <?=json_encode($affiliateCategories)?>;
	
	//the mongo id for this affiliate -  a string
	var affiliateId = "<?=$affiliate['_id']?>";
	
	//keep these for use in adding affiliate categories
	//useful for indexing category tag names and images
	var affiliateCategories = <?=json_encode($affiliate['category'])?>;
</script>

<?php 
	$i = 0;
?>

<div class="grid_16">
	<h2 id="page-heading">Affiliate Edit Panel</h2>
</div>
<div class="clear"></div>
<div class="grid_3 menu">
	<table>
		<thead>
			<tr>
				<th>Affiliate Navigation </th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td> <?php echo $this->html->link('Create Affiliate', 'affiliates/add'); ?> </td>
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
		<?=$this->form->create(null, array('id' => 'mainForm', 'enctype'=>'multipart/form-data')); ?>
			<?php $checked= (($affiliate['active']))? 'checked':'' ?>
			Activate: <?=$this->form->checkbox('active', array('checked'=>$checked)); ?> <br>
			<?php
				$option ='';
				foreach( $packages as $key){
					if( array_key_exists('level', $affiliate) && $key == $affiliate['level'] ) {
						$option .= "<option value= $key selected='selected'> $key</option>";
					} else {
						$option .= "<option value= $key> $key</option>";
					}
				}
			?>
			Affiliate Level: <select name="level" id="Level"> <?php echo $option; ?> </select> <br><br>
		Affiliate Name:
			<?=$this->form->text('affiliate_name', array('value' => $affiliate['name'])); ?> <br><br>		
			Affiliate Category: 
		<?=$this->form->text('affiliate_category', array('value' => '', 'autocomplete'=>'off', 'id'=>'affiliate_category')); ?>
		<input type="button" name="add_category" id="add_category" value="Add Category"/>
			<br><br>
			
			<div id="categories">
				<?php foreach($affiliate['category'] as $affCat) { ?>
				<div id="<?=$i."_".$affiliate['_id']?>">
					<a href="#" id="<?=$i."_".$affCat['name']?>" class="upload_img">+ <?=$affCat['name'];?></a>
<!-- form field for categyr name goes here -->
<input type="hidden" id="<?=$i."_".$affiliate['_id']?>_category_name" name="<?=$i."_".$affiliate['_id']?>_category_name" value="<?=$affCat['name']?>">
					
<!-- upload file for this category here -->
				<input type="hidden" id="<?=$i."_".$affiliate['_id']?>_category_background" name="<?=$i."_".$affiliate['_id']?>_category_background" value="<?=$affCat['background_image']?>">
				</div>
				<?php 
					$i++;
				} ?>
			</div>
			
			<br><br>
			Enter Code:
			<?=$this->form->text('code'); ?>  <input type="button" name="add_code" id="add_code" value="add"/>
			<br>
			Affiliate codes:<br>
			<?php
				$codes= array();
				foreach($affiliate['invitation_codes'] as $code) {
					$codes[$code]= $code;
				}
			?>
			<?=$this->form->select('invitation_codes',$codes,array('multiple'=>'multiple', 'size'=>5)); ?> <br>
			<input type="button" name="edit_code" id="edit_code" value="edit code"/>
			<br><br>
			<div id="upload_block" style="display:none">
				<div id="upload_panel" class="upload">
					<br>
					Category background image:<br>
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
			</div>
		</div>
<!--end of box-->

<div class ="grid_7 box">
	<div class="block forms">
			<div id ="tabs">
				<ul>
					<li id="pixel_tab"><a href="#pixel"><span>Pixels</span></a></li>
					<li id="current_tab"><a href="#current_pages"><span>Current Pages</span></a></li>
					<li id="landing_tab"><a href="#landing_page"><span>Landing Pages</span></a></li>
				</ul>
				<div id="pixel">
					<?php $checked = (($affiliate['active_pixel']))? 'checked':'' ?>
					<div id='pixel_activate'>
						Affiliate uses pixels:
						<?=$this->form->checkbox('active_pixel', array('value' => '1', 'checked' => $checked)); ?>
					</div>
					<br>
					<br>
					<div id="pixel_panel"><!--start pixel panel-->
						<h5>Add Pixels</h5>
						<input type="button" name="add_pixel" value="add pixel" id="add_pixel"/>
						<input type="button" name="remove_pixel" value="remove pixel" id="remove_pixel"/>
						<br>
						<br>
						<?php
							$count=0;
							$size= (array_key_exists('pixel', $affiliate)) ? count($affiliate['pixel']) : 0;

							if( $size > 0):
							foreach($affiliate['pixel'] as $pixel):
							$checked = (($pixel['enable'])) ? 'checked' : '';
							$pix = $pixel['pixel'];
							$option='';
							foreach($sitePages as $key => $name){
								if( array_key_exists('page', $pixel) && $pixel['page'] && in_array($key , $pixel['page']) ){
									$option .= "<option value=$key selected='selected'> $name </option>";
								}else{
									$option .= "<option value= $key> $name </option>";
								}
							}
						?>
						<div id='<?php echo 'pixel_'.($count+1)?>'>
							<label> Pixel # <?=$count+1; ?> </label><br>
							Enable:
							<?=$this->form->checkbox('pixel['.$count.'][enable]', array('value'=>'1', 'checked'=> $checked)); ?> <br>
							Select Page(s):<br>
							<select name="<?php echo 'pixel['.$count.'][page][]'; ?>" multiple='multiple' size='5'>
								<?php echo $option; ?>
							</select>
							<br>
							Pixel:<br>
							<?=$this->form->textarea('pixel['.$count.'][pixel]', array('value' => $pix, 'rows'=>'10', 'cols'=>'50')); ?>
							<br>
						</div>
						<?php
							$count++;
							endforeach;
							endif;
						?>
						<input type="hidden" id="pixel_count" name="pixel_count" value="<?php echo $count; ?>" />
						<br>
					</div> <!--end pixel panel-->
				</div><!--end pixel-->
				<div id="current_pages"> <!--end current page-->
					<div id="current_panel"><!--end current panel-->
						<br/>
						<div id="template_form">
							<?php
								if ($affiliate['category']): ?>
									<table id="currentPage">
										<th> Name </th>
										<th> URL </th>
										<?php foreach ($affiliate['category'] as $value):?>
											<tr>
												<td>
													<?=$value['name'];?>
												</td>
												<td>
													<div id="<?=$affiliate['_id'];?>" class="selector" style="text-decoration:underline; cursor:pointer">
													<a href="/<?=$value['name'];?>?a=<?=$affiliate['name'];?>">/<?=$value['name'];?>?a=<?=$affiliate['name'];?>
													</a>	
													</div>	
												</td>
											</tr>
										<?php endforeach; ?>
									</table>
							<?php else:
									echo "Affiliate currently has no landing pages";
								endif;
							?>
						</div>
					</div><!--end current page panel-->
			</div><!--end current page-->
			<?php
				if ($affiliate['active_landing']){
					$checked = 'checked';
				} else {
					$checked = '';
				}
			?>
			<div id="landing_page"><!--start landing_page-->
					<div id="landing_activate"> Affiliate uses landing pages:
						<?=$this->form->checkbox('active_landing', array('value'=>'1', 'checked' => $checked)); ?>
					</div>
					<div id="landing_panel"><!--start landing_panel-->
						<br/>
						<div id="template_panel"><!--start template_panel-->
								<?=$this->form->hidden('index'); ?>
								<label>Enable </label>
								<?=$this->form->checkbox('landing_enable', array('value'=>'1', 'checked' => "checked")); ?><br/>
								<label>Choose Template Type </label>
								<?=$this->form->select('template_type', array(
												'temp_1' => 'Template One'
									));
								?>
								<label>Name:</label>
								<?=$this->form->text('name'); ?>
								<label>Specified Url:</label>
								<?=$this->form->text('url'); ?>
								<br/>
								<br/>
								<!--background selection-->
								<a id="background_select" style="cursor:pointer">Click here to select a background </a> <br/>
								<div id="background_selection">
								</div>
								<!-- Upload Section -->
								<a id="upload" style="cursor:pointer">Click here to add backgrounds, feature images or logos </a>
								<!--
								<div id="upload_panel">
									<h5 id="uploaded_media">Uploaded Media</h5>
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
														<span id="spanButtonPlaceholder1" onclick="isBackground(upload1);"></span>
														<input id="btnCancel1" type="button" value="Cancel Uploads" onclick="cancelQueue(upload1);" disabled="disabled" style="margin-left: 2px; height: 22px; font-size: 8pt;" />
														<input id="isbackground" name="isbackground" type="checkbox" value='1' onclick="isBackground(upload1);" /> Is Background
														<input id="isfeature" value='1' name="isfeature" type="checkbox" onclick="isFeature(upload1);" /> Is Feature On
														<input id="islogo" value='1' name="islogo" type="checkbox" onclick="isLogo(upload1);" /> Is Logo
														<br />
													</div>
												</div>
											</td>
										</tr>
									</table>
								</div> -->
								<div id="template">
									<?php echo $this->view()->render(array('element' => 'temp_1')); ?>
								</div>
								<a href='#' id ='savePage'>Save Page</a>
						</div><!--end of template panel-->
					</div> <!--end of landing panel-->
				</div><!--end of landing page-->
		</div><!--end tabs-->
	</div>
</div>
			<br/>
			<br/>
			<br/>
		<div id="submit button" class="grid_16">
			<div class="grid_7" >
			<?=$this->form->submit('Edit', array('id'=>'edit')); ?>
		</div>
	</div>
	<?=$this->form->end(); ?>
</div>
<script type="text/javascript">

$(document).ready(function() {

	$("#affiliate_category").autocomplete({source: allAffiliateCategories, minChars:0, minLength:0, mustMatch:false});	

	$('#background_selection').hide();
	//$('#upload_panel').hide();
	$('#landing_tab').hide();
	//create tabs
	$("#tabs").tabs();
});
</script>
<script type="text/javascript">
	$().ready(function(){
		if($('#ActivePixel').is(':checked')){
			$('#pixel_panel').show();
		} else {
			$('#pixel_panel').hide();
		}

		if($('#ActiveLanding').is(':checked')){
			//$('#landing_panel').show();
		} else {
			//$('#landing_panel').hide();
		}
	});

	/*
	$(document).ready(function(){
		$('input[name=active_pixel]').change(function(){
			if( $('#ActivePixel:checked').val() == 1){
				$('#pixel_panel').show();
			}else{
				$('#pixel_panel').hide();
			}
		});

		$('input[name=active_landing]').change(function(){
			if( $('#ActiveLanding:checked').val() == 1){
				$('#landing_panel').show();
			}else{
				$('#landing_panel').hide();
			}
		});
	});*/
	
	$(document).ready( function() {
		var counter = Number($('#pixel_count').val()) + 1;
		
		$("#add_category").click( function() {
			//check if the category name has already been added		
			if(1==2) {
				alert("This category name is already added - try a different category name");
				return false;
			} else {
				//add it to the affiliate categories array			
				affiliateCategories.push({name: $("#affiliate_category").val(), backgroundImage:""});
				
				//get the new length of this array
				var len = affiliateCategories.length - 1;
				var formHTML = "";
							
				formHTML = "<div id='" + len + "_" + affiliateId + "'>";		
				formHTML += "<a id='" + len + "_" + $("#affiliate_category").val() + "' href='#' class='upload_img'>+ " + $("#affiliate_category").val() + "</a>";
				
				// append a hidden field with this naming convention for id's
				//1 - last index of aff cat
				//2 - the affiliate id
				//3 - the name of the field: category name
				formHTML += "<input type='hidden' name='" + len + "_" + affiliateId + "_category_name' id='" + len + "_" + affiliateId + "_category_name' value='" +  $("#affiliate_category").val() + "'>";
				formHTML += "</div>";
				$("#categories").append(formHTML);
			}
		});
		
		$(".upload_img").live('click',  function() {		
			var affCat = this.id;
			
			var catIndex = 0;
			var catImgId = "";
			
			//catImgId = catIndex + "_" + affiliateId + "_category_background";
			catIndex = affCat.substring(0, 1);
			// 1 - get the first number in the id field of the anchor tag 
			//waiting for image upload to work, then we can deal with how the returned image path will be dealt with
						
			if($("#" + catIndex + "_" + affiliateId + " .upload").length==0) {	
				var imgHTML = "";
			
				imgHTML += "<input type='hidden' name='" + affCat + "_category_background' id='" + affCat + "_category_background' class='upload'>";
				imgHTML += $("#upload_block").html();
				
				$("#" + catIndex + "_" + affiliateId).append(imgHTML);
			} else {
				$("#" + catIndex + "_" + affiliateId + " .upload").remove();
			}
		});	
		
		$('#add_pixel').click(function() {
			var newPixelDiv = $(document.createElement("div")).attr("id", "pixel_"+counter);

			newPixelDiv.html(unescape("<label> Pixel #" +counter + "</label> <br> Enable:"+
				'<?=$this->form->checkbox("pixel['+(counter-1)+'][enable]", array("value"=>"1", "checked"=>"checked")); ?> <br> Select:'+
				'<?=$this->form->select("pixel['+(counter-1)+'][page]", $sitePages, array("multiple"=>"multiple", "size"=>5)); ?><br> Pixel<br>'+
				'<?=$this->form->textarea("pixel['+(counter-1)+'][pixel]", array("rows"=>"5")); ?>'
				));
			newPixelDiv.appendTo('#pixel_panel');
			counter++;
		});

		$('#remove_pixel').click(function(){
			counter--;
			if(counter==1){
				alert('No more textbox to remove');
				return false;
			}

			$('#pixel_'+counter).remove();
		});
	});

	//multi select transfer transfer
	$().ready(function(){
		$('#add_code').click(function(){
			var value= $('#Code').val();
			if(value){
				$('#InvitationCodes').append("<option value=" + value + ">"+value+"</option>");
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
	$().ready(function(){
		$('#edit').click(function(){
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

	//select all codes upon submit
	$().ready(function(){
		$('#edit').click(function(){
			$('#InvitationCodes').each(function(){
				$('#InvitationCodes option').attr('selected','selected');
			});
		});
	});

	//Level Selector
	$(document).ready(function(){
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
		//selector for which template to veiw
		var select = $('.selector').click(function(){
			if ($(this).attr('id') == 'addPage') {
				$('#landing_tab a').html('Landing Page');
				$('#landing_tab').show();
				$("input:hidden[name='index']").val("new");
				$("#LandingEnable").attr('checked', 'checked');
				$("#Name").attr("value","");
				$("#Url").attr("value", "");
				$("#headline_1").html("Exclusive access to great deals for tots, moms & families!");
				$("input:hidden[name='headline_1']").val("Exclusive access to great deals for tots, moms & families!");
				$("#headline_2").html("Why Do Moms Love Totsy?");
				$("input:hidden[name='headline_2']").val("Why Do Moms Love Totsy?");
				$("#bullet_1").html("FREE Membership to private sales");
				$("input:hidden[name='bullet_1']").val("FREE Membership to private sales");
				$("#bullet_2").html("SAVE on apparel, toys, shoes, furniture & more");
				$("input:hidden[name='bullet_2']").val("SAVE on apparel, toys, shoes, furniture & more");
				$("#bullet_3").html("EARN Totsy credits to shop for free");
				$("input:hidden[name='bullet_3']").val("EARN Totsy credits to shop for free");
				$("#bullet_4").html("GREEN initiative plants one tree per purchase");
				$("input:hidden[name='bullet_4']").val("GREEN initiative plants one tree per purchase");
			}else{
				var name = $(this).attr('id');
				$('#landing_tab a').html(name + ' Page');
				$('#landing_tab').show();
				$.post('/affiliates/retrieveLanding',{affiliate:"<?php echo $affiliate['_id']?>",name:name}, function(data){
					// alert(data);
					var json = $.parseJSON(data);
					$("input:hidden[name='index']").val(json['key']);
					if (json['enabled']) {
						$("#LandingEnable").attr('checked', 'checked');
					}
					$("#TemplateType").attr('selected', json['template']);
					backgroundChange(json['background_img']);
					$("#Name").attr("value",json['name']);
					$("#Url").attr("value", json['url']);
					var index = 0;
					for (field in json['headline']) {
						$("#headline_" + (index + 1)).html(json['headline'][index]);
						$("input:hidden[name='headline_" + (index + 1) + "']").val(json['headline'][index]);
						index = index + 1;
					}
					var index = 0;
					for (field in json['bullet']) {
						$("#bullet_" + (index + 1)).html(json['bullet'][index]);
						$("input:hidden[name='bullet_" + (index + 1) +"']").val(json['bullet'][index]);
						index = index + 1;
					}
				});
			}
		$('#landing_tab a').click();
		});
	});
</script>
<script type="text/javascript">
$('#savePage').click (function(){
	dataString = $('#mainForm').serialize() + '&aid=' + "<?php echo $affiliate['_id']?>";
	//alert(dataString);
	$.post('/affiliates/saveLanding', dataString, function(data) {
		if(data){
			tr = $('<tr>').html('<td><div id="'+ $("#Name").val() +'"style="text-decoration:underline;cursor:pointer">'+$("#Name").val()+'</div></td>'
				+ '<td>' + $('#Url').val() +'</td>');
			tr.insertAfter('#currentPage tbody>tr:last');
			$('#' + $('#Name').val()).attr('class','selector');
		}
	});
});
//background
function loadBackgrounds(){
	var background = $('#background');
    $.post('/affiliates/background', function(data) {
	 	var col_limit = 3;
		var col = 0;
		var json = $.parseJSON(data);
		var table = "";
		var html = "<table>";
	   // alert(json);
		for(i=0;i < json.length; ++i){
			if(col == 0){
				table += "<tr>";
			}
			table += '<td onclick="backgroundChange(\''+ json[i] + '\')"><a><img src="/image/' + json[i] + '.png" width="150"/></a></td>';
			if(col == col_limit){
				table += "</tr>";
			}
			if(col < col_limit ){
				++col;
			}else{
				col = 0;
			}
		}
		html += table + "</table>";
        $("#background_selection").html(html);
    });
    timer();
}
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