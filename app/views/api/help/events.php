<div style="padding: 10px;">
	<div align="left">
		<b>Method</b>: Events<br>
		<b>Url</b>: /events<br>
		<b>Http protocols</b>: HTTP, HTTPS<br>
		<b>HTTP Methods</b>: GET/POST<br>
		<b>Formats</b>: json, xml<br>
		<b>Description</b>: Use this method to get list of all open, pending and closing events<br>
		
		<br>
		<br>
		<div style="margin-bottom: 20px;">
			<b>JSON Request Exampe</b>:
			
			<div style="padding-left: 10px;">
				<div>HTTP - Request:</div>
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						http://totsy.com/api/events.json?auth_token=&lt;your-auth_token&gt;&time=&lt;unix_timestamp&gt;&sig=&lt;md5_hash_of_queryStringParams&gt;
					</div>
				</div>
				<div>HTTPS - Request:</div>
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						http://totsy.com/api/events.json?auth_token=&lt;your-auth_token&gt;
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
							<div>'events': [</div>
							<div style="padding-left:10px;">
								<div>{</div>
									<div style="padding-left:10px;">
										<div>'name'&nbsp;:&nbsp;'&lt;event name&gt;',</div>
										<div>'description'&nbsp;:&nbsp;'&lt;event description&gt;',</div>
										<div>'start_date'&nbsp;:&nbsp;'&lt;event startDate&gt;',</div>
										<div>'end_date'&nbsp;:&nbsp;'&lt;event endDate&gt;',</div>
										<div>'discount'&nbsp;:&nbsp;'&lt;persentage off&gt;',</div>
										<div>'image'&nbsp;:&nbsp;'&lt;event image url&gt;',</div>
										<div>'url'&nbsp;:&nbsp;'&lt;event url&gt;',</div>
										<div>'enabled'&nbsp;:&nbsp;'&lt;inventory&gt;',</div>
									</div>	
								<div>}, ... </div>	
							</div>
							<div>],</div>
							<div>'pending': [</div>
							<div style="padding-left:10px;">
								<div>{</div>
								<div style="padding-left:10px;">
									<div>'name'&nbsp;:&nbsp;'&lt;event name&gt;',</div>
									<div>'url'&nbsp;:&nbsp;'&lt;event url&gt;'</div>
								</div>
								<div>}</div>		
							</div>
							<div>],</div>
							<div>'closing': [</div>
							<div style="padding-left:10px;">
								<div>{</div>
								<div style="padding-left:10px;">
									<div>'name'&nbsp;:&nbsp;'&lt;event name&gt;',</div>
									<div>'end_date'&nbsp;:&nbsp;'&lt;event endDate&gt;',</div>
									<div>'url'&nbsp;:&nbsp;'&lt;event url&gt;'</div>
								</div>
								<div>}</div>		
							</div>
							<div>]</div>							
					</div>
					<div>}</div>
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
						http://totsy.com/api/events.xml?auth_token=&lt;your-auth_token&gt;&time=&lt;unix_timestamp&gt;&sig=&lt;md5_hash_of_queryStringParams&gt;
					</div>
				</div>
				<div>HTTPS - Request:</div>
				<div style="margin: 10px; width:95%;" align="center"> 
					<div style="color: #FFFFFF; background: #141414; border: 3px solid #E6E6E6; overflow:auto; padding:5px; position:relative; white-space: nowrap;">
						http://totsy.com/api/events.xml?auth_token=&lt;your-auth_token&gt;
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
							<div>&lt;events&gt;</div>
								<div style="padding-left: 10px;">
									<div>&lt;event'&gt;</div>
									<div style="padding-left: 10px;">
										<div>&lt;name&gt;{ event name }&lt;/name&gt;</div>
										<div>&lt;description&gt;{ event description }&lt;/description&gt;</div>
										<div>&lt;startDate&gt;{ event start date }&lt;/startDate&gt;</div>
										<div>&lt;endDate&gt;{ event end date }&lt;/endDate&gt;</div>
										<div>&lt;discount&gt;{ max percentage off }&lt;/discount&gt;</div>
										<div>&lt;imgae&gt;{ event image url }&lt;/imgae&gt;</div>
										<div>&lt;url&gt;{ event url }&lt;/url&gt;</div>
										<div>&lt;enabled&gt;{ Boolean }&lt;/enabled&gt;</div>		
									</div>
									<div>&lt;/event&gt;</div>
								</div>
							<div>&lt;/events&gt;</div>
							
							<div>&lt;pendingEvents&gt;</div>
								<div style="padding-left: 10px;">
									<div>&lt;pendingEvent'&gt;</div>
									<div style="padding-left: 10px;">
										<div>&lt;name&gt;{ event name }&lt;/name&gt;</div>
										<div>&lt;url&gt;{ event url }&lt;/url&gt;</div>		
									</div>
									<div>&lt;/pendingEvent&gt;</div>
								</div>
							<div>&lt;/pendingEvents&gt;</div>

							<div>&lt;closingEvents&gt;</div>
								<div style="padding-left: 10px;">
									<div>&lt;closingEvent'&gt;</div>
									<div style="padding-left: 10px;">
										<div>&lt;name&gt;{ event name }&lt;/name&gt;</div>
										<div>&lt;endDate&gt;{ event end date }&lt;/endDate&gt;</div>
										<div>&lt;url&gt;{ event url }&lt;/url&gt;</div>		
									</div>
									<div>&lt;/closingEvent&gt;</div>
								</div>
							<div>&lt;/closingEvents&gt;</div>
													
						</div>
						<div>&lt;/root&gt;</div>
					</div>
				</div>
			</div>
		</div>
		<root>
<events>
	<event>
		<name>{event name}</name>
		<description>{ event description}</description>
		<startDate>{ event start date}</starDate>
		<endDate>{ event ebd date}</endDate>
		<discount>{ max percentage off }</discount>
		<imgae>{ event image url }</image>
		<url>{ event url }</url>
		<enabled>{ Boolean }</enabled>
	</event>
</events>
<pendingEvents>
	<pendingEvent>
		<name>{event name}</name>
		<url>{ event url }</url>		
	</pendingEvent>
</pendingEvent>
<closingEvents>
	<closingEvent>
		<name>{event name}</name>
		<endDate>{ event ebd date}</endDate>
		<url>{ event url }</url>
	</closingEvent>
</closingEvents>
</root>
			
	</div>
</div>