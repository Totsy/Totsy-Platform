<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title><?php echo $this->title(); ?></title>
	<?php echo $this->html->style(array('base'), array('media' => 'screen')); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-675412-15']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</head>

<?php
	$mainMenu = $this->menu->render('main-nav');
	$bottomMenu = $this->menu->render('bottom', array('ul' => array('class' => 'menu')));
?>
	<body> 
	
		<div id="topper"><!-- --></div> 
 
		<div id="wrapper"> 
			
			<!-- Start Header --> 
			<div id="header"> 
				
				<div id="header-lt"> 
				
					<?php echo $this->html->link(
						$this->html->image('logo.png', array('width'=>'155', 'height'=>'90')), '', array(
							'id' => 'main-logo', 'escape'=> false
						)
					); ?>
				
				</div> 
				
				<div id="header-mid"> 
					
					<a href="#" id="cs" title="Customer Service">At Your Service</a> 
					
					<div id="welcome"> 
						<strong>Hello</strong> Mitch Pirtle! (<a href="#" title="Sign Out">Sign Out</a>)
					</div> 
					
					<!-- Start Main Navigation --> 
					<div id="main-nav"> 
						<ul class="menu main-nav"> 
							
							<li class="active first item2"> 
								<a href="#" title="Sales"><span>Sales</span></a> 
							</li> 
							
							<li class="item3"> 
								<a href="#" title="Coming Soon"><span>Coming Soon</span></a> 
							</li> 
							
							<li class="item4"> 
								<a href="#" title="Blog"><span>Blog</span></a> 
							</li> 
							
							<li class="last parent item5"> 
								<a href="#" title="My Account"><span>My Account</span></a> 
								<ul> 
									<li class=" first item17"> 
										<a href="#" title="Account Dashboard"><span>Account Dashboard</span></a> 
									</li> 
									<li class="item18"> 
										<a href="#" title="Account Information"><span>Account Information</span></a> 
									</li> 
									<li class="item19"> 
										<a href="#" title="Address Book"><span>Address Book</span></a></li> 
									<li class="item20"> 
										<a href="#" title="My Orders"><span>My Orders</span></a> 
									</li> 
									<li class="item21"> 
										<a href="#" title="Email Preferences"><span>Email Preferences</span></a> 
									</li> 
									<li class="item22"> 
										<a href="#" title="Help Desk"><span>Help Desk</span></a> 
									</li> 
									<li class=" last item23"> 
										<a href="#" title="My Invitations"><span>My Invitations</span></a> 
									</li> 
								</ul> 
								
							</li> 
							
						</ul> 
					</div> 
					<!-- End Main Navigation --> 
					
				</div> 
				
				<div id="header-rt"> 
					
					<a href="#" id="if" title="Invite Friends. Get $15">Invite Friends. Get $15</a> 
					
					<p class="clear"> 
						<span class="fl"><a href="#" id="credits" title="My Credits">My Credits</a> ($1,000)</span> <a href="#" id="cart" title="My Cart">Cart</a> <span class="fl">(1) <a href="#" id="checkout" title="Checkout">Checkout</a></span> 
					</p> 
					
				</div> 
				
			</div> 
			<!-- End Header --> 
			
			<div id="content"> 
				<?php echo $this->content(); ?>
			</div> 
			
		</div> 
		
		<div id="botter"><!-- --></div> 
		
		<div id="footer"> 
		
			<ul class="menu"> 
			
				<li class="first"> 
					<a href="#" title="Terms of Use"><span>Terms of Use</span></a> 
				</li> 
				<li> 
					<a href="#" title="Privacy Policy"><span>Privacy Policy</span></a> 
				</li> 
				<li> 
					<a href="#" title="Sitemap"><span>Sitemap</span></a> 
				</li> 
				<li> 
					<a href="#" title="About Us"><span>About Us</span></a> 
				</li> 
				<li> 
					<a href="#" title="FAQ"><span>FAQ</span></a> 
				</li> 
				<li class="last"> 
					<a href="#" title="Contact Us"><span>Contact Us</span></a> 
				</li> 
				
			</ul> 
			
			<p class="clear">&copy; 2011 Totsy.com. All Rights Reserved.</p> 
		
		</div> 
		
		<script type="text/javascript" src="../js/jquery-1.4.2.js"></script> 
		<script type="text/javascript" src="../js/jquery.equalheights.js"></script> 
		<script type="text/javascript" src="../js/jquery-ui-1.8.2.custom.min.js"></script> 
		
		<script type="text/javascript"> 
			$(document).ready(function() {
				$("#tabs").tabs();
			});
		</script> 
				
	</body> 
	
</html> 