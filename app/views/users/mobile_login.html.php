<style>
	.mobile { min-width:320px; text-align: center!important; margin:0px auto; padding:0px; }
	#mobileFooter { float: left; }
	#mobileFooter  ul, li { margin:0px; padding:0px; }
	#mobileFooter  li { float: left; }
	
	.error {background:#ff0000; color:#fff; font-size: 12px; }
	
	.legal { font-size:11px; text-align: center;}
	
	h1, h2, h3, h4, h5, h6 { margin:0px; padding:0px; } 
	h2 { font-size:18px; color:#000; margin-bottom: 10px;  }
	
	#mobileFooter { bottom:0; position: absolute; text-align: center!important;}
	#mobileFooter li { padding:10px;}
	
	.mobile-input {margin-left:2px; padding:5px; border:1px solid #ddd; font-size:14px; margin-bottom:10px;}
	.mobile-password {margin-left:0px; padding:5px; border:1px solid #ddd; font-size:14px;}
</style>
<?php if ($message){ echo $message; } ?>
<div class="mobile">
	<h2>Sign In</h2>
	<?=$this->form->create(null,array('id'=>'loginForm'));?>
	<input type="input" class="validate['required'] input-text mobile-input" id="email" />
	<br />
	<input type="password" class="validate['required'] input-text mobile-password" id="password" />
	<br />
	<?=$this->form->submit('Login', array('class' => "button", "style" => "text-align:center; margin:10px 0px 10px 187px;"));?> 
	<?=$this->form->end();?>
	<div style="clear:both;"></div>
	<div id="fb-root"></div>
			<script src="http://connect.facebook.net/en_US/all.js"></script>
			<script>
				FB.init({ 
				appId:'130085027045086', cookie:true, 
				status:true, xfbml:true, oauth: true
				});
			</script>
		<!-- <fb:login-button>Login with Facebook</fb:login-button> -->
		<a href="http://www.facebook.com/dialog/oauth?client_id=130085027045086&redirect_uri=http://eric.totsy.com&display=touch"><img src="http://www.totsy.com/img/fb_login_btn.png" /></a>
		
		<p style='margin-top: 10px'> <?=$this->html->link('Forgot your password?','/reset', array('class'=>"md", 'title'=>"Forgot your password?", 'style' => 'margin-bottom:5px;'))?> </p>
	
	<div id="mobileFooter">
		<ul>
			<li><a href="/terms">Terms</a></li>
			<li style="float:right;"><a href="/privacy_policy">Privacy Policy</a></li>
		</ul>
	</div>
	<div class="legal">&copy; 2011 Totsy, Inc. All Rights Reserved.</div>
	</div>
