<style>
.cs_import { margin:0px 10px;}

#invites h2 {
	color: #777;
	font-weight: bold;
	font-size: 20px;
}

</style>

<?php $this->title("My Invitations"); ?>

<link rel="stylesheet" type="text/css" href="/css/validation-engine.jquery.css" media="screen" />
<link rel="stylesheet" type="text/css" href="/css/validation-template.css" media="screen" />
<script type="text/javascript" src="/js/form_validator/jquery.validation-engine.js" charset="utf-8"></script>    
<script type="text/javascript" src="/js/form_validator/languages/jquery.validation-engine-en.js" charset="utf-8"></script>

<?php if (is_object($user->invitation_codes)): ?>
	<?php foreach ($user->invitation_codes as $code): ?>
		<?php $invite = "http://www.totsy.com/join/" . $code;?>
	<?php endforeach ?>
<?php else: ?>
	<?php $invite = "http://www.totsy.com/join/" . $user->invitation_codes;?>
<?php endif ?>

			<?php if (!empty($flashMessage)): ?>
				<div class='standard-message'><strong><?=$flashMessage?></strong></div>
				<br>
			<?php endif ?>
				<h2>Dress your kids for free with Totsy!<br />Invite friends &amp; get $15 credits in your account!*</h2>
				<div class="clear"></div>
				<div style="width:255px; float:left; margin:25px 10px 0px 10px;">
				<hr style="margin:0 0px 7px"/>
				<h2 style="color:#999999; font-size:16px; font-weight:normal;">Send Invitations</h2>
					
						<fieldset>
							<br>
							<?=$this->form->create( "", array('url' => 'Users::invite', "id"=>"inviteForm")); ?>
								<?//=$this->form->label('Enter Your Friends Email Addresses:'); ?>
								<br>
								<?=$this->form->textarea('to', array(
									'class' => 'inputbox',
									'id' => 'contact_list',
									'style' => "width:243px; height:70px;",
									'value' => 'Separate email addresses by commas',
									'onblur' => "if(this.value=='') this.value='Separate email addresses by commas';",
									'onfocus' => "if(this.value=='Separate email addresses by commas') this.value='';"
									)); ?>
								<br>
								<?//=$this->form->label('Personalized Message To Friends:'); ?><br />
								<?=$this->form->textarea('message', array(
									'class' => 'inputbox',
									'id' => 'comments',
									'style' => "width:243px; height:70px",
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
						<div style="width:451px; float:left; margin:25px 0px 0px 10px;">
						<hr style="margin:0 7px 7px 0px"/>
						<h2 style="color:#999999; font-size:16px; font-weight:normal;">Invite friends from your address book</h2>
					
						<a href="#" title="Invite friends from your Gmail contacts" id="invite-gmail" class="fl cs_import" style="margin:38px 25px 10px 0px;"><img src="/img/Invitation-page1-V3_03.jpg"/></a>
						<a href="#" title="Invite friends from your Yahoo! contacts" id="invite-yahoo" class="fl cs_import" style="margin:38px 25px 10px 0px;"><img src="/img/Invitation-page1-V3_05.jpg"/></a>

						<a href="#" title="Invite friends from your Outlook address book" id="invite-outlook" class="fl cs_import" style="margin:38px 0px 10px 0px;"><img src="/img/Invitation-page1-V3_07.jpg"/></a><br />
						<a href="#" title="Invite friends from your AOL contacts" id="invite-aol" class="fl cs_import" style="margin:0px 25px 10px 0px;"><img src="/img/Invitation-page1-V3_12.jpg"/></a>

						<a href="#" title="Invite friends from your MSN address book" id="invite-msn" class="fl cs_import" style="margin:0px 25px 0px 0px;"><img src="/img/Invitation-page1-V3_13.jpg"/></a>
						<a href="#" title="Invite friends" id="invite-others" class="fl cs_import" style="margin:0px 0px 10px 0px;"><img src="/img/Invitation-page1-V3_14.jpg"/></a>	
						<a href="#" title="Invite friends from your MSN address book" id="invite-msn" class="fl cs_import" style="margin:0px 25px 10px 0px;"><img src="/img/Invitation-page1-V3_19.jpg"/></a>
						<a href="#" title="Invite friends" id="invite-others" class="fl cs_import" style="margin:0px 25px 0px 0px;"><img src="/img/Invitation-page1-V3_20.jpg"/></a>
						</div>

			<!-- End Send Invitations Tab -->
<div class="clear"></div>
<p style="font-size:9px; color:#999999; font-weight:normal; float:right; margin-right:10px;">*For each friend you invite, Totsy will credit your account with $15 after your friend's first order is shipped.</p>
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