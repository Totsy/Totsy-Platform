<?php use lithium\storage\Session; ?>
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

<?php
	$inviteBlurb = "For each friend you invite, Totsy will credit your account with <span style='color:#009900;'>$15</span> after your friend's place their first order.";
	$inviteMsg = "Please accept this invitation to join Totsy";
	
	if (Session::read("layout", array("name"=>"default"))=="mamapedia") {
		$inviteBlurb = "For each friend you invite, we will credit your account with <span style='color:#009900;'>$15</span> after your friend's place their first order.";
		$inviteMsg = "Please accept this invitation to join Mamasource Private Sales powered by Totsy";
	} 
?>

<?php if (is_object($user->invitation_codes)): ?>
	<?php foreach ($user->invitation_codes as $code): ?>
	    <?php $invite = "http://".$_SERVER['HTTP_HOST']."/join/" . $code; ?>
	<?php endforeach ?>
<?php else: ?>
	<?php $invite = "http://".$_SERVER['HTTP_HOST']."/join/" . $user->invitation_codes;?>
<?php endif ?>

	<h2 class="page-title gray">My Invitations <span class="fr" style="font-size:12px; margin-top:-10px;">Share this link with your friends: <br><span style="word-wrap:break-word;"><strong><a href="<?php echo $invite?>" title="Your Invite Link"><?php echo $invite?></a></strong></span></span></h2>
	<hr />
		<div id="tabs">
			<?php if (!empty($flashMessage)): ?>
				<div class='standard-message'><strong><?php echo $flashMessage?></strong></div>
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
					<p><?php echo $inviteBlurb; ?></p>
						<fieldset>
							<br>
							<?php echo $this->form->create( "", array("id"=>"inviteForm") ); ?>
								<?php echo $this->form->label('Enter Your Friends Email Addresses:'); ?>
								<br>
								<?php echo $this->form->textarea('to', array(
									'class' => 'inputbox',
									'id' => 'contact_list',
									'style' => "width:339px; height:100px;",
									'value' => 'Separate email addresses by commas',
									'onblur' => "if(this.value=='') this.value='Separate email addresses by commas';",
									'onfocus' => "if(this.value=='Separate email addresses by commas') this.value='';"
									)); ?>
								<br>
								<?php echo $this->form->label('Personalized Message To Friends:'); ?>
								<?php echo $this->form->textarea('message', array(
									'class' => 'inputbox',
									'id' => 'comments',
									'style' => "width:339px; height:100px",
									'value' => $inviteMsg,
									'onblur' => "if(this.value=='') this.value='Please accept this invitation to join Totsy';",
									'onfocus' => "if(this.value=='Please accept this invitation to join Totsy') this.value='';"
									)); ?>
								<br>
								<?php echo $this->form->submit('Send Invitations', array('class' => 'button fr')); ?>
							<?php echo $this->form->end(); ?>
							<br><br><br>
							
							
						</fieldset>
				</div>
				<div class="grid_5">
						<h2 class="gray mar-b">Share with your friends</h2>
						<hr />
						<div style="position:absolute; right:137px; top:-13px;"><?php echo $spinback_fb; ?></div>
						<h2 class="gray clear mar-b">Invite friends from your address book</h2>
						<hr />
						<a href="#" title="Invite friends from your Gmail contacts" id="invite-gmail" class="invite-btn fl cs_import">Gmail</a>
						<a href="#" title="Invite friends from your Yahoo! contacts" id="invite-yahoo" class="invite-btn fr cs_import">Yahoo!</a>

						<a href="#" title="Invite friends from your Outlook address book" id="invite-outlook" class="invite-btn fl cs_import">Outlook</a>
						<a href="#" title="Invite friends from your AOL contacts" id="invite-aol" class="invite-btn fr cs_import">AOL</a>

						<a href="#" title="Invite friends from your MSN address book" id="invite-msn" class="invite-btn fl cs_import">MSN</a>
						<a href="#" title="Invite friends" id="invite-others" class="invite-btn fr cs_import">Others</a>

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
								<td><?php echo $x?></td>
								<td><?php echo $invite->email; ?></td>
								<td><?php echo date('M-d-Y', $invite->date_sent->sec); ?></td>
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
								<td nowrap="nowrap"><?php echo $x?></td>
							  <td nowrap="nowrap"><?php echo $invite->email; ?></td>
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
		
		var default_msg = "Separate email addresses by commas";
	
		function validateEmail(field) {
			
			var begin = field.indexOf("<");
			var end = field.indexOf(">");
			if((begin != -1) && (end != -1)) {
				field = field.slice(begin+1,end);
			}
    		var regex=/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i;
    		return (regex.test(field)) ? true : false;
    	}
		
		var checkFields = function() {
		
			var email_addresses = $("#contact_list").val().split(",");
			
			if($("#contact_list").val()!= default_msg){			
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
							
							$('#contact_list').validationEngine('showPrompt',error_msg, '', true);
    			 			$('#contact_list').validationEngine({ promptPosition : "centerRight", scroll: false });
							return false;
						} else {
							$("#contact_list").validationEngine('hide');
						}
					}				
				} else {
					if( validateEmail(email_addresses)==false) {
						
						$('#contact_list').validationEngine('showPrompt','*This email is not valid:' +  email_addresses, '', true);
    			 		$('#contact_list').validationEngine({ promptPosition : "centerRight", scroll: false });
						return false;
					} else {
						$("#contact_list").validationEngine('hide');	
					}
				}		
			} else {
				$('#contact_list').validationEngine('showPrompt','*Please enter an email', '', true);
    			$('#contact_list').validationEngine({ promptPosition : "centerRight", scroll: false });
				return false;	
			}
		}
		$("#inviteForm").submit(function() {
			if(checkFields() == false){
				return false;	
			} 
		});	
	});
</script>
<script type="text/javascript" src="https://api.cloudsponge.com/address_books.js"></script>
<script type="text/javascript" charset="utf-8">
csInit({domain_key:"ZSSSM5GHM6C8S7Q5TEEG", textarea_id:'contact_list'});
</script>