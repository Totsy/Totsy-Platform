<?php use lithium\net\http\Router; ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?=$this->html->charset();?>
	<title>
		<?=$this->title() ?: 'Totsy, the private sale site for Moms'; ?>
		<?=$this->title() ? '- Totsy' : ''; ?>
	</title>
	<?=$this->html->style(array('base.css', '960.css', 'jquery_ui_custom/jquery.ui.all.css'), array('media' => 'screen')); ?>
	
	<script src="http://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", {uncompressed:false});</script>
	<script> google.load("jqueryui", "1.8.13", {uncompressed:false});</script>
            <!-- end jQuery / jQuery UI -->
            
    <?=$this->html->script(array('jquery.countdown.min.js', 'jquery.uniform.min.js' )); ?>
    
	<?=$this->scripts(); ?>
	
	<?=$this->html->link('Icon', null, array('type' => 'icon')); ?>
	<meta property="og:site_name" content="Totsy"/>
	<meta property="fb:app_id" content="181445585225391"/>
    <meta name="description"
          content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>
          
</head>
<body class="app">
	
	<div class="container_16 roundy glow">
		<div class="grid_3 alpha" style="margin:5px 0px 0px 5px;">
				<?=$this->html->link($this->html->image('logo.png', array('width'=>'120')), '/sales', array('escape'=> false)); ?>
			</div>

			<div class="menu_top_left">
			<?php if (!empty($userInfo)): ?>
			Hello, <?php if(array_key_exists('firstname',$userInfo) && !empty($userInfo['firstname'])):

					?>
						<?="{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
					<?php else:?>
						<?="{$userInfo['email']}"; ?>
					<?php endif; ?>
					<?php $logout = ($fblogout) ? $fblogout : 'Users::logout' ?>
					(<?=$this->html->link('Sign Out', $logout, array('title' => 'Sign Out')); ?>) 
						
				<?php endif ?>
			</div>
			
			<div class="menu_top_right">
			<?php if (!(empty($userInfo))) { ?>
				<a href="/account" title="My Account">My Account</a>
				<a href="/account/credits" title="My Credits $<?=$credit?>">My Credits $<?=$credit?></a>
				<a href="/cart/view" class="cart_icon" title="My Cart (<?=$cartCount;?>)">My Cart (<?=$cartCount;?>)</a>
				<a href="/users/invite" title="+ Invite Friends Get $15">+ Invite Friends Get $15</a>
			<?php } else { ?>
				<span style="text-align:right!important;">
					<a href="/" title="Sign In">Sign In</a> 
					<a href="/register" title="Sign Up">Sign Up</a>
					<a href="/users/invite" title="+ Invite Friends Get $15">+ Invite Friends Get $15</a>
				</span>
			<?php } ?>
			</div>
			<div class="menu_main_global">
			<?php if (!(empty($userInfo))): ?>
				<ul class="nav main" id="navlist">
					<li><a href="/sales" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales') == 0 || $_SERVER['REQUEST_URI'] == '/') {
						echo 'class="active"';
					} ?>>All Sales</a></li>
					<li><a href="/sales/girls" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/girls') == 0) {
						echo 'class="active"';
					} ?>>Girls</a></li>
					<li><a href="/sales/boys" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/boys') == 0)  {
						echo 'class="active"';
					} ?>>Boys</a></li>
					<li><a href="/sales/momsdads" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/momsdads') == 0) {
						echo 'class="active"';
					} ?>>Moms &amp; Dads</a></li>
				</ul>
			<?php endif ?>
		</div>
		<!-- end header -->
<div class="clear"></div>
		<?php echo $this->content(); ?>
		<!-- main content -->

	</div>
	
	<div class="clear"></div>
	<div id="footer" class="container_16">
		<ul>
			<li class="first" style="padding-top:4px;"><a href="/pages/terms" title="Terms of Use">Terms of Use</a></li>
			<li style="padding-top:4px;"><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li style="padding-top:4px;"><a href="/pages/aboutus" title="About Us">About Us</a></li>
			<li style="padding-top:4px;"><a href="http://blog.totsy.com" title="Blog" target="_blank">Blog</a></li>
			<li style="padding-top:4px;"><a href="/pages/faq" title="FAQ">FAQ</a></li>
			<li style="padding-top:4px;"><a href="/pages/affiliates" title="Affiliates">Affiliates</a></li>
			
			<! -- switch where this link points depending on whether they're logged in or not --> 
			<?php if (empty($userInfo)){ ?>
			<li class="last" style="padding-top:4px;"><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
			<?php } else { ?>
			<li class="last" style="padding-top:4px;"><a href="/tickets/add" title="Contact Us">Contact Us</a></li>
			<?php } ?>
			<li class="last" style="margin:0px 3px 0px 5px;"><a href="http://www.facebook.com/totsyfan" target="_blank"><img src="../img/icons/facebook_16.png" align="middle" /></a></li>
			<li class="last"><a href="http://twitter.com/MyTotsy" target="_blank"><img src="../img/icons/twitter_16.png" align="middle" /></a></li>
		</ul>
		<span id="copyright" style="padding-top:4px;" class="fl">&copy;2011 Totsy.com. All Rights Reserved.</span>
	</div>
	<div class="container_16 clear">
	<!-- begin thawte seal -->
    <div id="thawteseal" title="Click to Verify - This site chose Thawte SSL for secure e-commerce and confidential communications.">
    <div style="float: left!important; width:100px; display:block;"><script type="text/javascript" src="https://seal.thawte.com/getthawteseal?host_name=www.totsy.com&amp;size=L&amp;lang=en"></script></div>

    <div class="AuthorizeNetSeal" style="float: left!important; width:100px; display:block;"> <script type="text/javascript" language="javascript">var ANS_customer_id="98c2dcdf-499f-415d-9743-ca19c7d4381d";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script></div>
    </div>
    <!-- end thawte seal -->
    </div>
	<script type="text/javascript">
	$.base = '<?=rtrim(Router::match("/", $this->_request)); ?>';
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-675412-15']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>

    <script type="text/javascript">
			$(function () {
				$(window).scroll(function () {
					if ($(this).scrollTop() != 0) {
						$('#toTop').fadeIn();
					} else {
						$('#toTop').fadeOut();
					}
				});
				$('#toTop').click(function () {
					$('body,html').animate({
						scrollTop: 0
					},
					800);
				});
			});
	</script>

    <div id='toTop'>^ Back to Top</div>

    <!--affiliate pixels-->
    <?php echo $pixel; ?>
   <script>
	// date picker
	$('#date').datepicker();
	
	// radio buttons
	$(function() {
		$( "#radio" ).buttonset();
	});
	
	 // form buttons
	 $(function() {
		$( "button, input:submit, a", ".demo" ).button();
		$( "a", ".demo" ).click(function() { return false; });
	});
	
	// checkboxes
	$(function() {
		$( "#check" ).button();
		$( "#format" ).buttonset();
	});
	
	// buttons with icons
	$(function() {
		$( ".demo button:first" ).button({
            icons: {
                primary: "ui-icon-locked"
            },
            text: false
        }).next().button({
            icons: {
                primary: "ui-icon-locked"
            }
        }).next().button({
            icons: {
                primary: "ui-icon-gear",
                secondary: "ui-icon-triangle-1-s"
            }
        }).next().button({
            icons: {
                primary: "ui-icon-gear",
                secondary: "ui-icon-triangle-1-s"
            },
            text: false
        });
	});
	
	// split buttons
	$(function() {
		$( "#rerun" )
			.button()
			.click(function() {
				alert( "Running the last action" );
			})
			.next()
				.button( {
					text: false,
					icons: {
						primary: "ui-icon-triangle-1-s"
					}
				})
				.click(function() {
					alert( "Could display a menu to select an action" );
				})
				.parent()
					.buttonset();
	});
	
	// tabs
	$(function() {
		$( "#tabs" ).tabs();
	});
	
	// accordian with icons
	$(function() {
		var icons = {
			header: "ui-icon-circle-arrow-e",
			headerSelected: "ui-icon-circle-arrow-s"
		};
		$( "#accordion" ).accordion({
			icons: icons
		});
		$( "#toggle" ).button().toggle(function() {
			$( "#accordion" ).accordion( "option", "icons", false );
		}, function() {
			$( "#accordion" ).accordion( "option", "icons", icons );
		});
	});
	
	// progress bar
	$(function() {
		$( "#progressbar" ).progressbar({
			value: 59
		});
	});
	
	// uniform inputs
	$("input:file,select").uniform();
	
	
	// slider
	$(function() {
		$( "#slider-range-min" ).slider({
			range: "min",
			value: 37,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( "#amount" ).val( "$" + ui.value );
			}
		});
		$( "#amount" ).val( "$" + $( "#slider-range-min" ).slider( "value" ) );
	});
	
	// dialog
	$(function() {
		// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
		
		var name = $( "#name" ),
			email = $( "#email" ),
			password = $( "#password" ),
			allFields = $( [] ).add( name ).add( email ).add( password ),
			tips = $( ".validateTips" );

		function updateTips( t ) {
			tips
				.text( t )
				.addClass( "ui-state-highlight" );
			setTimeout(function() {
				tips.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}

		function checkLength( o, n, min, max ) {
			if ( o.val().length > max || o.val().length < min ) {
				o.addClass( "ui-state-error" );
				updateTips( "Length of " + n + " must be between " +
					min + " and " + max + "." );
				return false;
			} else {
				return true;
			}
		}

		function checkRegexp( o, regexp, n ) {
			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass( "ui-state-error" );
				updateTips( n );
				return false;
			} else {
				return true;
			}
		}
		
		$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons: {
				"Create an account": function() {
					var bValid = true;
					allFields.removeClass( "ui-state-error" );

					bValid = bValid && checkLength( name, "username", 3, 16 );
					bValid = bValid && checkLength( email, "email", 6, 80 );
					bValid = bValid && checkLength( password, "password", 5, 16 );

					bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
					// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
					bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
					bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );

					if ( bValid ) {
						$( "#users tbody" ).append( "<tr>" +
							"<td>" + name.val() + "</td>" + 
							"<td>" + email.val() + "</td>" + 
							"<td>" + password.val() + "</td>" +
						"</tr>" ); 
						$( this ).dialog( "close" );
					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});

		$( "#create-user" )
			.button()
			.click(function() {
				$( "#dialog-form" ).dialog( "open" );
			});
	});
		
</script>
	</body> 
</html>