<?php echo $this->html->script('FusionCharts.js')?>
<div class="grid_16">
	<h2 id="page-heading">Reports - Services</h2>
</div>
<div class="clear"></div>
<h2>Charts 1st purchase (Free Shipping)</h2>
<?php echo $ServiceCharts->renderChart()?>
<h2>Charts 2nd purchase ($10 Off, purchase of $50+)</h2>
<?php echo $Service2ndCharts->renderChart()?>