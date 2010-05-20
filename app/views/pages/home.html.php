<div id="container">
	<div id="topper"></div>
	<div id="wrapper">
		<div id="header">
			<div id="header-lt">
				<?=$this->html->link($this->html->image('logo.png', array('width'=>'155', 'height'=>'90')),'',array('id'=>'main-logo', 'escape'=>false));?>
			</div>
			<div id="header-mid">
				<?=$this->html->link('At Your Service','',array('id'=>'cs'));?>	
				<div id="welcome">
					<strong>Hello!</strong> (<?=$this->html->link('Sign Out',array('controller' => 'users','action'=>'logout'),array('title'=>'Sign Out'));?>	)				
				</div>
			</div>
			<div id="header-rt">
				<?=$this->html->link('Invite Friends. Get $15','',array('title'=>'Invite Friends. Get $15', 'id'=>'if'));?>	
				<p class="clear">
				<span class="fl">
					<?=$this->html->link('My Credit','',array('title'=>'My Credits', 'id'=>'credits'));?>($1,000)
					<?=$this->html->link('My Cart','',array('title'=>'My Cart', 'id'=>'cart'));?>				
				<span class="fl">(1) <?=$this->html->link('Checkout','',array('title'=>'Checkout', 'id'=>'checkout'));?></span>
				</span>
				</p>			
			</div>
		</div>
	</div>
</div>	