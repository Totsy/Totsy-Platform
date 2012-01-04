<div style="padding: 10px;">
	<div align="left">
		<b>Method</b>: Authorization<br>
		<b>Url</b>: /authorize<br>
		<b>Http protocols</b>: HTTP, HTTPS<br>
		<b>HTTP Methods</b>: GET/POST<br>
		<b>Formats</b>: json, xml<br>
		<b>Description</b>: Use this method for  the authorization process<br>
		
		<br>
		<br>
		<div style="margin-bottom: 20px;">
			<b>JSON Request Exampe</b>:
			
			<div style="padding-left: 10px;">
				<div>HTTP - Request:</div>
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						http://totsy.com/api/authorize.json?auth_token=&lt;your-auth_token&gt;&time=&lt;unix_timestamp&gt;&sig=&lt;md5_hash_of_queryStringParams&gt;
					</div>
				</div>
				<div>HTTPS - Request:</div>
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						https://totsy.com/api/authorize.json?auth_token=&lt;your-auth_token&gt;
					</div>
				</div>
			</div>
		</div>


		<div style="margin-bottom: 20px;">
			<b>JSON Response Exampe</b>:
			
			<div style="padding-left: 10px;">
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						{&nbsp;'token'&nbsp;:&nbsp;'&lt;your-new-token&gt;'&nbsp;}
					</div>
				</div>
			</div>
		</div>		
		
		<div style="margin: 15px;">&nbsp;</div>
		
		<div>
			<b>XML Request Exampe</b>:
			
			<div style="padding-left: 10px;">
				<div>HTTP - Request:</div>
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						http://totsy.com/api/authorize.xml?auth_token=&lt;your-auth_token&gt;&time=&lt;unix_timestamp&gt;&sig=&lt;md5_hash_of_queryStringParams&gt;
					</div>
				</div>
				<div>HTTPS - Request:</div>
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						https://totsy.com/api/authorize.xml?auth_token=&lt;your-auth_token&gt;
					</div>
				</div>
			</div>
		</div>
		
		<div style="margin-bottom: 20px;">
			<b>XML Response Exampe</b>:
			
			<div style="padding-left: 10px;">
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;" align="left">
						<div>&lt;?xml version="1.0"?&gt;</div>
						<div>&lt;root&gt;</div>
						<div style="padding-left: 10px;">&lt;token&gt;{your-new-token}&lt;/token&gt;</div>
						<div>&lt;/root&gt;</div>
					</div>
				</div>
			</div>
		</div>
			
	</div>
</div>