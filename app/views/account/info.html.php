<?=$this->html->style('style');?>
<div class="tl"></div>
<div class="tr"></div>
<div id="page">

	<h2 class="gray mar-b">Edit Account Information</h2>
	<fieldset id="" class="">
		<legend>Account Information</legend>
		<div>
			<?php 
				if($sucess){
					echo "Thank You for Updating Your Information";
				}
			?>
		</div>
		<?=$this->form->create();?>
		<?=$this->form->field('firstname', array(
				'label' => 'First Name' ,
				'class'=>'required', 
				'type' => 'text', 
				'name' => 'test', 
				'value' => "$data[firstname]"
			))?>
		<?=$this->form->field('lastname', array(
				'label' => 'Last Name', 
				'class'=>'required', 
				'value' => "$data[lastname]"
			));?>
		<?=$this->form->field('email', array(
				'label' => 'E-Mail', 
				'class'=>'required', 
				'value' => "$data[email]"
			));?>
		<button type="submit" name="submit" class="flex-btn fr"><span>Submit</span></button>
		<?=$this->form->end();?>	
	</fieldset>
	
</div>
<div class="bl"></div>
<div class="br"></div>