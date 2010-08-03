<?php

use lithium\net\http\Router;

?>
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
						To ensure delivery to your inbox, please add <a href="mailto:support@totsy.com" style="color:#E00000;text-decoration:none">support@totsy.com</a> to your address book
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
								"$data[domain]/account",
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
								"$data[domain]/Users/invite", 
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
					<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-left:1px solid #666; border-right:1px solid #666">
						<tr>
							<td colspan="4" style="padding:0 10px 10px 10px">
								<?=$this->html->image(
									"$data[domain]/img/email/welcome_mainpic.jpg",
									array(
										'width'=>'570',
										'height'=>'178',
										'alt' => 'welcome_mainpic'
									)); 
								?>
							</td>
						</tr>
						<tr>
							<td style="padding:20px" valign="top">
								<p>Dear <?=$user->firstname; ?>,</p> 
								<p>Welcome and congratulations! We are proud to welcome you to
									Totsy's network of exclusive savings for moms and families.</p>
								<p>As a member, you now have access to brand-specific sales, up to
									70% off retail prices, just for you and the kids, ages 0-7. All
									sales start at 9 PM (EST), last 48 to 72 hours, and always
									feature amazing deals from quality, luxury, and designer brands.
								</p>
								<p>To make sure you don't miss out, we will send you an email before
									each new sale with information about the featured brand and
									discount pricing. You can change your email preferences at any
									time by visiting
									<a href="<?php echo (Router::match('Account::index', $this->_request, array('absolute' => true))); ?>">My Account</a> at
									<a href="http://www.totsy.com/" style="color:#E00000;text-decoration:none">Totsy.com</a>.
								</p>
								<p>In addition to the great savings, take a minute to check out our
									<a href="http://www.totsy.com/pages/being_green" style="color:#E00000;text-decoration:none">
									environmentally conscious initiatives</a>, and find out how
									every purchase can make a difference.
								</p>
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
						Totsy - 27 West 20th Street, Suite 400 - New York, NY 10011 | 1-888-59TOTSY (1.888.791.1112) <a href="mailto:info@totsy.com" title="Info Email Address" style="color:#E00000">info@totsy.com</a>
					</td>
				</tr>	
		</table>
	</center>
</body>
</html>
