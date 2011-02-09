<?php use app\models\Address; ?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>


	<h1 class="p-header">My Account</h1>
	<div id="left">
		<ul class="menu main-nav">
		<li class="firstitem17 "><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
	    <li class="item18"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
	    <li class="item19 active"><a href="/addresses" title="Address Book"><span>Address Book</span></a></li>
	    <li class="item20"><a href="/orders" title="My Orders"><span>My Orders</span></a></li>
	    <li class="item20"><a href="/Credits/view" title="My Credits"><span>My Credits</span></a></li>
	    <li class="lastitem23"><a href="/Users/invite" title="My Invitations"><span>My Invitations</span></a></li>
		  <br />
		  <h3 style="color:#999;">Need Help?</h3>
		  <hr />
		  <li class="first item18"><a href="/tickets/add" title="Contact Us"><span>Help Desk</span></a></li>
		  <li class="first item19"><a href="/pages/faq" title="Frequently Asked Questions"><span>FAQ's</span></a></li>
		</ul>
	</div>



<div class="tl"></div>
<div class="tr"></div>
<div id="page">
	<?php if ($message): ?>
		<div class="standard-message"><?=$message; ?></div>
	<?php endif ?>

	<h2 class="gray mar-b">Add / Edit Address Book <span style="float:right; font-weight:normal; font-size:12px;"><?php if (!$isAjax): ?>
                <?=$this->html->link('Manage Address Book','addresses');?><?php endif ?></span>
	</h2>
	<hr />
	<?=$this->form->create($address, array(
		'id' => 'addressForm',
		'class' => "fl",
		'action' => "{$action}/{$address->_id}"
	)); ?>
		<fieldset> 
			<legend class="no-show">New Address</legend> 
			<?php if (!$isAjax): ?>
				<div class="form-row">
					<label class="addresses">Make Default</label> 
					<input type="radio" name="default" value="1" checked> Yes<br>
					<input type="radio" name="default" value="0"> No
				</div>
			<?php endif ?>
			<div class="form-row"> 
				<?=$this->form->label('description', 'Description <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('description', array('class' => 'inputbox')); ?>
				<?=$this->form->error('description'); ?>
			<span style="font-size:10px;">(i.e. home, work, school, etc)</span>
            </div>
            
			<div class="form-row">
				<?=$this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('firstname', array('class' => 'inputbox')); ?>
				<?=$this->form->error('firstname'); ?>
			</div>

			<div class="form-row">
				<?=$this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
				<?=$this->form->error('lastname'); ?>
			</div>

			<div class="form-row">
				<?=$this->form->label('telephone', 'Telephone', array('escape' => false,'class' => 'addresses')); ?>
				<?=$this->form->text('telephone', array('class' => 'inputbox', 'id' => 'phone')); ?>
			</div>

			<div class="form-row">
				<?=$this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('address', array('class' => 'inputbox')); ?>
				<?=$this->form->error('address'); ?>
			</div>

			<div class="form-row">
				<?=$this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'addresses')); ?>
				<?=$this->form->text('address_2', array('class' => 'inputbox')); ?>
			</div>

			<div class="form-row">
				<?=$this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('city', array('class' => 'inputbox')); ?>
				<?=$this->form->error('city'); ?>
			</div>

			<div class="form-row">
				<label for="state" class='required'>State <span>*</span></label>
				<?=$this->form->select('state', Address::$states, array('empty' => 'Select a state')); ?>
				<?=$this->form->error('state'); ?>
			</div>

			<div class="form-row">
				<?=$this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
			</div> 
			<?=$this->form->submit('Submit', array('class' => 'flex-btn fr')); ?>
		</fieldset> 
		<?php if ($isAjax): ?>
			<?=$this->form->hidden('isAjax', array('value' => 1)); ?>
		<?php endif ?>
	<?=$this->form->end();?> 
	
</div> 
<script type="text/javascript">
jQuery(function($){
   $("#date").mask("99/99/9999");
   $("#phone").mask("(999) 999-9999");
   $("#tin").mask("99-9999999");
   $("#ssn").mask("999-99-9999");
   $("#zip").mask("99999");
});
</script>
<div class="bl"></div>
<div class="br"></div>
