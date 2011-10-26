<div style="padding: 10px;">
	<div align="left">
		<b>Method</b>: Items<br>
		<b>Url</b>: /items<br>
		<b>Http protocols</b>: HTTP, HTTPS<br>
		<b>HTTP Methods</b>: GET/POST<br>
		<b>Formats</b>: json, xml<br>
		<b>Description</b>: Use this method to get list of all active items<br>
		
		<br>
		<br>
		<div style="margin-bottom: 20px;">
			<b>JSON Request Exampe</b>:
			
			<div style="padding-left: 10px;">
				<div>HTTP - Request:</div>
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						http://totsy.com/api/items.json?auth_token=&lt;your-auth_token&gt;&time=&lt;unix_timestamp&gt;&sig=&lt;md5_hash_of_queryStringParams&gt;
					</div>
				</div>
				<div>HTTPS - Request:</div>
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						https://totsy.com/api/items.json?auth_token=&lt;your-auth_token&gt;
					</div>
				</div>
			</div>
		</div>


		<div style="margin-bottom: 20px;">
			<b>JSON Response Exampe</b>:
			
			<div style="padding-left: 10px;">
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;" align="left">
						<div>{</div>
						<div style="padding-left:10px;">
							<div>'token'&nbsp;:&nbsp;'&lt;your-token&gt;',</div>
							<div>'items': [</div>
							<div style="padding-left:10px;">
								<div>{</div>
									<div style="padding-left:10px;">
										<div>'id'&nbsp;:&nbsp;'&lt;item id&gt;',</div>
										<div>'name'&nbsp;:&nbsp;'&lt;item name&gt;',</div>
										<div>'description'&nbsp;:&nbsp;'&lt;item description&gt;',</div>
										<div>'start_date'&nbsp;:&nbsp;'&lt;event startDate&gt;',</div>
										<div>'end_date'&nbsp;:&nbsp;'&lt;event endDate&gt;',</div>
										<div>'discount'&nbsp;:&nbsp;'&lt;persentage off&gt;',</div>
										<div>'image'&nbsp;:&nbsp;'&lt;item image url&gt;',</div>
										<div>'url'&nbsp;:&nbsp;'&lt;item url&gt;',</div>
										<div>'instock'&nbsp;:&nbsp;'&lt;inventory&gt;',</div>
									</div>	
								<div>}, ... </div>	
							</div>
							<div>]</div>		
						</div>
						<div>}</div>
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
						http://totsy.com/api/items.xml?auth_token=&lt;your-auth_token&gt;&time=&lt;unix_timestamp&gt;&sig=&lt;md5_hash_of_queryStringParams&gt;
					</div>
				</div>
				<div>HTTPS - Request:</div>
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						https://totsy.com/api/items.xml?auth_token=&lt;your-auth_token&gt;
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
						<div style="padding-left: 10px;">
							<div>&lt;token&gt;{your-new-token}&lt;/token&gt;</div>
							<div>&lt;items&gt;</div>
								<div style="padding-left: 10px;">
									<div>&lt;item id='{ item id }'&gt;</div>
									<div style="padding-left: 10px;">
										<div>&lt;name&gt;{ item name }&lt;/name&gt;</div>
										<div>&lt;description&gt;{ item description }&lt;/description&gt;</div>
										<div>&lt;startDate&gt;{ event start date }&lt;/startDate&gt;</div>
										<div>&lt;endDate&gt;{ event end date }&lt;/endDate&gt;</div>
										<div>&lt;discount&gt;{ item percentage off }&lt;/discount&gt;</div>
										<div>&lt;imgae&gt;{ item image url }&lt;/imgae&gt;</div>
										<div>&lt;url&gt;{ item url }&lt;/url&gt;</div>
										<div>&lt;inventory&gt;{ Boolean }&lt;/inventory&gt;</div>		
									</div>
									<div>&lt;/item&gt;</div>
								</div>
							<div>&lt;/items&gt;</div>						
						</div>
						<div>&lt;/root&gt;</div>
					</div>
				</div>
			</div>
		</div>
			
	</div>
</div>