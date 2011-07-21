<div style="width: 100%;" align="center">
	<div style="width: 78%; background-color: #FFFFFF; border: 1px solid #777777; padding: 10px; display: inline-block; clear: both; position: relative;">
		
		<div style="width: 35%; border: 1px solid #AAAAAA; float:left; position: relative; padding:5px;" align="left">
			<?php foreach ($methods as $method){?>
			<div>
				<div><a href="/api/help">Intro</a></div>
				<?php if ($current_method1!=$method['clear']) { ?>
				<div><a href="/api/help/method/<?php echo $method['clear'];?>">
				<?php } ?>
					<?php echo $method['name'];?>
				<?php if ($current_method!=$method['clear']) { ?>
				</a></div>
				<?php } ?>
			</div>
			<?php }?>
		</div>
		<div style="width: 63%; border: 1px solid #AAAAAA; float:right; position: relative;">
			<div>
				This is actually the simpliest Totsy's api docs.
				<br>
				<br>
				If you search properly you'll be able to find answers for all of your questions in terms of this coll api.
				<br>
				Don't want to bother you any more ... HaVe fUn !!!
				<br> 
				;-)
				<br><br>
				All what you need to know about api  for smooth running is next:<br>
				-	does support http as well as  https protocols with some differences.
				-	does support GET and POST HTTP methods.
				
				So, lets take a look closer to find what are the difference between those two requests. 
				To make an HTPP-Request via non-secure protocol (http)  you need to sign each request with md5 hash of 
				your private key and query string params in alphabetical order by this formula 
				md5(<your private key><param 1><param 2><É><param n>)<br>
				
				You have an url: http://totsy.com/api/authorize?auth_token=<your-auth_token>
				At this step you have to add one extra parameter `time`  - it is a unix time stamp of what ever time is now.
				So you getting have quesry strung as: 
				auth_token=<your auth token>&time=< unix timestamp >
				next step is to calculate a md5 hash of those two params (in a future you might have more that those 2 params).
				
				In terms of secure protocol ( https ) minimum requirement is to pass in query string  authorization hash (auth_token or token).
				
				As of today api supports these methods: authorize, events and items. We will speak about those methods next.

				
			</div>
			<div style="position:relative; display: inline-block; clear: both; margin:10px;">
				<pre style="position: relative;">
					<code style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:1px; position:relative; width:90%;">
						$this->method = null;
					</code>
				</pre>
			</div>	
		</div>
	
	</div>
</div>