<?php $this->title("My Invitations"); ?>

<link rel="stylesheet" type="text/css" href="/css/validation-engine.jquery.css" media="screen" />
<link rel="stylesheet" type="text/css" href="/css/validation-template.css" media="screen" />
<script type="text/javascript" src="/js/form_validator/jquery.validation-engine.js" charset="utf-8"></script>    
<script type="text/javascript" src="/js/form_validator/languages/jquery.validation-engine-en.js" charset="utf-8"></script>

<div class="grid_16">
	<h2 class="page-title gray">My Invitations</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'myAccountNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
</div>

<div class="grid_11 omega roundy grey_inside b_side">

<?php if (is_object($user->invitation_codes)): ?>
										<?php foreach ($user->invitation_codes as $code): ?>
											<?php $invite = "http://www.totsy.com/join/" . $code;?>
										<?php endforeach ?>
									<?php else: ?>
										<?php $invite = "http://www.totsy.com/join/" . $user->invitation_codes;?>
									<?php endif ?>

	<h2 class="page-title gray">My Invitations <span class="fr" style="font-size:12px; margin-top:-10px;">Share this link with your friends: <br><span style="word-wrap:break-word;"><strong><a href="<?=$invite?>" title="Your Invite Link"><?=$invite?></a></strong></span></span></h2>
	<hr />
		<div id="tabs">
					<?php if (!empty($flashMessage)): ?>
						<div class='standard-message'><strong><?=$flashMessage?></strong></div>
						<br>
					<?php endif ?>
					<ul>
						<li><a href="#sendinvites"><span>Send</span></a></li>
					    <li><a href="#openinvites"><span>Open Invitations</span></a></li>
					    <li><a href="#acceptedinvites"><span>Accepted</span></a></li>
					    
					</ul>
					<!-- Start Open Invitations Tab -->
					<div id="sendinvites" class="ui-tabs-hide">
						<div class="grid_6">
							<h2 class="gray mar-b">Send Invitations</h2>
							<hr />
							<p>For each friend you invite, Totsy will credit your account with <span style="color:#009900;">$15</span> after your friend's place their first order.</p>
								<fieldset>
									<br>
									<?=$this->form->create( "", array("id"=>"inviteForm") ); ?>
										<?=$this->form->label('Enter Your Friends Email Addresses:'); ?>
										<br>
										<?=$this->form->textarea('to', array(
											'class' => 'inputbox',
											'id' => 'recipient_list',
											'style' => "width:339px; height:100px;",
											'value' => 'Separate email addresses by commas',
											'onblur' => "if(this.value=='') this.value='Separate email addresses by commas';",
											'onfocus' => "if(this.value=='Separate email addresses by commas') this.value='';"
											)); ?>
										<br>
										<?=$this->form->label('Personalized Message To Friends:'); ?>
										<?=$this->form->textarea('message', array(
											'class' => 'inputbox',
											'id' => 'comments',
											'style' => "width:339px; height:100px",
											'value' => "Please accept this invitation to join Totsy",
											'onblur' => "if(this.value=='') this.value='Please accept this invitation to join Totsy';",
											'onfocus' => "if(this.value=='Please accept this invitation to join Totsy') this.value='';"
											)); ?>
										<br>
										<?=$this->form->submit('Send Invitations', array('class' => 'button fr')); ?>
									<?=$this->form->end(); ?>
									<br><br><br>
									
									
								</fieldset>
						</div>
						<div class="grid_5">
								<h2 class="gray mar-b">Share with your friends</h2>
								<hr />
								<div style="position:absolute; right:137px; top:-13px;"><?php echo $spinback_fb; ?></div>
								<h2 class="gray clear mar-b">Invite friends from your address book</h2>
								<hr />
								<a href="#" title="Invite friends from your Gmail contacts" id="invite-gmail" class="invite-btn fl">Gmail</a>
								<a href="#" title="Invite friends from your Yahoo! contacts" id="invite-yahoo" class="invite-btn fr">Yahoo!</a>

								<a href="#" title="Invite friends from your Outlook address book" id="invite-outlook" class="invite-btn fl">Outlook</a>
								<a href="#" title="Invite friends from your AOL contacts" id="invite-aol" class="invite-btn fr">AOL</a>

								<a href="#" title="Invite friends from your MSN address book" id="invite-msn" class="invite-btn fl">MSN</a>
								<a href="#" title="Invite friends" id="invite-others" class="invite-btn fr">Others</a>

						</div>

					</div>
					<!-- End Send Invitations Tab -->


					<!-- Start Open Invitations Tab -->
					<div id="openinvites" class="ui-tabs-hide">
						<?php if (!empty($open)): ?>
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
									<?php // $invites = $open->data() ?>
							<?php foreach ($open as $invite): ?>
									<tr class="alt<?php echo ( ($y++ % 2) == 1 ? 0 : 1); ?>">
										<td><?=$x?></td>
										<td><?=$invite->email; ?></td>
										<td><?=date('M-d-Y', $invite->date_sent->sec); ?></td>
									</tr>
								<?php $x++ ?>
							<?php endforeach ?>
								</tbody>
							</table>
						<?php else: ?>
							<strong>This feature is being migrated to the new site.</strong><br />
							We'll have your invitation data loaded soon.
						<?php endif ?>
					</div>
					<!-- End Open Invitations Tab -->

					<!-- Start Accepted Invitations Tab -->
					<div id="acceptedinvites" class="ui-tabs-hide">
						<?php if (!empty($accepted)): ?>
                        <span style="font-size:11px;">($15.00 credit will be applied for each friend making their 1st purchase)</span>
							<table cellpadding="0" cellspacing="0" border="0" width="100%" class="order-table">
								<thead>
									<tr>
										<th width="8%">#</th>
										<th width="85%">Invitations</th>
										<th width="7%">Status</th>
						          </tr>
								</thead>
							<?php $x = 1;?>
							<?php $y = 0;?>
								<tbody>
							<?php foreach ($accepted as $invite): ?>
									<tr class="alt<?php echo ( ($y++ % 2) == 1 ? 0 : 1); ?>">
										<td nowrap="nowrap"><?=$x?></td>
									  <td nowrap="nowrap"><?=$invite->email; ?></td>
										<td nowrap="nowrap"><span style="color:#090; font-size:12px; font-weight:bold; float:right;">Accepted!</span></td>
									</tr>
								<?php $x++ ?>
							<?php endforeach ?>
								</tbody>
							</table>
						<?php else: ?>
    <strong>This feature is being migrated to the new site.</strong><br>
							We have your invitation history and will have it loaded soon.
						<?php endif ?>

			    </div>
					<!-- End Accepted Invitations Tab -->

				</div>
	<br />

</div>
</div>
<div class="clear"></div>

<script type="text/javascript" charset="utf-8">

	$(document).ready( function() {
	
		$("#inviteForm").validationEngine('attach');        
    	$("#inviteForm").validationEngine('init', { promptPosition : "centerRight", scroll: false } );  
	
		$("input[type=Submit]").attr("disabled","disabled");
		
		var default_msg = "Separate email addresses by commas";
	
		function validateEmail(field) {
    		var regex=/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i;
    		return (regex.test(field)) ? true : false;
    	}			
		
		$("#recipient_list").blur( function() {
		
			var email_addresses = $("#recipient_list").val().split(",");
			
			if($("#recipient_list").val()!= default_msg){			
				if (typeof email_addresses == "object") {
					for ( email in email_addresses ) {
						var clean_email = email_addresses[email].replace(" ","");			
						
						if( validateEmail(clean_email)==false) {
							
							var error_msg = "";
							
								if(clean_email==""){
									error_msg = "*One of your emails is blank, or remove that extra comma.";
								} else {
									error_msg = '*This email is not valid: ' +  clean_email;
								}
							
							$('#recipient_list').validationEngine('showPrompt',error_msg, '', true);
    			 			$('#recipient_list').validationEngine({ promptPosition : "centerRight", scroll: false });
							
							$("input[type=Submit]").attr("disabled","disabled");
							return false;
						} else {
							$("#recipient_list").validationEngine('hide');
							$("input[type=Submit]").removeAttr("disabled");	
						}
					}				
				} else {
					if( validateEmail(email_addresses)==false) {
						
						$('#recipient_list').validationEngine('showPrompt','*This email is not valid:' +  email_addresses, '', true);
    			 		$('#recipient_list').validationEngine({ promptPosition : "centerRight", scroll: false });
						$("input[type=Submit]").attr("disabled","disabled");
						return false;
					} else {
						$("#recipient_list").validationEngine('hide');	
						$("input[type=Submit]").removeAttr("disabled");
					}
				}		
			} else {
				$('#recipient_list').validationEngine('showPrompt','*Please enter an email', '', true);
    			$('#recipient_list').validationEngine({ promptPosition : "centerRight", scroll: false });
				$("input[type=Submit]").attr("disabled","disabled");
				return false;	
			}
		});
	});
	
</script>
<script type="text/javascript" charset="utf-8">
	$('#invite-gmail, #invite-yahoo, #invite-outlook, #invite-aol, #invite-msn, #invite-others').click(function(){
		$('#recipient_list').val('');
		showPlaxoABChooser('recipient_list', '/pages/plaxo');
	})
</script>
<script type="text/javascript" src="http://www.plaxo.com/css/m/js/util.js"></script>
<script type="text/javascript" src="http://www.plaxo.com/css/m/js/basic.js"></script>
<script type="text/javascript" src="http://www.plaxo.com/css/m/js/abc_launcher.js"></script>