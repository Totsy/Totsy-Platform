<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 	
    </head>
    <body style="background-color: #DADADA; font-family:Arial, sans-serif; color:#888888; font-size:14px; line-height:18px;">
        <center>
        <table width="593" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" style="font-size:11px; text-align:center; padding:20px;">If you are unable to see this message, <a href="<?=$data['domain'].'/invitation/'.$data['user']->invitation_codes[0]; ?>" title="Click to Accept Invitation" style="color:#E00000;text-decoration:none">click here to view</a><br>To ensure delivery to your inbox, please add <a href="mailto:support@totsy.com" style="color:#E00000;text-decoration:none">support@totsy.com</a> to your address book</td>
  </tr>
  </table>
        <table cellspacing="0" cellpadding="0" border="0" width="593">
<tbody>
                
                <tr>
                    <!-- Remember to add absolute file paths to all images for production -->

                    <td style="height: 143px;" background="http://www.totsy.com/markup/v2_emails/assets/img/top_back.png">
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td><a target="_blank" href="http://www.totsy.com" title="Totsy.com" border="0"><img height="112" width="181" src="http://www.totsy.com/markup/v2_emails/assets/img/header_01.png" alt="Totsy.com" border="0" /></a></td>
                                <td><a target="_blank" href="http://www.totsy.com/" title="Current Totsy Sales" border="0"><img height="112" width="53" src="http://www.totsy.com/markup/v2_emails/assets/img/header_02.png" alt="Sales" border="0"  /></a></td>
                                <td><a target="_blank" href="http://www.totsy.com/account" title="Access My Account" border="0"><img height="112" width="123" src="http://www.totsy.com/markup/v2_emails/assets/img/header_03.png" alt="My Account" border="0"  /></a></td>
                                <td><a target="_blank" href="http://www.totsy.com/invite" title="Invite Friends to Totsy" border="0"><img height="112" width="234" src="http://www.totsy.com/markup/v2_emails/assets/img/header_04.png" alt="invite Your Friends" border="0" /></a></td>
                            </tr>

                        </tbody>
                    </table>
                    </td>
                </tr>
                <tr>
                    <td>
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>

                                <td valign="top" background="http://www.totsy.com/markup/v2_emails/assets/img/mid_back.png">
                                <table cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="4" style="padding:0 10px 10px 10px"><img src="https://www.totsy.com/img/email/order-confirmation.jpg" width="570" height="77" /></td>
									</tr>
						<tr>
							<td style="padding:20px" valign="top">
								<p style="font-weight:bold">Order: <?=$order->order_id;?></p><br>
								<p>Thank you so much for shopping with Totsy. Please find your order summary information below.
								<br><br>
									A detailed summary of your order is also available at 
									<?=$this->html->link('www.totsy.com', 'www.totsy.com', array(
										'style' => "color:#E00000;text-decoration:none"
										)); 
									?>
								</p>
							</td>
						</tr>
						<tr>
							<td style="color:#666666;padding-top:5px;padding-bottom:5px;padding-left:20px" valign="top">
						</tr>
						<tr>
							<td style="color:#666666;padding:0 20px;" colspan="4"><!-- start order detail table -->
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<td width="75" style="padding-bottom:15px;text-transform:uppercase"><strong>Item</strong></td>
											<td width="100" style="padding-bottom:15px;text-transform:uppercase"><strong>Description</strong></td>
											<td width="50" style="padding-bottom:15px;text-transform:uppercase"><strong>Price</strong></td>
											<td width="50" style="padding-bottom:15px;text-transform:uppercase"><strong>Qty</strong></td>
											<td width="50" style="padding-bottom:15px;text-transform:uppercase"><strong>Subtotal</strong></td>
										</tr>
										<tr style="background-color:#e8e8e8">
											<td colspan="5" height="5"></td>
										</tr>
										<?php $items = $order->items->data(); ?>

										<?php foreach ($items as $item): ?>
											<tr style="background-color:#e8e8e8;text-align:center">
											<?php
												if (!empty($item['primary_image'])) {
													$image = $data['domain'].'/image/'. $item['primary_image'] . '.jpg';
												} else {
													$image = "$data[domain]/img/no-image-small.jpeg";
												}
											?>
											<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px" title="item">
												<?=$this->html->image("$image", array('width' => "95", 'height' => "88")); ?>
											</td>
											<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px;text-align:left;padding-left:5px" title="description">
												<strong style="font-weight:bold;color:#666666"><?=$item['description']?></strong>
												<br>
												<strong style="font-weight:bold">Color:</strong><strong style="font-size:10pt;font-weight:normal"><?=$item['color']?></strong>
												<br>
												<strong style="font-weight:bold">Size:</strong><strong style="font-size:10pt;font-weight:normal"><?=$item['size']?></strong>
											</td>
											<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px" title="price">
												<strong syle="text-weight:bold">
													$<?=number_format($item['sale_retail'],2); ?>
												</strong>
											</td>
												<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px;text-align:center" title="quantity">
													<?=$item['quantity']?>
												</td>
												<td height="100" title="subtotal">
													<strong syle="text-weight:bold">$<?php echo number_format(($item['quantity'] * $item['sale_retail']),2)?></strong>
												</td>
										<?php endforeach ?>
										</tr>
										<tr style="background-color:#e8e8e8">
											<td colspan="5" height="5"></td>
										</tr>

									</table>
							</td><!-- end order detail table -->
						</tr>
						<tr>
							<td colspan="4"><!-- start totals table -->
							</td><!-- end totals table -->
						</tr>
						<tr>
							<td style="padding:0 20px 0 20px"><hr></td>
						</tr>
						<tr>
							<td colspan="4" style="padding:0 0 0 20px">
								<table style="padding:0 0 0 20px">
									<tr>
										<td style="padding:20px" valign="top">
											Order Subtotal:
											<br>
											<?php if ($order->credit_used): ?>
											Credit Applied:
												<br>
											<?php endif ?>
											<?php if ($order->promo_discount): ?>
											Promotion Discount:
												<br>
											<?php endif ?>
											Sales Tax:
											<br>
											Shipping:
											<br><br><br>
											<strong style="font-weight:bold;color:#606060">Total:</strong> 
										</td>
										<td style="padding:20px;text-align:right" valign="top">
											$<?=number_format($order->subTotal,2); ?>
											<br>
											<?php if ($order->credit_used): ?>
												-$<?=number_format(abs($order->credit_used),2); ?>
												<br>
											<?php endif ?>
											<?php if ($order->promo_discount): ?>
												-$<?=number_format(abs($order->promo_discount),2); ?>
												<br>
											<?php endif ?>
											$<?=number_format($order->tax,2); ?>
											<br>
											$<?=number_format($order->handling,2); ?>
											<br><br><br>
											<strong style="font-weight:bold;color:#606060">$<?=number_format($order->total,2); ?></strong>
										</td>
									</tr>
								</table>
								<table style="padding:0 0 0 20px">
									<tr>
										<td style="padding:20px;">
											Payment Info:
										</td>
										<td width="200">
											 <?=strtoupper($order->card_type)?> ending with <?=$order->card_number?>
										</td>
									</tr>
									<tr>
										<td style="padding:20px;">
											Shipping Address:
										</td>
										<td>
											<address class="shipping-address">
												<?=$order->shipping->firstname;?> <?=$order->shipping->lastname;?><br>
												<?=$order->shipping->address; ?> <?=$order->shipping->address_2; ?><br />
												<?=$order->shipping->city; ?>, <?=$order->shipping->state; ?>
												<?=$order->shipping->zip; ?>
											</address>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="padding:0 20px 0 20px"><hr></td>
						</tr>
						<tr>
							<td colspan="4">
								<table>
									<tr>
										<td style="padding:0 0 0 20px;color:#888888">
											<br>
											<p>A TREE HAS BEEN PLANTED WITH THIS ORDER.
												<br>
												<strong style="color:#E00000;font-weight:normal"></strong>
												<?=$this->html->link('Find out how every purchase makes a difference.', array('Pages::being_green')); ?>
											</p>
										</td>
										<td>&nbsp;
											 
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="padding:0 0 0 20px">
							<br>
							Thank you,
							<br>
							<strong style="color:#E00000;font-weight:normal">Totsy</strong>
							</td>
						</tr>
					</table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </td>
                </tr>

                <tr>
                    <td name="Cont_6" style="padding: 20px;" background="http://www.totsy.com/markup/v2_emails/assets/img/bottom_back.png" height="161"></td>
                </tr>
                <tr>
                    <td style="font-size: 11px; text-align: center; padding: 20px;">Totsy - 10 West 18th Street, Floor 4 - New York, NY 10011 <a href="#" title="Info Email Address" style="color: rgb(224, 0, 0);">info@totsy.com</a>
                    </td>
                </tr>
            </tbody>
        </table>
        </center>
    </body>
</html>
