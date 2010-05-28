<?=$this->html->script('mootools-1.2.4-core-nc.js');?>
<?=$this->html->script('mootools-1.2.4.4-more.js');?>
<?=$this->html->script('formcheck.js');?>
<?=$this->html->script('en.js');?>
<?=$this->html->style('formcheck');?>

<script type="text/javascript">
    window.addEvent('domready', function(){
        new FormCheck('addressForm');
    });
</script>
<?php
	use app\extensions\helper\Menu;

	$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
	$leftMenu = Menu::build($navigation, $options);
	echo $leftMenu;
	
?>
<div class="tl"></div> 
<div class="tr"></div> 
<div id="page"> 
 	
	<h2 class="gray mar-b">Add New Address</h2>
	
	<?=$this->form->create('',array('id'=>'addressForm', 'class' => "fl"));?>
		<fieldset> 
			<legend class="no-show">New Address</legend> 
			<div class="form-row">
				<label for="type">Address Type</label> 
				<select name="type">
				  <option>Billing</option>
				  <option>Shipping</option>
				</select>
			</div>
			<div class="form-row">
				<label>Make Default</label> 
				<input type="radio" name="default" value="Yes" checked> Yes<br>
				<input type="radio" name="default" value="No">No
			</div>
			<div class="form-row"> 
				<label for="fname">Description</label> 
				<input type="text" name="description" id="description" class="validate['required']"/> 
			</div>
			<div class="form-row"> 
				<label for="fname">First Name</label> 
				<input type="text" name="firstname" id="fname" class="validate['required']"/> 
			</div> 
			
			<div class="form-row"> 
				<label for="lname">Last Name</label> 
				<input type="text" name="lastname" id="lname" class="validate['required']"/> 
			</div> 
			
			<div class="form-row"> 
				<label for="company">Company</label> 
				<input type="text" name="company" id="company" class="inputbox" value="" /> 
			</div> 
			
			<div class="form-row"> 
				<label for="phone">Telephone</label> 
				<input type="text" name="phone" id="phone" class="inputbox" value="" /> 
			</div> 
			
			<div class="form-row"> 
				<label for="fax">Fax</label> 
				<input type="text" name="fax" id="fax" class="inputbox" value="" /> 
			</div> 
			
			<div class="form-row"> 
				<label for="address">Street Address</label> 
				<input type="text" name="address" id="address" class="validate['required']" value="" /> 
			</div> 
			
			<div class="form-row"> 
				<label for="address_2">Street Address 2</label> 
				<input type="text" name="address_2" id="address_2" class="inputbox" value="" /> 
			</div> 
			
			<div class="form-row"> 
				<label for="city">City</label> 
				<input type="text" name="city" id="city" class="validate['required']" value="" /> 
			</div> 
			
			<div class="form-row"> 
				<label for="state">State/Province</label> 
				<input type="text" name="state" id="state" class="validate['required']" value="" /> 
			</div> 
			
			<div class="form-row"> 
				<label for="zip">Zip/Postal Code</label> 
				<input type="text" name="zip" id="zip" class="validate['required']" value="" /> 
			</div> 
			
			<div class="form-row"> 
				<label for="country">Country</label> 
				<input type="text" name="country" id="country" class="validate['required']" value="" /> 
			</div> 
			
			<button type="submit" name="submit" class="flex-btn fr"><span>Submit</span></button> 
			
		</fieldset> 
	
	<?=$this->form->end();?> 
	<?=$this->html->link('Manage Address','account/addresses');?>
</div> 

<div class="bl"></div> 
<div class="br"></div> 
			
















