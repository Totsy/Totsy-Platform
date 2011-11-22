<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<?=$this->html->script('jquery-1.4.2');?>
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
	//this is for keeping ALL affiliate categories
	var allAffiliateCategories = <?=json_encode($affiliateCategories)?>;
	
	//the mongo id for this affiliate -  a string
	var affiliateId = "<?=$affiliate['_id']?>";
	
	//keep these for use in adding affiliate categories
	//useful for indexing category tag names and images
	var temp = <?=json_encode($affiliate['category'])?>;
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
<div class="grid_6 box">
	<form id = "AffiliateId">
		<input type="hidden" name="affiliate_id" value="<?=(string)$affiliate->_id?>">
	</form>
	<div class="block forms">
		<?=$this->form->create(null, array('id' => 'mainForm', 'enctype'=>'multipart/form-data'));?>
			<?php $checked= (($affiliate->active))? 'checked':'' ?>
			Activate: <?=$this->form->checkbox('active', array('checked'=>$checked)); ?> <br>
			<?php
				$option ='';
				foreach( $packages as $key){
					if( $key == $affiliate->level ) {
						$option .= "<option value= $key selected='selected'> $key</option>";
					} else {
						$option .= "<option value= $key> $key</option>";
					}
				}
			?>
			Affiliate Level: <select name="level" id="Level"> <?php echo $option; ?> </select> <br><br>
		Affiliate Name:
			<?=$this->form->text('affiliate_name', array('value' => $affiliate->name)); ?> 
		
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
		</div>
</div>
<!--end of box-->

<div class ="grid_9 box">
	<div class="block forms">
			<div id ="tabs">
				<ul>
					<li id="pixel_tab"><a href="#pixel"><span>Pixels</span></a></li>
					<li id="current_tab"><a href="#current_pages"><span>Current Pages</span></a></li>
					<li id="landing_tab"><a href="#landing_page"><span>Dynamic Landing Pages</span></a></li>
					<li id="pending_tab"><a href="#pending_page"><span>Pending Backgrounds</span></a></li>
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
				<div id="current_pages"> <!--start current page-->
					<div id="current_panel"><!--start current panel-->
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
				<div id="landing_page"><!--start landng page-->
					<div id="landing_activate">
						<?php $checked = (($affiliate->active_landing))? 'checked':''; ?>
			        Affiliate uses dynamic landing Pages:
			         <?=$this->form->checkbox('active_landing', array('value'=>'1', 'checked'=>$checked)); ?>
			    </div>
			    <p>
					<strong> Upload backgroud images for landing pages.</strong> 
			    </p>
			    <div id="landing_panel">
					<!--Current Background Images-->
					<h3 id="current_images">Current Images</h3>
					<strong>If you have add a url make sure the http:// is in the url.</strong>
					<hr />
						<table border="1" cellspacing="30" cellpadding="30">
						<tr>
							<th align="justify">
								Image
							</th>
							<th align="justify">
								Category
							</th>
							<th align="justify">
								Code
							</th>
							<th align="justify">
								Remove
							</th>
						</tr>
							<?php foreach($affiliate->category as $image):?>
								<tr>
									<td align="center">
										<?php
												$catImage = "/image/{$image['background_image']}.jpg";
										?>
										<?=$this->html->image("$catImage", array('width' => 100, 'alt' => 'altText')); ?>
									</td>

									<td align="center">
										<?php
												$category = $image['name'];
												$id = $image['background_image'];
										?>
										<?=$this->form->text("affiliate_category[$id]", array('value' => $category, 'autocomplete'=>'on', 'class' => 'affiliate_category', 'id'=>"category_" . $id)); ?>
									</td>
									<td align="center">
										<?php
											$code = $image['code'];
											$options = array_combine($affiliate->invitation_codes->data(),$affiliate->invitation_codes->data());
											$selection = array_merge($options,array('all' => 'all'));
										?>
										<?=$this->form->select("apply_code[$id]", $selection,array('class' => "relevantCodes", "value" => $code));?>
									</td>
									 <td align="center">
										<input type="hidden" name="img[]" value="<?php echo $id; ?>"/>
									</td>
								</tr>
							<?php endforeach;?>
						</table>
				<!--End of Current Background Image-->
					<div id="agile_file_upload"></div>
					<script type="text/javascript">
						$('#agile_file_upload').agileUploader({
							flashSrc: "<?=$this->url('/swf/agile-uploader.swf'); ?>",
							formId: 'AffiliateId',
							flashWidth: 70,
							removeIcon: "<?=$this->url('/img/agile_uploader/trash-icon.png'); ?>",
							flashVars: {
								submitRedirect: '<?=$this->url("/affiliates/edit/{$affiliate->_id}"); ?>',
								button_up: "<?=$this->url('/img/agile_uploader/add-file.png?v=1'); ?>",
								button_down: "<?=$this->url('/img/agile_uploader/add-file.png'); ?>",
								button_over: "<?=$this->url('/img/agile_uploader/add-file.png'); ?>",
								form_action: "<?=$this->url('/files/upload/all'); ?>",
								file_limit: 30,
								max_height: '1000',
								max_width: '1000',
								file_filter: '*.jpg;*.jpeg;*.gif;*.png;*.JPhttp://www.webdav.org/specs/rfc2518.htmlG;*.JPEG;*.GIF;*.PNG',
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
					</div><!--end of landing panel-->
				</div><!--end landing page-->
				<div id="pending_page"><!--start Pending backgrounds-->
					<?=$this->view()->render(array('element' => 'files_pending'), array('item' => $affiliate,'search_type' => 'affiliate')); ?>
				</div><!--end Pending backgrounds-->
		</div><!--end tabs-->
	</div>
</div>
			<br/>
			<br/>
			<br/>
		<div id="submit button" class="grid_16">
			<div class="grid_7" >
			<?=$this->form->submit('Update', array('id'=>'edit')); ?>
		</div>
	</div>
	<?=$this->form->end(); ?>
</div>
<script type="text/javascript">

$(document).ready(function() {

	$(".affiliate_category").autocomplete({source: allAffiliateCategories, minChars:0, minLength:0, mustMatch:false});	

	$('#background_selection').hide();
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
			affiliateCategories.push({name: temp[i].name});	
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
		
		$(".affiliate_category").blur( function() {
			//check if the category name has already been added	
			var catExists = false;
			var active_id = $(this).attr('id');
			var new_category = $("#"+active_id).val();
			var current_index = $('.affiliate_category').index($("#"+active_id));
			if (current_index == -1) {
				size = $('.affiliate_category').length;
				current_index = size - 1;
			}
			var used_categories = $('.affiliate_category');
			for(index in used_categories) {
				if (current_index != index && used_categories[index].value == new_category){
					catExists = true;
					break;
				}
			}
						
			if(catExists==true) {
				alert("This category name is already added - try a different category name");
				return false;
			} else if(validateNames(new_category)==false) {
				alert("The category name can only contain letters and/or underscores. no spaces, ampersands or other URL incompatible characters");				
				return false; 
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
				$('.relevantCodes').append("<option value="+value+">"+value+"</option>");
				$('#Code').attr('value', "");
			}
		});
		$('#edit_code').click(function(){
			var value=$('#InvitationCodes option:selected').val();
			$('#InvitationCodes option:selected').remove();
			$('.relevantCodes option:selected').remove();
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
