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
					<td width="180">
						<?=$this->html->link(
							$this->html->image(
								"$data[domain]/img/email/email-logo.jpg",
								array(
									'width'=>'180',
									'height'=>'116'
								)),
								'',
								array(
									'id' => 'Totsy',
									'escape'=> false
								)
							);
						?>
					</td>
					<td width="65">
						<?=$this->html->link(
							$this->html->image(
								"$data[domain]/img/email/sales-btn.jpg",
								array(
									'width'=>'65',
									'height'=>'116',
									'title' => 'Current Totsy Sales'
								)),
								"$data[domain]/sales", 
								array(
									'id' => 'Totsy',
									'escape'=> false
								)
							);
						?>
					</td>
					<td width="111">
						<?=$this->html->link(
							$this->html->image(
								"$data[domain]/img/email/account-btn.jpg",
								array(
									'width'=>'111',
									'height'=>'116',
									'title' => 'Access My Account',
									'alt' => 'Access My Account'
								)),
								"$data[domain]/sales",
								array(
									'id' => 'Totsy', 
									'escape'=> false
								)
							);
						?>
					</td>
					<td width="236">
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
					</td>
				</tr>
				<tr>
					<td colspan="4">
					<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-left:1px solid #666666;border-right:1px solid #666666">
						
						<tr>
							<td colspan="4" style="padding:0 10px 10px 10px">
								<?php echo $this->html->image(
									"$data[domain]/img/email/password-main-picture.jpg",
									array(
										'width'=>'570',
										'height'=>'177',
										'alt' => 'Totsy'
									));
								?>
							</td>
						</tr>
						
						<tr>
							<td style="padding:20px" valign="top">
								<p>Dear <?=$data['user']->firstname." ".$data['user']->lastname;?>,</p>
								<p>We understand that you are having some difficulty logging into your Totsy account. We've created a temporary password for you which is <?=$data['token']?>.</p>
								<p>To login with your temporary password and assign a new one that is easy for you to remember, please <a href="http://www.totsy.com/login" title="Totsy Login" style="color:#E00000">visit our login page</a>.
								<p>Our customer service team is also available to answer any questions. If you are still experiencing problems accessing Totsy.com after you have reset your password, please contact us at <a href="mailto:support@totsy.com" title="Totsy Support Address" style="color:#E00000">support@totsy.com</a> with the following information:</p>
								
								<ul>
								<li>Your first and last name</li>
								<li>Your email address and any others you may have used to register</li>
								</ul>

								<p>&nbsp</p>
								<p>All the best,</p>
								<p><strong style="color:#E00000;font-weight:normal">Totsy</strong></p>

							</td>
						</tr>
					</table>
				</tr>	
				<tr>
					<td colspan="4" style="">
						<?php echo $this->html->image(
							"$data[domain]/img/email/footer-image.jpg",
							array(
								'width'=>'592',
								'height'=>'150',
								'alt' => 'Totsy'
							));
						?>
					</td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center;padding:20px">
						Totsy - 27 West 20th Street, Suite 400 - New York, NY 10011 | 1-888-59TOTSY (1.888.791.1112) 
						<a href="#" title="Info Email Address" style="color:#E00000">info@totsy.com</a>
					</td>
				</tr>
		</table>
	</center>
</body>
</html>
