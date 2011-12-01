<?php $this->title("Rewards Members"); ?>
<script>

$(document).ready( function() {
	$("#reward-members-menu-items span a").click( function() {
		if($(this).attr("class")!="roundy nav-roundy") {
			$(this).attr("class", "roundy nav-roundy");
		} else {
			$(this).attr("class", "");	
		} 
	});
});

</script>

<div class="grid_16">
	<h2 class="page-title gray">Account Dashboard</h2>
	<hr />
</div>
<div class="grid_4">
	<?php echo $this->view()->render( array('element' => 'myAccountNav')); ?>
	<?php echo $this->view()->render( array('element' => 'helpNav')); ?>
</div>
<div id="rewards-members-app" class="grid_12">
	<div id="rewards-members-welcome">
		<span style="float:left">Welcome Back Evan!</span>
		<span style="float:right">Points balance:230</span>
	</div>
	<div id="reward-members-menu" class="roundy">
		<div id="reward-members-menu-title" class="roundy">
			<span>Totsy Rewards&trade;</span>
		</div>
		<div class="clear"></div>
		<div id="reward-members-menu-items">
			<span><a href="#">Rewards</a></span>
			<span><a href="#">Deals</a></span>
			<span><a href="#">Badges</a></span>
			<span><a href="#">Activity</a></span>
			<span><a href="#">Overview</a></span>
			<span><a href="#">Account</a></span>
		</div>
	</div>
</div>
<div class="clear"></div>