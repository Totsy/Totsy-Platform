<div style="padding:5px;">
	This is actually the simpliest Totsy's api docs.
	<br>
	<br>
	If you search properly you'll be able to find answers for all of your questions in terms of this coll api.
	<br>
	Don't want to bother you any more ... HaVe fUn !!!
	<br> 
	;-)
	<br><br>
	<div align="left">
		<p>
			All what you need to know about api  for smooth running is next:<br>
			<div style="padding: 10px;">
				<div style="padding-left: 5px;"> - does support http as well as  https protocols with some differences.</div>
				<div style="padding-left: 5px;"> - does support GET and POST HTTP methods.</div>
			</div>
		</p>
		<p>			
			So, lets take a look closer to find what are the difference between those two requests. 
			To make an HTPP-Request via non-secure protocol (http)  you need to sign each request with md5 hash of 
			your private key and query string params in alphabetical order by this formula
			<div style="margin: 10px; width:95%;" align="center"> 
				<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
					md5(&lt;your private key&gt;&lt;param 1&gt;&lt;param 2&gt;&lt;...&gt;&lt;param n&gt;)
				</div>
			</div>
		</p>
		<p>
			You have an url: 
			<div style="margin: 10px; width:95%;" align="center"> 
				<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
					http://totsy.com/api/authorize?auth_token=&lt;your-auth_token&gt;
				</div>
			</div>
			At this step you have to add one extra parameter <b>`time`</b>  - it is a unix time stamp of what ever time is now.<br>
			So you getting have quesry strung as:
			<div style="margin: 10px; width:95%;" align="center"> 
				<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
					auth_token=&lt;your auth token&gt;&time=&lt;unix timestamp&gt;
				</div>
			</div> 
	
			next step is to calculate a <b>md5 hash</b> of those two params (in a future you might have more that those 2 params).<br>
			In terms of secure protocol ( https ) minimum requirement is to pass in query string  authorization hash 
			(auth_token or token). 
			As of today api supports these methods: authorize, events and items. We will speak about those methods next.
		</p>
	</div>
</div>	