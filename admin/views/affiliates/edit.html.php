<?php echo $this->html->script('tiny_mce/tiny_mce.js');?>
<?php echo $this->html->script('jquery-1.4.2');?>
<?php echo $this->html->script('jquery-dynamic-form.js');?>
<?php echo $this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?php echo $this->html->script('swfupload.js');?>
<?php echo $this->html->script('swfupload.queue.js');?>
<?php echo $this->html->script('fileprogress.js');?>
<?php echo $this->html->script('handlers.js');?>
<?php echo $this->html->script('jquery.editable-1.3.3.js');?>
<?php echo $this->html->script('affiliate_upload.js');?>
<?php echo $this->html->style('swfupload')?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>

<script type="text/javascript">
	//this is for keeping ALL affiliate categories
	var allAffiliateCategories = <?php echo json_encode($affiliateCategories)?>;
	
	//the mongo id for this affiliate -  a string
	var affiliateId = "<?php echo $affiliate['_id']?>";
	
	//keep these for use in adding affiliate categories
	//useful for indexing category tag names and images
	var temp = <?php echo json_encode($affiliate['category'])?>;
	var affiliateCategories = new Array();
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
				<td> <?php echo $this->html->link('Create Affiliate', 'affiliates/add');?> </td>
			</tr>
			<tr>
				<td><?php echo $this->html->link('View/Edit Affiliate', 'affiliates/index');?></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="clear"></div>
<div class="grid_8 box">
	<div class="block forms">
		<?php echo $this->form->create(null, array('id' => 'mainForm', 'enctype'=>'multipart/form-data'));?>
			<?php $checked= (($affiliate['active']))? 'checked':'' ?>
			Activate: <?php echo $this->form->checkbox('active', array('checked'=>$checked)); ?> <br>
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
			<?php echo $this->form->text('affiliate_name', array('value' => $affiliate['name'])); ?> <br><br>		
			Affiliate Category: 
		<?php echo $this->form->text('affiliate_category', array('value' => '', 'autocomplete'=>'off', 'id'=>'affiliate_category')); ?>
		<input type="button" name="add_category" id="add_category" value="Add Category"/>
			<br><br>
			<div id="categories">
				<?php foreach($affiliate['category'] as $affCat) { ?>
				<div id="<?php echo $i."_".$affiliate['_id']?>">
					<a href="#" id="<?php echo $i."_".$affCat['name']?>" class="upload_img" style="width:200px">+ <?php echo $affCat['name'];?></a>
					<a href="#" class="remove_category" style="float:right; width:10px">X</a>
					<span style="float:right; width:150px">Select for upload <input type="radio" name="selected_image" value="<?php echo $i?>"></span>
<!-- form field for categyr name goes here -->
<input type="hidden" id="<?php echo $i."_".$affiliate['_id']?>_category_name" name="<?php echo $i."_".$affiliate['_id']?>_category_name" value="<?php echo $affCat['name']?>">
					
<!-- upload file for this category here -->
				<input type="hidden" id="<?php echo $i."_".$affiliate['_id']?>_category_background" name="<?php echo $i."_".$affiliate['_id']?>_category_background" value="<?php echo $affCat['background_image']?>">
				</div>
				<?php 
					$i++;
				} ?>
			</div>
			<div id="upload_block">
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
				        						    				<span id="spanButtonPlaceholder1"></span>
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
			<br><br>
			Enter Code:
			<?php echo $this->form->text('code'); ?>  <input type="button" name="add_code" id="add_code" value="add"/>
			<br>
			Affiliate codes:<br>
			<?php
				$codes= array();
				foreach($affiliate['invitation_codes'] as $code) {
					$codes[$code]= $code;
				}
			?>
			<?php echo $this->form->select('invitation_codes',$codes,array('multiple'=>'multiple', 'size'=>5)); ?> <br>
			<input type="button" name="edit_code" id="edit_code" value="edit code"/>
			<br><br>
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
						<?php echo $this->form->checkbox('active_pixel', array('value' => '1', 'checked' => $checked)); ?>
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
							<label> Pixel # <?php echo $count+1; ?> </label><br>
							Enable:
							<?php echo $this->form->checkbox('pixel['.$count.'][enable]', array('value'=>'1', 'checked'=> $checked)); ?> <br>
							Select Page(s):<br>
							<select name="<?php echo 'pixel['.$count.'][page][]'; ?>" multiple='multiple' size='5'>
								<?php echo $option; ?>
							</select>
							<br>
							Pixel:<br>
							<?php echo $this->form->textarea('pixel['.$count.'][pixel]', array('value' => $pix, 'rows'=>'10', 'cols'=>'50')); ?>
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
													<?php echo $value['name'];?>
												</td>
												<td>
													<div id="<?php echo $affiliate['_id'];?>" class="selector" style="text-decoration:underline; cursor:pointer">
													<a href="/<?php echo $value['name'];?>?a=<?php echo $affiliate['name'];?>">/<?php echo $value['name'];?>?a=<?php echo $affiliate['name'];?>
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
			
		</div><!--end tabs-->
	</div>
</div>
			<br/>
			<br/>
			<br/>
		<div id="submit button" class="grid_16">
			<div class="grid_7" >
			<?php echo $this->form->submit('Update', array('id'=>'edit')); ?>
		</div>
	</div>
	<?php echo $this->form->end(); ?>
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
	});
	
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
	});
	
	$(document).ready( function() {
		var counter = Number($('#pixel_count').val()) + 1;
		
		//get count of all categories in the object
		var categoryCount = 0;
		
		for(i in temp) {
			categoryCount++;
			affiliateCategories.push({name: temp[i].name, background_image: temp[i].background_image});	
		}
 		
 		function validateNames(t) {
 			var regexp = /^[a-zA-Z0-9-_]+$/;

			if (t.search(regexp) == -1 || t==""){ 
				return false;
			} else { 
				return true; 
			}
 		}
 		
 		$("#mainForm").submit( function() {
 			if(validateNames($("#AffiliateName").val())==false) {
 				alert("The affiliate name can only contain letters and/or underscores.");
 				return false;
 			}
 		});
		
		$("#add_category").click( function() {
			//check if the category name has already been added	
			var catExists = false;
			
			for(i in affiliateCategories) {
				if (affiliateCategories[i].name==$("#affiliate_category").val()){
					catExists = true;
					break;
				}
			}
						
			if(catExists==true) {
				alert("This category name is already added - try a different category name");
				return false;
			} else if(validateNames($("#affiliate_category").val())==false) {
				alert("The category name can only contain letters and/or underscores. no spaces, ampersands or other URL incompatible characters");				
				return false; 
			} else {
				//add it to the affiliate categories array			
				//affiliateCategories({name: $("#affiliate_category").val(), backgroundImage:""});
				
				affiliateCategories.push({name: $("#affiliate_category").val(), backgroundImage:""});							
				//get the new length of this array
				var len = affiliateCategories.length - 1;
				var formHTML = "";
							
				formHTML = "<div id='" + len + "_" + affiliateId + "'>";		
				formHTML += "<a id='" + len + "_" + $("#affiliate_category").val() + "' href='#' class='upload_img'>+ " + $("#affiliate_category").val() + "</a>";
				
				formHTML += "<a href='#' class='remove_category' style='float:right;width:10px'>X</a>";
				
				formHTML += "<span style='float:right; width:150px'>Select for upload <input type='radio' name='selected_image' value='" + len +"'></span>";				
				// append a hidden field with this naming convention for id's
				//1 - last index of aff cat
				//2 - the affiliate id
				//3 - the name of the field: category name
				formHTML += "<input type='hidden' name='" + len + "_" + affiliateId + "_category_name' id='" + len + "_" + affiliateId + "_category_name' value='" +  $("#affiliate_category").val() + "'>";
				formHTML += "</div>";
				$("#categories").append(formHTML);
			}
		});
		
		$(".remove_category").live('click', function(){
			var categoryId = $(this).parent()[0].id;
			var catIndex = 0;
			
			catIndex = categoryId.substring(0, 1);	
					
			//remove it from the affiliate categories object
			delete affiliateCategories[catIndex];
			//remove HTML portion
			$("#" + categoryId).remove();
		});
		
		/*
		$(".upload_img").live('click', function() {		
			var affCat = this.id;
			
			var catIndex = 0;
			var catImgId = "";
			
			//catImgId = catIndex + "_" + affiliateId + "_category_background";
			catIndex = affCat.substring(0, 1);
			// 1 - get the first number in the id field of the anchor tag 
			//waiting for image upload to work, then we can deal with how the returned image path will be dealt with
						
				var imgHTML = "";
			
				imgHTML += "<input type='hidden' name='" + affCat + "_category_background' id='" + affCat + "_category_background' class='upload'>";				
				//imgHTML += $("#upload_block").html();
				//$("#upload_block").show();				
				
				//$("#" + catIndex + "_" + affiliateId).append(imgHTML);
				//$("#" + catIndex + "_" + affiliateId + " .upload").remove();
		});*/
		
		$('#add_pixel').click(function() {
			var newPixelDiv = $(document.createElement("div")).attr("id", "pixel_"+counter);

			newPixelDiv.html(unescape("<label> Pixel #" +counter + "</label> <br> Enable:"+
				'<?php echo $this->form->checkbox("pixel['+(counter-1)+'][enable]", array("value"=>"1", "checked"=>"checked")); ?> <br> Select:'+
				'<?php echo $this->form->select("pixel['+(counter-1)+'][page]", $sitePages, array("multiple"=>"multiple", "size"=>5)); ?><br> Pixel<br>'+
				'<?php echo $this->form->textarea("pixel['+(counter-1)+'][pixel]", array("rows"=>"5")); ?>'
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
	});
</script>