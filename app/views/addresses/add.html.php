<?php use app\models\Address; ?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>

<?php $this->title("Add / Edit Address Book "); ?>
<?php if (!$isAjax): ?>
<div class="grid_16">
	<h2 class="page-title gray">Add / Edit Address Book </h2>
	<hr />
</div>
<div class="grid_4">
	<div class="roundy grey_inside">
		<h3 class="gray">My Account</h3>
		<hr />
		<ul class="menu main-nav">
		<li><a href="/account" title="Account Dashboard">Account Dashboard</a></li>
		<li><a href="/account/info" title="Account Information">Account Information</a></li>
		<li><a href="/account/password" title="Change Password">Change Password</a></li>
		<li class="active"><a href="/addresses" title="Address Book">Address Book</a></li>
		<li><a href="/orders" title="My Orders">My Orders</a></li>
		<li><a href="/Credits/view" title="My Credits">My Credits</a></li>
		<li><a href="/Users/invite" title="My Invitations">My Invitations</a></li>
		</ul>
	</div>
	<div class="clear"></div>
	<div class="roundy grey_inside">
		<h3 class="gray">Need Help?</h3>
		<hr />
		<ul class="menu main-nav">
		    <li><a href="/tickets/add" title="Contact Us">Help Desk</a></li>
			<li><a href="/pages/faq" title="Frequently Asked Questions">FAQ's</a></li>
			<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="/pages/terms" title="Terms Of Use">Terms Of Use</a></li>
		</ul>
	</div>
</div>
<?php endif ?>


<div class="grid_11 omega roundy grey_inside<?php if (!$isAjax): ?> b_side <?php endif ?>">

	<?php if ($message): ?>
		<div class="standard-message"><?=$message; ?></div>
	<?php endif ?>

	<h2 class="page-title gray">Add / Edit Address Book <span style="float:right; font-weight:normal; font-size:12px;"><?php if (!$isAjax): ?>
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
			<?=$this->form->submit('Update Address Book', array('class' => 'button')); ?>
			
		</fieldset> 
		<?php if ($isAjax): ?>
			<?=$this->form->hidden('isAjax', array('value' => 1)); ?>
		<?php endif ?>
	<?=$this->form->end();?> 
	<br />

</div>
</div>
<div class="clear"></div>

<script type="text/javascript">
jQuery(function($){
   $("#date").mask("99/99/9999");
   $("#phone").mask("(999) 999-9999");
   $("#tin").mask("99-9999999");
   $("#zip").mask("99999");
});
</script>
