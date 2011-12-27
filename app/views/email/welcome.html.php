<?php

use lithium\net\http\Router;

?>
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
                              <td valign="top" style="padding: 10px;" background="http://www.totsy.com/markup/v2_emails/assets/img/mid_back.png"><img src="https://www.totsy.com/img/email/welcome_mainpic.jpg" width="570" height="178" /></td>
                            </tr>
                            <tr>

                                <td valign="top" style="padding: 20px;" background="http://www.totsy.com/markup/v2_emails/assets/img/mid_back.png">
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
									<?=$this->html->link('My Account', array('Account::index'), array('style' => "color:#E00000;text-decoration:none")); ?>
									at Totsy</a>.
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
