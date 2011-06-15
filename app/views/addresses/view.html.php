<?php $this->title("Address Book"); ?>

<div class="grid_16">
	<h2 class="page-title gray">Address Book</h2>
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
<div class="grid_11 omega roundy grey_inside b_side">
	<h2 class="page-title gray">Address Book <span style="float:right; font-weight:normal; font-size:12px;"><?=$this->html->link('Add New Address','Addresses::add'); ?></span></h2>
	<hr />
		<?php if (!empty($address)): ?> 
		<table width="100%" class="cart-table">
			<tbody>
			<?php $x = 0?>
			<?php foreach ($addresses as $address): ?>
				<?php $x++; ?>
				<tr id="<?=$address->_id?>">
				<td>
						<strong>Location:</strong><?=$address->description?>
						<hr/>
						<?=$address->firstname." ".$address->lastname?>
						<?=$address->address?><br>
						<?=$address->address_2?><br>
						<?=$address->city?>, <?=$address->state?>, <?=$address->zip?><br>
						<?=$this->html->link('Edit', array('controller' => 'Addresses', 'action' => 'edit', 'args' => $address->_id)); ?>
					<a href="#" id="<?php echo "remove$address->_id"?>" title="Remove Address"><img src="/img/trash.png" width="25" /></a>
				</td>
				<?php
					$removeButtons[] = "<script type=\"text/javascript\" charset=\"utf-8\">
							$('#remove$address->_id').click(function () { 
								$('#$address->_id').remove();
								$.ajax({url: $.base + \"addresses/remove\", data:'$address->_id', context: document.body, success: function(data){
								      }});
							    });
						</script>";
				?>
			<?php endforeach ?>
				</tr>
			</tbody>
		</table>
		<?php else : ?>
		<div style="text-align:center;">You don't have any addresses yet. </div>
		<?php endif ?>
</div>
</div>
<div class="clear"></div>

<?php if (!empty($removeButtons)): ?>
	<?php foreach ($removeButtons as $button): ?>
		<?php echo $button ?>
	<?php endforeach ?>
<?php endif ?>