
<div class="grid_16">
	<h2 id="page-heading">Affiliate Edit Panel</h2>
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

<div class="grid_16">

	<?=$this->form->create(); ?>
	<?php $checked= (($affiliate['active']))? 'checked':'' ?>
	Activate: <?=$this->form->checkbox('active', array('checked'=>$checked)); ?> <br>
	<?php
		$option ='';
		foreach( $packages as $key){
			if( array_key_exists('level', $affiliate->data())  && $key == $affiliate['level'] ){

				$option .= "<option value= $key selected='selected'> $key</option>";
			}else{
				$option .= "<option value= $key> $key</option>";
			}
		}
	?>
	Affiliate Level: <select name="level" id='Level'> <?php echo $option; ?> </select> <br><br>
	Affiliate Name:
	<?=$this->form->text('affiliate_name', array('value' => $affiliate['name'])); ?> <br><br>
	Enter Code:
	<?=$this->form->text('code'); ?>  <input type='button' name='add_code' id='add_code' value='add'/>
	<br>

	Affiliate codes:<br>
	<?php

		$codes= array();
		foreach($affiliate['invitation_codes'] as $code){
			$codes[$code]= $code;
		}
	?>
	<?=$this->form->select('invitation_codes',$codes,array('multiple'=>'multiple', 'size'=>5)); ?> <br>
	<input type='button' name='edit_code' id='edit_code' value='edit'/><br><br>
	<?php $checked= (($affiliate['active_pixel']))? 'checked':'' ?>
	<div id='activate_pixel'> Affiliate uses pixels: <?=$this->form->checkbox('active_pixel', array('value'=>'1', 'checked'=> $checked)); ?>
		<br>
		<br>
		<div id='pixel_panel'>
			<h5>Add Pixels</h5>
			<input type='button' name='add_pixel' value='add pixel'id='add_pixel'/>
			<input type='button' name='remove_pixel' value='remove pixel' id='remove_pixel'/>
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
						if( in_array($key , $pixel['page']) ){
							$option .= "<option value=$key selected='selected'> $name </option>";
						}else{
							$option .= "<option value= $key> $name </option>";
						}
				?>
					<div id='<?php echo 'pixel_'.($count+1)?>'>
						<label> Pixel # <?=$count+1; ?> </label><br>
						Enable:
						<?=$this->form->checkbox('pixel['.$count.'][enable]', array('value'=>'1', 'checked'=> $checked)); ?> <br>
						Select Page(s):<br>
						<select name="<?php echo 'pixel['.$count.'][page][]'; ?>" multiple='multiple' size='5'> <?php echo $option; ?> </select> <br>
						Pixel:<br>
						<?=$this->form->textarea('pixel['.$count.'][pixel]', array('value' => $pix, 'rows'=>'5')); ?>
						<br>
					</div>
				<?php
					$count++;
					endforeach;
				endif;
			?>
			<input type='hidden' id="pixel_count" name="pixel_count" value="<?php echo $count; ?>" />
			<br>

		</div>
	</div>
	<br>
	<br>
	<?=$this->form->submit('Edit', array('id'=>'edit')); ?>
	<?=$this->form->end(); ?>
</div>

<script type='text/javascript'>
	$().ready(function(){
		if($('#ActivePixel').is(':checked')){
			$('#pixel_panel').show();
		}else{
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
	});
	//this jquery is for adding/removing pixel entry fields
	$(document).ready(function(){
		var counter = Number($('#pixel_count').val()) + 1;

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
		$('#edit').click(function(){
			$('#InvitationCodes').each(function(){
				$('#InvitationCodes option').attr('selected','selected');
			});
		});
	});

	//Level Selector
	$(document).ready(function(){
		if( $('#Level').val() != 'regular'){
			$('#activate_pixel').show();
		}else{
			$('#activate_pixel').hide();
		}

		$('#Level').change(function(){
			if( $('#Level').val() != 'regular'){
				$('#activate_pixel').show();
			}else{
				$('#pixel_panel').hide();
				$('#activate_pixel').hide();
			}
		});
	});
</script>