<?=$this->html->script('mootools-1.2.4-core-nc.js');?>
<?=$this->html->script('overlay.js');?>
<?=$this->html->script('Assets.js');?>
<?=$this->html->script('multibox.js');?>
<?=$this->html->style('multibox');?>
<?php
	use app\extensions\helper\Menu;
	use app\models\Navigation;

	$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
	$leftMenu = Menu::build($navigation, $options);

	echo $leftMenu;

?>

<script>
window.addEvent('domready', function(){ 
 	var overlay; 
    var box = new multiBox('mb', { 
		overlay: overlay,
		showControls: false
		
    }); 

	 
});
</script>

<h1 class="p-header">My Account</h1>
<div class="tl"></div>
<div class="tr"></div>
<div id="page">

	<!-- Replace with user's name -->
	<strong>Hello <?=$data['firstname']?></strong>
	
	<!-- Replace with account welcome message -->
	<p>From your My Account Dashboard you have the ability to view a snapshot of your recent account activity and update your account information. Select a link below to view or edit information.</p>
	
	<h2 class="gray"><?php echo ('Account Information');?></h2>
	
	<div class="col-2">
	
		<div class="r-container box-2 fl">
			<div class="tl"></div>
			<div class="tr"></div>
			<div class="r-box lt-gradient-1">
				<h3 class="gray fl"><?php echo ('Contact Information');?></h3>&nbsp;|&nbsp;<?=$this->html->link('Edit Info', '/account/info', array('class'=> 'mb', 'rel' => "width:550, height:600"));?>
				<br />
				<br />
				<?=$data['firstname'].' '.$data['lastname'] ?><br />
				<?=$data['email']?><br />
				<a href="#" title="<?php echo ('Change Password');?>"><?php echo ('Change Password');?></a>
			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>
		
		<div class="r-container box-2 fr">
			<div class="tl"></div>
			<div class="tr"></div>
			<div class="r-box lt-gradient-1">
				<h3 class="gray fl"><?php echo ('Newsletter');?></h3>&nbsp;|&nbsp;<?=$this->html->link('Edit', '/account/news', array('class'=> 'mb'));?>
				<br />
				<br />
				<dl>
					<dt>You are currently subscribed to:</dt>
					<dd>
						<ul>
							<li>General Subscription</li>
						</ul>
					</dd>
				</dl>
				
			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>
	
	</div>
	
	<h2 class="gray fl"><?php echo ('Address Book');?></h2>&nbsp;|&nbsp;<?=$this->html->link('Manage Addresses', '/account/addresses');?>
	
	<div class="col-2">
	
		<div class="r-container box-2 fl">
			<div class="tl"></div>
			<div class="tr"></div>
			<div class="r-box lt-gradient-1">
				<h3 class="gray fl"><?php echo ('Primary Billing Address');?></h3>&nbsp;|&nbsp;<?=$this->html->link('Edit Address', '/account/edit');?>
				<br />
				<br />
				<address>
					123 Main Street<br />
					Apt. 456<br />
					Anytown, ST 90210 USA
				</address>
			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>
		
		<div class="r-container box-2 fr">
			<div class="tl"></div>
			<div class="tr"></div>
			<div class="r-box lt-gradient-1">
				<h3 class="gray fl"><?php echo ('Primary Shipping Address');?></h3>&nbsp;|&nbsp;<?=$this->html->link('Edit Address', '/account/edit', array('class'=> 'mb'));?>
				<br />
				<br />
				<address>
					567 Front Street<br />
					8th Floor<br />
					Suite 409<br />
					Mytown, CA 86753 USA
				</address>
			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>
	
	</div>

</div>
<div class="bl"></div>
<div class="br"></div>
<?=$this->html->link('Register', '/register', array('class'=> 'mb', 'rel' => "width:1000, height:1000"));?>