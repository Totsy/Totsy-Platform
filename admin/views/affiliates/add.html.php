<?=$this->html->script('tiny_mce/tiny_mce.js');?>
<?=$this->html->script('jquery-1.4.2');?>
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

<div class="grid_16">
	<h2 id="page-heading">Affiliate Add Panel</h2>
</div>

<div class='grid_3 menu'>
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
<div class="grid_2 box">
	<div class='block forms'>
		<?=$this->form->create(); ?>

		Activate: <?=$this->form->checkbox('active', array('checked'=>'checked')); ?> <br>
		Affiliate Level: <?=$this->form->select('level',$packages); ?> <br><br>
		Affiliate Name:
		<?=$this->form->text('affiliate_name'); ?> <br><br>
		Enter Code:
		<?=$this->form->text('code'); ?>  <input type='button' name='add_code' id='add_code' value='add'/>
		<br>
		Affiliate codes:<br>
		<?=$this->form->select('invitation_codes',array(),array('multiple'=>'multiple', 'size'=>5)); ?> <br>
		<input type='button' name='edit_code' id='edit_code' value='edit code'/><br><br>
	</div>
</div> <!--end of box-->
<div class ="grid_13 box">
	<div class='block forms'>
		<div id='tabs'>
			<ul>
				<li><a href="#pixel"><span>Pixels</span></a></li>
				<li><a href="#landing_page"><span>Landing Pages</span></a></li>
			</ul>
			<div id='pixel'>
				<div id='pixel_activate'> Affiliate uses pixels: <?=$this->form->checkbox('active_pixel', array('value'=>'1')); ?> </div>
				<br>
				<br>
				<div id='pixel_panel'>
					<h5>Add Pixels</h5>
					<input type='button' name='add_pixel' value='add pixel'id='add_pixel'/>
					<input type='button' name='remove_pixel' value='remove pixel' id='remove_pixel'/>
					<br>
					<br>

					<div id='pixel_1'>
						<label> Pixel #1 </label><br>
						Enable:
						<?=$this->form->checkbox('pixel[0][enable]', array('value'=>'1', 'checked'=>'checked')); ?> <br>
						Select Page(s):<br>
						<?=$this->form->select('pixel[0][page]', $sitePages, array('multiple'=>'multiple', 'size'=>5)); ?><br>
						Pixel:<br>
						<?=$this->form->textarea('pixel[0][pixel]' , array('rows'=>'10', 'cols' =>'30')); ?>
					</div>
					<br>
				</div><!--end of pixel panel-->
			</div><!--end of pixel-->
			<div id='landing_page'>
				<div id='landing_activate'> Affiliate uses landing pages:
					<?=$this->form->checkbox('active_landing', array('value'=>'1')); ?>
				</div>
				<div id='landing_panel'>
					<br/>
					<div id='template_panel'>
						<label>Enable </label>
						<?=$this->form->checkbox('landing_enable', array('value'=>'1', 'checked' => 'checked')); ?><br/>

						<label>Choose Template Type </label>
						<?=$this->form->select('template_type', $templates, array('id' => 'templates') );
						?>
						<label>Name:</label>
						<?=$this->form->text('name'); ?>
						<label>Specified Url:</label>
						<?=$this->form->text('url'); ?>
						<div id="template" style="margin: 0 5 0 0">
							<?php echo $this->view()->render(array('element' => 'template1')); ?>
						</div>
					</div>
				</div><!--end landing panel-->
			</div><!--end landing page-->

		</div><!--end tabs-->
	</div>
</div>
		<br>
		<br>
	<div class="clear"></div>
	<div id='submit button' class="grid_2">
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
<script type='text/javascript'>
		$('#pixel_panel').hide();
		$('#landing_panel').hide();
	$(document).ready(function(){
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

			$('#pixel_'+counter).remove();
		});
	});

	//multi select transfer transfer
	$().ready(function(){
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
	$().ready(function(){
		$('#create').click(function(){
			$('#InvitationCodes').each(function(){
				$('#InvitationCodes option').attr('selected','selected');
			});
		});
	});
</script>
<script type="text/javascript">
$(function(){
    $('#headline_1').editable({
        onEdit:begin,
        onSubmit:submit,
        type:'textarea'
    });
    function begin(){
        this.append('Click any to submit');
    }
    function submit(){
        $('input:hidden[name=headline_1]').val(function(){
            return $('#headline_1').html();
        });
    }
});
$(function(){
    $('#headline_2').editable({
        onEdit:begin,
        onSubmit:submit,
        type:'textarea'
    });
    function begin(){
        this.append('Click any to submit');
    }
     function submit(){
         $('input:hidden[name=headline_2]').val(function(){
            return $('#headline_2').html();
        });
    }
});
$(function(){
    $('#headline_3').editable({
        onEdit:begin,
        onSubmit:submit,
        type:'textarea'
    });
    function begin(){
        this.append('Click any to submit');
    }
     function submit(){
         $('input:hidden[name=headline_3]').val(function(){
            return $('#headline_3').html();
        });
    }
});
$(function(){
    $('#test').editable({
        onEdit:begin,
        type:'textarea'
    });
    function begin(){
        this.append('Click any to submit');
    }
});
$(function(){
    $('#sub').editable({
        onEdit:begin,
        type:'textarea'
    });
    function begin(){
        this.append('Click any to submit');
    }
});
</script>