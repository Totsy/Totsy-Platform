
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

<div class="grid_16">
	<?=$this->form->create(); ?>

	Activate: <?=$this->form->checkbox('active', array('checked'=>'checked')); ?> <br>
	Affiliate Name:
	<?=$this->form->text('affiliate_name'); ?> <br><br>
	Enter Code:
	<?=$this->form->text('code'); ?>  <input type='button' name='add_code' id='add_code' value='add'/>
	<br>
	Affiliate codes:<br>
	<?=$this->form->select('invitation_codes',array(),array('multiple'=>'multiple', 'size'=>5)); ?> <br>
	<input type='button' name='edit_code' id='edit_code' value='edit'/><br><br>
	Affiliate uses pixels: <?=$this->form->checkbox('active_pixel', array('value'=>'1')); ?>
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
			<?=$this->form->select('pixel[0][page][]', $sitePages, array('multiple'=>'multiple', 'size'=>5)); ?><br>
			Pixel:<br>
			<?=$this->form->textarea('pixel[0][pixel]'); ?>
		</div>
		<br>
	</div>
	<br>
	<br>
	<?=$this->form->submit('Create', array('id'=>'create')); ?>
	<?=$this->form->end(); ?>
</div>

<script type='text/javascript'>
		$('#pixel_panel').hide();
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
		var counter =2;

		$('#add_pixel').click(function(){
			var newPixelDiv = $(document.createElement('div')).attr("id", "pixel_"+counter);

			newPixelDiv.html("<label> Pixel #" +counter + "</label> <br> Enable:"+
				'<?=$this->form->checkbox("pixel['+(counter-1)+'][enable]", array("value"=>"1", "checked"=>"checked")); ?> <br> Select:'+
				'<?=$this->form->select("pixel['+(counter-1)+'][page]", $sitePages, array("multiple"=>"multiple", "size"=>5)); ?><br> Pixel<br>'+
				'<?=$this->form->textarea("pixel['+(counter-1)+'][pixel]"); ?>'
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