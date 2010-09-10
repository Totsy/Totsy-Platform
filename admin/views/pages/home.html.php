
			<div class="grid_16">
				<h2 id="page-heading">Template Examples for Rapid Prototyping in Lithium</h2>
			</div>
			
			<div class="clear"></div>
			
			<div class="grid_4">
				<?php echo $this->view()->render(array('element' => '../../libraries/li3_grid/views/elements/box'), array(					
					'boxtitle' => 'This is an Element',
					'boxbody' => "These boxes, are elements. You can easily add them to your pages by adding an include element line. And the best part, you dont even have to copy them over to your elements directory. They are setup to use variables for the title and body. So all you have to do is pass those to the element and you have instant content"
				)); ?>
			</div>
			
			<div class="grid_4">
				<?php echo $this->view()->render(array('element' => '../../libraries/li3_grid/views/elements/box'), array(					
					'boxtitle' => 'How to Pass',
					'boxbody' => "To pass the title of the element, just store it into the 'boxtitle' key value in the element. And 'boxbody' for the body. You can then easily fill these values with content from a controller and place that variables identifier into the value field for the boxtitle and boxbody keys. If you do not want to pass the variables but want to just add the text into your element. Simply copy box.html.php element from li3_grid/views/elements to your own elements directories and edit as you desire."
				)); ?>
			</div>
			
			<div class="grid_4">
				<?php echo $this->view()->render(array('element' => '../../libraries/li3_grid/views/elements/box'), array(					
					'boxtitle' => 'Flexibility',
					'boxbody' => 'Most boxes you see on this page, are rendered using one single box element. Thats what they are for. For the more complex boxes, all you have to do is store the html into a variable and echo that variable using <strong>&lt;?=$varname?&gt;</strong>. You have to use that syntax so all the special characters are escaped such as \' and " so it does not break the box.'
				)); ?>
			</div>