<div id="fullscreen">
	<div id="login-box">
		<div id="login-box-border">
			<div id="login-box-container">
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
						<li>Savings of up to 70% off retail.</li>
						<li>For every purchase, one tree is planted.</li>
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

<script>
$(document).ready(function () {
	var invitation_id = '<%= @customer.inviting_invitation_token_id %>';
	var customer_id = '<%= @customer.id %>';
	var cookieName = "K_18733";
	var img = '<img src="https://k.keyade.com/kaev/1/?kaPcId=18733&kaEvId=17105&kaEvMcId=';
	var params = '&kaEvCt1=1" width="2" height="2">';
	var nameEQ = cookieName + "=";
	var ca = document.cookie.split(';')
	for (var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0){
			if (invitation_id == '95069'){
				document.write( img + customer_id + '&kaClkId=' + c.substring(nameEQ.length,c.length) + params);
			} 
		}
	}
	setTimeout(function(){window.location = '/'}, 5000 );
});
</script>