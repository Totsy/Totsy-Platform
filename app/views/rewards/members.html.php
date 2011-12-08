<?php 
$this->title("Rewards Members"); 
?>

<script type="text/javascript">

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

<script src="https://d3aa0ztdn3oibi.cloudfront.net/javascripts/ff.loyalty.widget.js" type="text/javascript"></script> 

<div id="rewards-members-app" class="grid_12">
<iframe id="ff_member_iframe" style="width:100%;height:1045px;border:0"></iframe> 
</div>
<div class="clear"></div>

<script type="text/javascript">
	_ffLoyalty.initialize("<?=$params['uuid']?>");
	_ffLoyalty.loadIframe({email:"<?=$userInfo['email']?>", auth_token: "<?=$authToken?>"}); 
</script>