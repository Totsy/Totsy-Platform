<?php
	use app\models\Menu;

	$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
	echo $this->MenuList->build($menu, $options);
?>

<h1 class="p-header">Press</h1> 
			
<div class="tl"><!-- --></div> 
<div class="tr"><!-- --></div> 

<div id="page">
	
	
	
	<p>Totsy is the leading website in private sales for moms. We are also the first company of its kind to be <?=$this->html->link('100% green', array('Pages::being_green')); ?>. More than just exclusive savings, we are a hub of information, expert advice, and quality products perfect for mom, baby, and child.</p>
	
	<p>For media inquiries, please contact us at <a href="mailto:press@totsy.com">press@totsy.com</a>.</p>
	
	
</div> 
<div class="bl"><!-- --></div> 
<div class="br"><!-- --></div> 

		</div> 

	</div> 

</div>