<?php $this->title("My Addresses"); ?>
<h1 class="p-header">My Account</h1>

	<div id="left">
		<ul class="menu main-nav">
		<li class="firstitem17 "><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
	    <li class="item18"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
	    <li class="item19 active"><a href="/addresses" title="Address Book"><span>Address Book</span></a></li>
	    <li class="item20"><a href="/orders" title="My Orders"><span>My Orders</span></a></li>
	    <li class="item20"><a href="/Credits/view" title="My Credits"><span>My Credits</span></a></li>
	    <li class="item22"><a href="/tickets/add" title="Help Desk"><span>Help Desk</span></a></li>
	    <li class="lastitem23"><a href="/Users/invite" title="My Invitations"><span>My Invitations</span></a></li>
		  <br />
		  <h3 style="color:#999;">Need Help?</h3>
		  <hr />
		  <li class="first item18"><a href="/tickets/add" title="Contact Us"><span>Help Desk</span></a></li>
		  <li class="first item19"><a href="/pages/faq" title="Frequently Asked Questions"><span>FAQ's</span></a></li>
		</ul>
	</div>

<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
	<h2 class="gray mar-b">Address Book <span style="float:right; font-weight:normal; font-size:12px;"><?=$this->html->link('Add New Address','Addresses::add'); ?></span></h2>
	<hr />
	
		<table width="100%" class="cart-table">
			<thead>
				<tr>
					<th>#</th>
					<th>Description <span style="font-size:10px; color:#fff;">(i.e. home, work, school, etc)</span></th>
					<th>Address</th>
					<th>Remove</th>
				</tr>
			</thead>
			<tbody>
			<?php $x = 0?>
			<?php foreach ($addresses as $address): ?>
				<?php $x++; ?>
				<tr id="<?=$address->_id?>">
				<td>
					<?=$x?>
				</td>
				<td>
					<?=$address->description?>
				</td>
				<td>
					<div id='name'><?=$address->firstname." ".$address->lastname?></div>
					<div id='address'>
						<?=$address->address?><br><?=$address->address_2?><br>
						<?=$address->city?>, <?=$address->state?>, <?=$address->zip?><br>
						<?=$this->html->link('Edit', array('controller' => 'Addresses', 'action' => 'edit', 'args' => $address->_id)); ?>
					</div>
				</td>
				<td align='center'>
					<a href="#" id="<?php echo "remove$address->_id"?>" title="Remove Address" class="delete">delete</a>
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
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
<?php if (!empty($removeButtons)): ?>
	<?php foreach ($removeButtons as $button): ?>
		<?php echo $button ?>
	<?php endforeach ?>
<?php endif ?>
