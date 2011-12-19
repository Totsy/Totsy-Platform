<div id="fullscreen">

	<div id="login-box">

		<div id="login-box-border" class="register-modal">

			<div id="login-box-container">
				
				<div class="tt">
					<div></div>
				</div>			
				
				<div class="tm">
					<div class="ti">		
					
						<div class="tc login-inner">
				
							<h1 id="logo">Totsy</h1>
							<div id="intro-copy">
								<h2 style="margin-top:30px"><span>Welcome to Totsy!!!</span></h2>
							</div>
							<div class="r-container clear reg-list">
							<div class="tl"></div>
							<div class="tr"></div>
							
							<div class="r-box lt-gradient-1">
								<strong class="red">Why you will love Totsy</strong>
								<ul class="bugs columns-2">
									<li>Exclusive sales for moms, children &amp; babies.</li>
									<li>Sales last up to 3 days, plenty of time to shop.</li>
									<li>Savings of up to 90% off retail.</li>
									<li>A tree is planted for your first purchase.</li>
									<li>Membership is free</li>
									<li>We are 100% green.</li>
								</ul>
							</div>
							
							<div class="bl"></div>
							<div class="br"></div>
							
							</div>
							<br><br><br>
							<center><p><strong class='red'>Thank you for registering with Totsy.<br>
								In a few seconds you will be redirected to see all our latest sales.
							</strong></p></center>
						
						</div>
					</div>
				</div>
				
				<div class="tb">
					<div></div>
				</div>
				
			</div>

		</div>
	</div>
</div>

<div id="footer">
	<?php echo $this->view()->render(array('element' => 'footerNavPublic')); ?>
</div>

<div id="imagepix"></div>
<script type="text/javascript">
$(document).ready(function () {
	var customer_id = '<?php echo $user->_id;?>';
	var cookieName = "K_18733";
	var img = '<img src="https://k.keyade.com/kaev/1/?kaPcId=18733&kaEvId=17105&kaEvMcId=';
	var params = '&kaEvCt1=1" width="2" height="2">';
	var nameEQ = cookieName + "=";
	var ca = document.cookie.split(';')
	for (var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0){
			$('#imagepix').html( img + customer_id + '&kaClkId=' + c.substring(nameEQ.length,c.length) + params);
		}
	}
	setTimeout(function(){window.location = '/'}, 5000 );
});
</script>

<div id="footer">
	<?php echo $this->view()->render(array('element' => 'footerNavPublic')); ?>
</div>

<!-- Google Code for Homepage Remarketing List -->
<script type="text/javascript">
/* <![CDATA[ */
	var google_conversion_id = 1019183989;
	var google_conversion_language = "en";
	var google_conversion_format = "3";
	var google_conversion_color = "666666";
	var google_conversion_label = "8xkfCIH8iwIQ9Yb-5QM";
	var google_conversion_value = 0;
/* ]]> */
</script>

<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js"></script>

<noscript>
	<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1019183989/?label=8xkfCIH8iwIQ9Yb-5QM&amp;guid=ON&amp;script=0"/>
	</div>
</noscript>
<!-- END OF Google Code for Homepage Remarketing List -->
