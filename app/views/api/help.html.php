<div style="width: 100%;" align="center">
	<div style="width: 78%; background-color: #FFFFFF; border: 1px solid #777777; padding: 10px; display: inline-block; clear: both; position: relative;">
		
		<div style="width: 30%; border: 1px solid #AAAAAA; float:left; position: relative; padding:5px;" align="left">
			<div>
				<?php if ($current_method!='intro') { ?>
				<a href="/api/help">
				<?php } ?>
					Intro
				<?php if ($current_method!='intro') { ?>
				</a>
				<?php } ?>
			</div>
			<?php foreach ($methods as $method){?>
			<div>
				<?php if ($current_method!=$method['clear']) { ?>
				<a href="/api/help/method/<?php echo $method['clear'];?>">
				<?php } ?>
					<?php echo $method['name'];?>
				<?php if ($current_method!=$method['clear']) { ?>
				</a>
				<?php } ?>
			</div>
			<?php }?>
		</div>
		<div style="width: 68%; border: 1px solid #AAAAAA; float:right; position: relative;">
			<?php echo $content; ?>
		</div>
	</div>
</div>