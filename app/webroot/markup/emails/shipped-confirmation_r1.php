<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<html>
<body>

	<style type="text/css">
	
		td{font-family:Arial,sans-serif;color:#888888;font-size:14px;line-height:18px}
		img{border:none}
	
	</style>

	<center>
		<table cellspacing="0" cellpadding="0" border="0" width="592">	
			
				<tr>
					<td colspan="4" style="text-align:center;padding:10px">
						If you are unable to see this message, <a href="#" style="color:#E00000">click here to view</a>
						<br>
						To ensure delivery to your inbox, please add <a href="#" title="Totsy Support Address" style="color:#E00000">support@totsy.com</a> to your address book
					</td>
				</tr>
				
				<tr>
					<!-- Remember to add absolute file paths to all images for production -->
					<td width="180">
						<a href="#" title="Totsy.com">
						<?php echo $this->html->link(
							$this->html->image(
								"$data[domain]/img/email/email-logo.jpg",
								array(
									'width'=>'180',
									'height'=>'116'
								)),
								'',
								array(
									'id' => 'Totsy'
								)
							);
						?>
						<!--  img src="../../img/email/email-logo.jpg" alt="Totsy" width="180" height="116" /></a -->
					</td>
					<td width="65">
						<a href="#" title="Current Totsy Sales">
						<?php echo $this->html->link(
							$this->html->image(
								"$data[domain]/img/email/sales-btn.jpg",
								array(
									'width'=>'65',
									'height'=>'116',
									'title' => 'Current Totsy Sales'
								)),
								"$data[domain]/sales", 
								array(
									'id' => 'Totsy'
								)
							);
						?>
						<!--  img src="../../img/email/sales-btn.jpg" alt="Current Totsy Sales" width="65" height="116" /></a -->
					</td>
					<td width="111">
						<a href="#" title="Access My Account">
						<?php echo $this->html->link(
							$this->html->image(
								"$data[domain]/img/email/account-btn.jpg",
								array(
									'width'=>'111',
									'height'=>'116',
									'title' => 'Access My Account',
									'alt' => 'Access My Account'
								)),
								"$data[domain]/sales",
								array('id' => 'Totsy', 'escape'=> false)
							);
						?>
						<!--  img src="../../img/email/account-btn.jpg" alt="Access My Account" width="111" height="116" /></a -->
					</td>
					<td width="236">
						<a href="#" title="Invite Friends to Totsy">
						<?php echo $this->html->link(
							$this->html->image(
								"$data[domain]/img/email/invite-btn.jpg",
								array(
									'width'=>'236',
									'height'=>'116',
									'alt' => 'Invite Friends to Totsy'
								)),
								"$data[domain]/sales", 
								array(
									'id' => 'Totsy',
									'escape'=> false,
									'title' => 'Invite Friends to Totsy'
								)
							);
						?>
						
						<!-- img src="../../img/email/invite-btn.jpg" alt="Invite Friends to Totsy" width="236" height="116" /></a -->
					</td>
				</tr>
				
				<tr> <!-- start body of email -->
					<td colspan="4">
						<table cellpadding="0" cellspacing="0" width="100%" style="border-left:1px solid #666; border-right:1px solid #666">
							<tr>
								<td colspan="4" style="padding:0 10px 10px 10px">
									<?php echo $this->html->image(
													"$data[domain]/img/email/order-shipped.jpg",
													array(
														'width'=>'570',
														'height'=>'78',
														'alt' => 'order-shipped'
													))
									?>
									<!-- img src="../../img/email/order-shipped.jpg" alt="order-shipped" width="570" height="78" / -->
								</td>
							</tr>
							
							<tr>
								<td style="padding:20px" valign="top">
									<p>Dear <?=$data['user']->firstname." ".$data['user']->lastname;?>,</p> 
									<p>Your order, <?=$data['order_number'] ?> has shipped
									<br>
									A summary of your order is available from your account page.<a href="#" style="color:#E00000;text-decoration:none"> Click here</a> to view your order.</p>
									<p>Track your order: <a href="#" style="color:#E00000;text-decoration:none">UPS Ground 1Z8V965W0340223174</a>
									<br>
									(Tracking information will be available within 24 hours)
									</p>
								</td>
								
							</tr>
							
							<tr>
								<td style=";color:#666;padding-top:5px;padding-bottom:5px;padding-left:20px" valign="top">
								<strong style="font-weight:bold">Order Summary</strong>		
							</tr>
							
							<tr>
								<td style="color:#666;padding:0 20px" colspan="4"><!-- start order detail table -->
									<table cellpadding="0" cellspacing="0" border="0" width="550">
										
										<tr>
											<td width="75" style="font-size:8pt;padding-bottom:15px;text-transform:uppercase"><strong>Item</strong></td>
											<td width="100" style="font-size:8pt;padding-bottom:15px;text-transform:uppercase"><strong>Description</strong></td>					
											<td width="50" style="font-size:8pt;padding-bottom:15px;text-transform:uppercase"><strong>Price</strong></td>							
											<td width="50" style="font-size:8pt;padding-bottom:15px;text-transform:uppercase"><strong>Qty</strong></td>							
											<td width="50" style="font-size:8pt;padding-bottom:15px;text-transform:uppercase"><strong>Subtotal</strong></td>
										</tr>
										
										<tr style="background-color:#e8e8e8">
											<td colspan="5" height="5"></td>
										</tr>
										
										<tr style="background-color:#e8e8e8;text-align:center">
											<td height="100" style="border-right:1px solid #000000;padding-top:5px;padding-bottom:5px" title="item">
												<img src="../../img/email/stroller-item.jpg" alt="stroller-item" width="95" height="88" />
											</td>
											<td height="100" style="border-right:1px solid #000000;padding-top:5px;padding-bottom:5px;text-align:left;padding-left:5px" title="description">
												<strong style="font-weight:bold;color:#000000">Stroller A</strong>
												<br>
												<strong style="font-weight:normal;font-size:10pt">Wheel Stripe</strong>
												<br>
												<strong style="font-weight:bold;font-size:10pt">Color:</strong> <strong style="font-weight:normal">red</strong>
												<br>
												<strong style="font-weight:bold;font-size:10pt">Size:</strong> <strong style="font-weight:normal">NA</strong>
												
											</td>
											<td height="100" style="border-right:1px solid #000000;padding-top:5px;padding-bottom:5px" title="price">
												<strong syle="text-weight:bold">$350</strong>
											</td>
											<td height="100" style="border-right:1px solid #000000;padding-top:5px;padding-bottom:5px;text-align:center" title="quantity">
												1
											</td>
											<td height="100" title="subtotal">
												<strong syle="text-weight:bold">$350</strong>
											</td>
										</tr>
										
										<tr style="background-color:#e8e8e8">
											<td colspan="5" height="5"></td>
										</tr>
										
									</table>
								</td><!-- end order detail table -->
							</tr>
							
							<tr>
								<td colspan="4"><!-- start totals table -->
									<table>
										<tr>
											<td style="padding:20px" valign="top">
												Order Subtotal:
												<br>
												Sales Tax:
												<br>
												Shipping:
												<br><br><br>
												<strong style="font-weight:bold;color:#606060">Total:</strong> 
											</td>
											
											<td style="padding:20px;text-align:right" valign="top">
												$350.00
												<br>
												$0.00
												<br>
												$6.00
												<br><br><br>
												<strong style="font-weight:bold;color:#606060">$356.00</strong>
											
											</td>
											
										</tr>
									</table>
								</td><!-- end totals table -->
							
							</tr>
							
							<tr>
								<td style="padding:0 20px 0 20px"><hr></td>
							</tr>
							
							<tr>
								<td colspan="4"><!-- start payment info table -->
									<table style="padding:0 0 0 20px">
										<tr>
											<td width="150">
												Order Subtotal:
												<br>
												Payment Info:
											</td>
											<td width="200">
												March 04, 2010
												<br>
												Master ending with 4595
											</td>
											<td width="150" style="font-weight:bold" valign="bottom">
												$356
											</td>
										</tr>
									
									</table>
								</td><!-- end payment info table -->
							
							</tr>
							
							<tr>
								<td style="padding:0 20px 0 20px"><hr></td>
							</tr>
							
							<tr>
								<td style="padding:10px 0 0 20px">
								Thank you,
								<br><br>
								<strong style="color:#E00000;font-weight:normal">Totsy</strong>
								</td>
							
							
							</tr>
					
						</table>		
					</td>	
				</tr> <!-- end body of email -->
				

				

				<tr>
					<td colspan="4" style="">
						<?php echo $this->html->image(
									"$data[domain]/img/email/footer-image.jpg.jpg",
									array(
										'width'=>'592',
										'height'=>'150',
										'alt' => 'footer-image'
									));
					?>
						<!--  img src="../../img/email/footer-image.jpg" alt="footer-image" width="592" height="150" / -->
					</td>
				</tr>

				<tr>
					<td colspan="4" style="text-align:center;padding:10px">
						Totsy - 27 West 20th Street, Suite 400 - New York, NY 10011 | 1-888-59TOTSY (1.888.791.1112) <a href="#" style="color:#E00000">info@totsy.com</a>
					</td>
				</tr>

				
		</table>
	</center>

</body>
</html>
