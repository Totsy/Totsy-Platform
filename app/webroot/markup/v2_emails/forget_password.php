<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 	
    </head>
    <body style="background-color: #DADADA; font-family:Arial, sans-serif; color:#888888; font-size:14px; line-height:18px;">
        <center>
        <table cellspacing="0" cellpadding="0" border="0" width="593">
            <tbody>
                
                <tr>
                    <!-- Remember to add absolute file paths to all images for production -->

                    <td style="height: 143px;" background="http://dev.totsy.com/markup/v2_emails/assets/img/top_back.png">
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td><a target="_blank" href="http://www.totsy.com" title="Totsy.com" border="0"><img height="112" width="181" src="http://dev.totsy.com/markup/v2_emails/assets/img/header_01.png" alt="Totsy.com" border="0" /></a></td>
                                <td><a target="_blank" href="http://www.totsy.com/" title="Current Totsy Sales" border="0"><img height="112" width="53" src="http://dev.totsy.com/markup/v2_emails/assets/img/header_02.png" alt="Sales" border="0"  /></a></td>
                                <td><a target="_blank" href="http://www.totsy.com/account" title="Access My Account" border="0"><img height="112" width="123" src="http://dev.totsy.com/markup/v2_emails/assets/img/header_03.png" alt="My Account" border="0"  /></a></td>
                                <td><a target="_blank" href="http://www.totsy.com/invite" title="Invite Friends to Totsy" border="0"><img height="112" width="234" src="http://dev.totsy.com/markup/v2_emails/assets/img/header_04.png" alt="invite Your Friends" border="0" /></a></td>
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

                                <td valign="top" style="padding: 20px;" background="http://dev.totsy.com/markup/v2_emails/assets/img/mid_back.png">
                                <p>Dear <?=$data['user']->firstname." ".$data['user']->lastname;?>,</p>
								<p>We understand that you are having some difficulty logging into your Totsy account. We've created a temporary password for you which is <?=$data['token']?>.</p>
								<p>To login with your temporary password and assign a new one that is easy for you to remember, please <a href="http://test.totsy.com/login" title="Totsy Login" style="color:#E00000">visit our login page</a>.
								<p>Our customer service team is also available to answer any questions. If you are still experiencing problems accessing Totsy.com after you have reset your password, please contact us at <a href="mailto:support@totsy.com" title="Totsy Support Address" style="color:#E00000">support@totsy.com</a> with the following information:</p>
								
								<ul>
								<li>Your first and last name</li>
								<li>Your email address and any others you may have used to register</li>
								</ul>

								<p>&nbsp;</p>
								<p>All the best,</p>
								<p><strong>Totsy</strong></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </td>
                </tr>

                <tr>
                    <td name="Cont_6" style="padding: 20px;" background="http://dev.totsy.com/markup/v2_emails/assets/img/bottom_back.png" height="161"></td>
                </tr>
                <tr>
                    <td style="text-align: center; padding: 20px;">Totsy - 27 West 20th Street, Suite 400 - New York, NY 10011 | 1-888-59TOTSY (1.888.791.1112) <a href="#" title="Info Email Address" style="color: rgb(224, 0, 0);">info@totsy.com</a>
                    </td>
                </tr>
            </tbody>
        </table>
        </center>
    </body>
</html>