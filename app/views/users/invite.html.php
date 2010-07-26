<?php $this->title("My Invitations "); ?>
<?=$this->html->script(array('jquery.equalheights', 'jquery.flash')); ?>
<h1 class="p-header">My Account</h1>
<?=$this->menu->render('left');?>				
			<!-- Start Main Page Content -->
		<div id="middle" class="noright">				
			
			<div class="tl"></div>
			<div class="tr"></div>
			<div id="page">
				
				<div id="tabs">
					<?php if (!empty($flashMessage)): ?>
						<div id='flash'><strong><?=$flashMessage?></strong></div>
					<?php endif ?>
					<ul>
						<li><a href="#sendinvites"><span>Send</span></a></li>
					    <li><a href="#openinvites"><span>Open Invitations</span></a></li>
					    <li><a href="#acceptedinvites"><span>Accepted</span></a></li>
					</ul>
					<!-- Start Open Invitations Tab -->
					<div id="sendinvites" class="ui-tabs-hide">
						<div class="send-left">
							<h2 class="gray">Send Invitations</h2>
							<p>For each friend you invite, Totsy will credit your account with $15 after your friend's place their first order.</p>
								<fieldset>
									<br>
									<?=$this->form->create(); ?>
										<?=$this->form->hidden('invitation_code', array('value' => 'ylveslecoq')); ?>
										<?=$this->form->label('To:'); ?>
										<?=$this->form->textarea('to', array(
											'class' => 'inputbox',
											'id' => 'to', 
											'style' => "width:320px",
											'value' => 'Separate email addresses by commas',
											'onblur' => "if(this.value=='') this.value='Separate email addresses by commas';",
											'onfocus' => "if(this.value=='Separate email addresses by commas') this.value='';"
											)); ?>
										<br>
										<?=$this->form->label('Comments:'); ?>
										<?=$this->form->textarea('message', array(
											'class' => 'inputbox',
											'id' => 'comments', 
											'style' => "width:320px",
											'value' => "Please accept this invitation to join Totsy",
											'onblur' => "if(this.value=='') this.value='Please accept this invitation to join Totsy';",
											'onfocus' => "if(this.value=='Please accept this invitation to join Totsy') this.value='';"
											)); ?>
										<br>
										<?=$this->form->submit('Send', array('class' => 'flex-btn fr')); ?>
									<?=$this->form->end(); ?>
									<br><br><br>
									<?php $invite = "http://totsy.com/invitation/$user->invitation_code";?>
									<p>Share this link with your friends</p>
									<a href="#" title="Share this link with your friends" class="bold"><?=$invite?></a>
								</fieldset>
						</div>
						<div class="send-right r-container">
							<div class="tl"></div>
							<div class="tr"></div>
							<div class="r-box-2">
				
								<h2 class="gray">Share with your friends</h2>
								
								<a href="#" href="Share Totsy with your Facebook friends" id="invite-facebook" class="invite-btn fl">Facebook</a>
								<a href="#" href="Share Totsy with your Twitter followers" id="invite-twitter" class="invite-btn fr">Twitter</a>
								
								<div class="dividing-line clear mar-b"><!-- --></div>
								
								<h2 class="gray clear">Invite from your address book</h2>
								
								<a href="#" href="Invite friends from your Gmail contacts" id="invite-gmail" class="invite-btn fl">Gmail</a>
								<a href="#" href="Invite friends from your Yahoo! contacts" id="invite-yahoo" class="invite-btn fr">Yahoo!</a>
								
								<a href="#" href="Invite friends from your Outlook address book" id="invite-outlook" class="invite-btn fl">Outlook</a>
								<a href="#" href="Invite friends from your AOL contacts" id="invite-aol" class="invite-btn fr">AOL</a>
								
								<a href="#" href="Invite friends from your MSN address book" id="invite-msn" class="invite-btn fl">MSN</a>
								<a href="#" href="Invite friends" id="invite-others" class="invite-btn fr">Others</a>
				
							</div>
							<div class="bl"></div>
							<div class="br"></div>
						</div>
						
					</div>
					<!-- End Send Invitations Tab -->
						
						
					<!-- Start Open Invitations Tab -->
					<div id="openinvites" class="ui-tabs-hide">
						<?php if (!empty($open->invitations)): ?>
							<table cellpadding="0" cellspacing="0" border="0" width="100%" class="order-table">
								<thead>
									<tr>
										<th>#</th>
										<th>E-Mail</th>
										<th>Date Sent</th>
									</tr>
								</thead>
							<?php $x = 1;?>
							<?php $y = 0;?>
								<tbody>
									<?php $invites = $open->invitations->data() ?>
							<?php foreach ($invites as $key => $value): ?>
									<tr class="alt<?php echo ( ($y++ % 2) == 1 ? 0 : 1); ?>">
										<td><?=$x?></td>
										<td><?=$value['email']?></td>
										<td><?=date('M-d-Y h:i:s', $value['date_sent']->sec); ?></td>
									</tr>
								<?php $x++ ?>
							<?php endforeach ?>
								</tbody>
							</table>
						<?php else: ?>
							<strong>All the friends you've invited have accepted.</strong><br />
							Nice! Now why not go invite more? Get a $15 Credit when they place their first order.
						<?php endif ?>
					</div>
					<!-- End Open Invitations Tab -->
					
					<!-- Start Accepted Invitations Tab -->
					<div id="acceptedinvites" class="ui-tabs-hide">
						<?php if (!empty($accepted->invitations)): ?>
							<table cellpadding="0" cellspacing="0" border="0" width="100%" class="order-table">
								<thead>
									<tr>
										<th>Friends</th>
										<th>Member Since</th>
										<th>Credits Earned</th>
									</tr>
								</thead>
							<?php $x = 1;?>
							<?php $y = 0;?>
								<tbody>
									<?php $invites = $accepted->invitations->data() ?>
							<?php foreach ($invites as $key => $value): ?>
									<tr class="alt<?php echo ( ($y++ % 2) == 1 ? 0 : 1); ?>">
										<td><?=$value['friend']?></td>
										<td><?=date('M-d-Y h:i:s', $value['member_created']->sec); ?></td>
										<td><?=$value['credit']?></td>
									</tr>
								<?php $x++ ?>
							<?php endforeach ?>
								</tbody>
							</table>
						<?php else: ?>
							<strong>You haven't invited anyone to Totsy. Why not send and invitation and get a $15 Credit when they place their first order</strong><br>
						<?php endif ?>
					
					</div>
					<!-- End Accepted Invitations Tab -->
				
				</div>
									
			</div>
			<div class="bl"></div>
			<div class="br"></div>
			
		</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#tabs").tabs();
	});
</script>

<!-- This equals the hight of all the boxes to the same height -->
<script type="text/javascript">
	$(document).ready(function() {
		$(".r-box").equalHeights(100,300);
	});
</script>
<script type="text/javascript" charset="utf-8">
	$('.form')
</script>
