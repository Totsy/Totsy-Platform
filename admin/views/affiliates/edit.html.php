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
 <form id = "AffiliateId">
    <input type="hidden" name="affiliate_id" value="<?=(string)$affiliate->_id?>"/>
</form>
<?=$this->form->create(null, array('id' => 'affForm'));?>
    <div id="submit button" class="grid_16">
         <div class="grid_2" >
            <?=$this->form->submit('Update', array('id'=>'edit')); ?>
        </div>
    </div>
    <div class="grid_7 box">
        <div class="block forms">
            <?php $checked= (($affiliate->active))? 'checked':'' ?>
            Activate: <?=$this->form->checkbox('active', array('checked'=>$checked)); ?> <br/>
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
            Affiliate Level: <select name="level" id="Level"> <?php echo $option; ?> </select> <br/><br/>
        Affiliate Name:
            <?=$this->form->text('affiliate_name', array('value' => $affiliate->name)); ?>

            <br/><br/>
            Enter Code:
            <?=$this->form->text('code'); ?>  <input type="button" name="add_code" id="add_code" value="add"/>
            <br/>
            Affiliate codes:<br/>
            <?php
                $codes= array();
                foreach($affiliate['invitation_codes'] as $code) {
                    $codes[$code]= $code;
                }
            ?>
            <?=$this->form->select('invitation_codes',$codes,array('multiple'=>'multiple', 'size'=>5)); ?> <br/>
            <input type="button" name="edit_code" id="edit_code" value="edit code"/>
            <br/><br/>
        </div><!--end of block-->
    </div><!--end of box-->

    <div class ="grid_8 box">
            <div class="block forms">
                    <div id ="tabs">
                        <ul>
                            <li id="pixel_tab"><a href="#pixel"><span>Pixels</span></a></li>
                            <li id="current_tab"><a href="#current_pages"><span>Current Pages</span></a></li>
                            <li id="landing_tab"><a href="#landing_page"><span>Dynamic Pages</span></a></li>
                        </ul>
                        <div id="pixel">
                            <?php $checked = (($affiliate['active_pixel']))? 'checked':'' ?>
                            <div id='pixel_activate'>
                                Affiliate uses pixels:
                                <?=$this->form->checkbox('active_pixel', array('value' => '1', 'checked' => $checked)); ?>
                            </div>
                            <br/>
                            <br/>
                            <div id="pixel_panel"><!--start pixel panel-->
                                <h5>Add Pixels</h5>
                                <input type="button" name="add_pixel" value="add pixel" id="add_pixel"/>
                                <input type="button" name="remove_pixel" value="remove pixel" id="remove_pixel"/>
                                <br/>
                                <br/>
                                <?php
                                    $count=0;
                                    $size= (array_key_exists('pixel', $affiliate->data())) ? count($affiliate['pixel']) : 0;

                                    if( $size > 0):
                                    foreach($affiliate['pixel'] as $pixel):
                                        $checked = (($pixel['enable'])) ? 'checked' : '';
                                        $pix = $pixel['pixel'];
                                        $option = '';
                                        $option_codes = "<option value='all'> all </option>";
                                        foreach($sitePages as $key => $name){
                                            if( array_key_exists('page', $pixel->data()) && $pixel['page'] && in_array($key , $pixel['page']->data()) ){
                                                $option .= "<option value=$key selected='selected'> $name </option>";
                                            }else{
                                                $option .= "<option value= $key> $name </option>";
                                            }
                                        }
                                     foreach($affiliate['invitation_codes'] as $key => $name){
                                        if( array_key_exists('codes', $pixel->data()) && $pixel['codes'] && in_array($name , $pixel['codes']->data()) ){
                                            $option_codes .= "<option value=$name selected='selected'> $name </option>";
                                        }else{
                                            $option_codes .= "<option value= $name> $name </option>";
                                        }
                                    }
                                ?>
                                <div id='<?php echo 'pixel_'.($count+1)?>'>
                                    <label> Pixel # <?=$count+1; ?> </label><br/>
                                    Enable:
                                    <?=$this->form->checkbox('pixel['.$count.'][enable]', array('value'=>'1', 'checked'=> $checked)); ?> <br/>
                                    Select Page(s):<br/>
                                    <select name="<?php echo 'pixel['.$count.'][page][]'; ?>" multiple='multiple' size='5'>
                                        <?php echo $option; ?>
                                    </select>
                                    <br/>
                                    <br/>
                                    Select code(s) pixel applies to:
                                    <select name="<?php echo 'pixel['.$count.'][codes][]'; ?>" multiple='multiple' size='5' class ='relevantCodes'>
                                        <?php echo $option_codes; ?>
                                    </select>
                                    <br/>
                                    Pixel:<br/>
                                    <?=$this->form->textarea('pixel['.$count.'][pixel]', array('value' => $pix, 'rows'=>'6', 'cols'=>'50')); ?>
                                    <br/>
                                </div>
                                <?php
                                    $count++;
                                    endforeach;
                                    endif;
                                ?>
                                <input type="hidden" id="pixel_count" name="pixel_count" value="<?php echo $count; ?>" />
                                <br/>
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
                            <strong>
                            1. Upload the background image first <br/>
                            2. Add a category <br/>
                            3. Select the associated affiliate code<br/>
                            </strong><br/>
                            <?=$this->html->link('refresh' ,'#categories' ,array('id' => 'cat_refresh' ,'onClick' => 'refreshCategories()'));?>
                        </p>
                        <div id="landing_panel">
                        <!--Current Background Images-->
                            <div id="categories"></div>
                        <!--End of Current Background Image-->
							<h3>Upload Images:</h3>
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
                                href="#categories"
                                class="upload_files_link"
                                onClick="document.getElementById('agileUploaderSWF').submit(); refreshCategories();"
                            >
                                Start Upload <?=$this->html->image('agile_uploader/upload-icon.png', array('height' => '24')); ?>
                            </a>
                            </div><!--end of landing panel-->
                        </div><!--end landing page-->
                </div><!--end tabs-->
            </div><!--end block-->
        </div><!--end grid 8-->
	<?=$this->form->end(); ?>
</div>
<script type="text/javascript">

$(document).ready(function() {
	$(".affiliate_category").autocomplete({source: allAffiliateCategories, minChars:0, minLength:0, mustMatch:false});
	//create tabs
	$("#tabs").tabs();
	$('#pending_page').hide();
	refreshCategories();
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
			$('#landing_panel').show();
			$('#pending_tab').show();
			$('#pending_page').show();
		} else {
			$('#landing_panel').hide();
			$('#pending_tab').hide();
			$('#pending_page').hide();
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
				$('#pending_tab').show();
				$('#pending_page').show();
			}else{
				$('#landing_panel').hide();
				$('#pending_tab').hide();
				$('#pending_page').hide();
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
            codes = getCodes();
			newPixelDiv.html(unescape("<label> Pixel #" +counter + "</label> <br/> Enable:"+
				'<?=$this->form->checkbox("pixel['+(counter-1)+'][enable]", array("value"=>"1", "checked"=>"checked")); ?> <br/> Select:'+
				'<?=$this->form->select("pixel['+(counter-1)+'][page]", $sitePages, array("multiple"=>"multiple", "size"=>5)); ?><br/> Pixel<br/>'+
				'<select name="pixel['+(counter-1)+'][codes][]" multiple="multiple"  size=5 class = "relevantCodes">' + codes+'</select><br/>Pixel<br/>'+
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
			$('.relevantCodes option[value=' + value + ']').remove();
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
	function getCodes() {
        var tmp = "";

        $('#InvitationCodes option').each(function(index,val){
            tmp = tmp + "<option value=" + $(val).text() + ">" + $(val).text() + "</option>";
        });
        return tmp;
    }
    function refreshCategories() {
        var target = $("#categories");

        $.ajax({
            async: false,
            url: "/affiliates/categories/<?=$affiliate->_id;?>",
            type: "POST",
            success: function(data) {
                target.html(data);
            }
        });
    }
</script>
