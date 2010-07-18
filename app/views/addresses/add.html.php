<?=$this->html->script('mootools-1.2.4-core-nc.js');?>
<?=$this->html->script('mootools-1.2.4.4-more.js');?>
<?=$this->html->script('formcheck.js');?>
<?=$this->html->script('en.js');?>
<?=$this->html->style('formcheck');?>

<!-- <script type="text/javascript">
    window.addEvent('domready', function(){
        new FormCheck('addressForm');
    });
</script> -->
<?php
	$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
	$leftMenu = $this->MenuList->build($menu, $options);
	echo $leftMenu;

?>
<div class="tl"></div> 
<div class="tr"></div> 
<div id="page"> 
<p>	
<?php if($message) {
	echo $message;	
}?>
</p>
	<h2 class="gray mar-b">Add/Edit Address</h2>
	
	<?=$this->form->create($address, array('id'=>'addressForm', 'class' => "fl"));?>
		<fieldset> 
			<legend class="no-show">New Address</legend> 
			<div class="form-row">
				<label for="type">Address Type</label> 
				<select name="type" value= "<?=$address->type; ?>"
				  <option>Billing</option>
				<option>Shipping</option>			  
				</select>
			</div>
			<div class="form-row">
				<label>Make Default</label> 
				<input type="radio" name="default" value="1" checked> Yes<br>
				<input type="radio" name="default" value="0">No
			</div>
			<div class="form-row"> 
				<?=$this->form->label('description', 'Description'); ?>
				<?=$this->form->text('description', array('class' => 'inputbox')); ?> 
			</div>
			<div class="form-row"> 
				<?=$this->form->label('firstname', 'First Name'); ?>
				<?=$this->form->text('firstname', array('class' => 'inputbox')); ?> 
			</div> 
			
			<div class="form-row"> 
				<?=$this->form->label('lastname', 'Last Name'); ?>
				<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
			</div> 
			
			<div class="form-row"> 
				<?=$this->form->label('company', 'Company'); ?>
				<?=$this->form->text('company', array('class' => 'inputbox')); ?>
			</div> 
			
			<div class="form-row"> 
				<?=$this->form->label('telephone', 'Telephone'); ?>
				<?=$this->form->text('telephone', array('class' => 'inputbox')); ?>
			</div> 
			
			<div class="form-row"> 
				<?=$this->form->label('fax', 'Fax'); ?>
				<?=$this->form->text('fax', array('class' => 'inputbox')); ?>
			</div> 
			
			<div class="form-row"> 
				<?=$this->form->label('address', 'Street Address'); ?>
				<?=$this->form->text('address', array('class' => 'inputbox')); ?>
			</div> 
			
			<div class="form-row"> 
				<?=$this->form->label('address_2', 'Street Address 2'); ?>
				<?=$this->form->text('address_2', array('class' => 'inputbox')); ?>
			</div> 
			
			<div class="form-row"> 
				<?=$this->form->label('city', 'City'); ?>
				<?=$this->form->text('city', array('class' => 'inputbox')); ?>
			</div> 
			
			<div class="form-row"> 
				<?=$this->form->label('state', 'State/Province'); ?>
				<?=$this->form->text('state', array('class' => 'inputbox')); ?>
			</div> 
			
			<div class="form-row"> 
				<?=$this->form->label('zip', 'Zip/Postal Code'); ?>
				<?=$this->form->text('zip', array('class' => 'inputbox')); ?>
			</div> 
			
			<div class="form-row"> 
				<?=$this->form->label('country', 'Country'); ?>
				<?=$this->form->text('country', array('class' => 'inputbox')); ?>
			</div> 
			<?=$this->form->submit('Submit', array('class' => 'flex-btn fr')); ?>
		</fieldset> 

	<?=$this->form->end();?> 
	<?=$this->html->link('Manage Address','addresses');?>
</div> 

<div class="bl"></div> 
<div class="br"></div> 
