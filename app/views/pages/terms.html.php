<?php use lithium\storage\Session; ?>
<?php $this->title("Terms Of Use"); ?>
<div class="grid_16">
	<h2 class="page-title gray">About Us</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'aboutUsNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
</div>

<div class="grid_11 omega roundy grey_inside b_side">

	<h2 class="page-title gray">Terms Of Use</h2>
	<hr />
	
	<?php if(Session::read("layout", array("name"=>"default"))=="mamapedia"): ?>
	<p>Mamasource abides by the same terms of use as its partner and provider Totsy.</p>
	<hr />
	<?php endif ?>
    <p class="gray mar-b">
    	<strong>(revised as of 03/11/2012)</strong> 
    </p>
    <p>
        By clicking on the "I AGREE" button, you (hereinafter referred as "you", "You" or “Member”) agree to be irrevocably bound with Totsy, Inc. (hereinafter referred to as "TOTSY") by the following terms and conditions of sale and service, including the Totsy Rewards Program Official Rules set forth under Annex 1, (hereinafter referred to as the "Terms of Use") and the TOTSY privacy policy (hereinafter referred to as the “Privacy Policy”), which are incorporated in each and every sale transaction made on our website at Totsy.com (the “Site”) , a proprietary domain name of TOTSY, and affiliated websites, and govern all aspects of the relationship between TOTSY and its Members.
    </p>
    <br />
    <p>
        Please contact us at <a href="mailto:support@totsy.com">support@totsy.com</a> if you have any questions regarding these Terms of Use.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>1. Sale of Goods and Services</strong>
    </p>
    <hr/>
    <p>
        The Site contains offerings for the purchase of goods and services that are supplied by various brand merchants and suppliers ("Suppliers").  Before making purchases, you need to register as a member and agree to these Terms of Use and to establish one account for you ("Account").  Totsy only allows one Member to have one Account.  If you do not agree to these terms of Use, you may not use the Site in any way.  As part of your registration, you will give us certain personal information, which will be used by us in accordance with our Privacy Policy. By agreeing to the terms of this agreement, you are also consenting to receive marketing and advertisement e-mails from us from time to time.  You may opt out of these emails by logging into your Account and updating your email preferences under the <?php echo $this->html->link('My Account', array('Users::account')); ?> tab.
    </p>
    <br/>
    <p>
        The goods or services sold by TOTSY and described in a Purchase Order ("PO") are subject to these Terms of Use.  You and TOTSY agree to be bound by and to comply with all such conditions.  Each sale transaction will be confirmed to Member in a purchase order, or upon the commencement of performance by TOTSY.  All sales are final upon immediate payment by credit card or debit card by a Member.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>2. Terms of Payment and Purchase price</strong>
    </p>
    <hr/>
    <p>
        Along with the PO and a packing list, TOTSY shall submit an invoice with each shipment showing the PO number and other product information purchased by Member. When the sales order is ready for shipment, a copy of the invoice will be forwarded to you by email. All offers are priced in U.S. dollars and are intended to be valid only in the United States.  Applicable taxes will be added to all stated purchase prices prior to checkout.  All prices shown in the PO are firm and are not subject to adjustment. You will pay your PO immediately, before goods are shipped, by credit card using TOTSY's trusted online security payment system. Your credit card information will be used only for one transaction (corresponding to one PO). However, you may opt to store your credit card information with TOTSY to be used for future transactions on Totsy.com.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>3. Invite Friends Policy and Customer Credits</strong>
    </p>
    <hr/>
    <p>
    	<strong>3.1 Invite Friends Offer Details.</strong> For every new member that you  invite ("Invitee") to TOTSY, you (the "Sponsor") will receive a $15.00 in credit ("Credit"), or credit amount stated in promotional terms & conditions, to use only on Sponsor's next purchase.  The Credit shall be posted on our Account after Invitee's first qualifying purchase has been shipped.  The Credit shall have no value unless used for a purchase on the Site.  You can invite as many people as you want and you can earn as much Credits.  Only one Credit is allowed per invited new member.  Invitees must join via clicking the link in the e-mail invitation or clicking on your personal invite URL to receive the Credit.  Invitee can only accept one invite to TOTSY.  If an Invitee accepts an invite from someone else, Sponsor will not issue Credits in connection with that invitation.  In connection with these promotions, which may change from time to time, any person that receives Credits, prizes or other benefits by using multiple user accounts or email addresses, using false names, impersonating others, or through the use of any other fraudulent or misleading conduct, shall forfeit any Credits, prizes or benefits obtained through such means, and may be liable for civil and/or criminal penalties under applicable law..  All information submitted by you in connection with any referral will be subject to our Privacy Policy. 
    </p>
    <br/>
    <p>
        <strong>3.2 Customer Credits.</strong>  Credits are only for use on product contained within the Site and may not be used for the purchase of gift cards or services.  If a purchase is made but is thereafter cancelled or returned, you will not be awarded any Credit. Credits are promotional in nature without any exchange of money or value from you. Credits are not transferable to other accounts and as such, Credits do not constitute property and you do not have a vested property right or interest in the Credits. We reserve the right at any time, at our sole and absolute discretion, and without prior notice, to discontinue the Credits program or to add or change Credits program rules, terms or conditions, including changing expiration periods or Credits values for existing or future Credits.
    </p>
    <br/>
    <p>TOTSY reserves the right to change the terms and policies of this offer at any time without notice. TOTSY further reserves the right to suspend or terminate the Account of any user it believes is engaged in fraudulent, illegal, or inappropriate conduct in relation to this offer including, without limitation, creating fictitious, alias, or duplicate accounts to obtain credits. If your Account and/or membership is terminated for any reason, any credit balances in your Account will be cancelled, except as prohibited by law.  Account balances are determined by TOTSY and such determination is final.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>4. Delivery</strong>
    </p>
    <hr />
    <p>
        TOTSY will make its best efforts to deliver to you the goods purchased on Totsy.com, consistent with the estimated schedule of delivery indicated to you during the purchasing process, according to the shipping method you have selected. However, TOTSY's delivery obligations are subject to the performance of outside contractors, and TOTSY will not be held liable for any damages due to a delay in delivery, or errors in delivery due to erroneous shipment information entered by the Member. If a delay in delivery exceeds more than fifteen (15) business days above the estimated shipping date, it will be deemed as unreasonable and you will be entitled to cancel your PO, or part of the PO which is not delivered, and be entitled to a refund for those undelivered goods, subject however to the terms of our <?php echo $this->html->link('return policy', array('Pages::returns')); ?> concerning returns.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>5. Shipment — Shipping Errors, Lost or Damaged Merchandise, Defective Goods</strong>
    </p>
    <hr />
    <p>
        TOTSY shall ship the goods of each PO in their original purchase condition, including the original product packaging, manufacturer's containers, documentation, warranty cards, manuals, and all accessories, and in a manner that goods are properly protected from deterioration and contamination. You shall be responsible to pay only such freight charges as indicated on the face of your PO. For any shipping errors, lost or damages goods in transit, or defective goods, please refer to the terms of TOTSY's <?php echo $this->html->link('return policy', array('Pages::returns')); ?> incorporated in this Terms of Use by reference. If you receive quantities in excess of that shown in the PO, you must contact TOTSY's customer care immediately and TOTSY will ship back the goods shipped in excess at its own expenses.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>6. Warranty</strong>
    </p>
    <hr />
    <p>
        TOTSY warrants to Member that goods supplied under this PO are new, have not been used or refurbished, in compliance with all applicable specifications and the requirements of this PO. All goods supplied under this PO shall be free from any liens or encumbrance on title. TOTSY warrants that the goods sold on its website originates from licensed and authorized sellers and distributors, and that such licensed and authorized sellers or distributors have represented to TOTSY that their goods are not stolen or counterfeits, and do not violate or infringe of any copyright, trade name, trademark, patent or related property rights. In the unlikely event that Member believes that the good it purchased is a stolen good or a counterfeit, or otherwise infringes on a third party's intellectual property rights, the Member must inform TOTSY without delay.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>7. Compliance with Laws</strong>
    </p>
    <hr />
    <p>
        TOTSY warrants that all goods and services sold hereunder shall have been purchased, sold, delivered and furnished in strict compliance with all applicable laws and regulations to which they are subject. TOTSY shall execute and deliver such documents as may be required to effect or to evidence compliance. All laws and regulations required in agreements of this character are hereby incorporated by this reference.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>8. Confidentiality — Privacy Policy</strong>
    </p>
    <hr />
    <p>
        In the performance of the services, TOTSY and its subcontractors, if any, may have access to personal information pertaining to Members and which TOTSY wants to protect from disclosure. TOTSY undertakes to hold all of personal information it receives from its Members in strict confidence and neither to disclose or release in any manner such personal information to any third party, nor to use such personal information with any third party, nor to use such personal information for any other purpose than the one for which Member has disclosed same in accordance with its <?php echo $this->html->link('return policy', array('Pages::returns')); ?> which is incorporated to these Terms of Use by reference.
    </p>
    <br />
   	<p class="gray mar-b">
    	<strong>9. Intellectual Property</strong>
    </p>
    <hr />
    <p>
        <strong>9.1 Site Ownership - License.</strong> The content and compilation of content on the Site is the exclusive property of TOTSY and its licensors and protected by U.S. and international copyright laws. TOTSY grants you a limited license to access and make personal use of this Site. However, you may not download, manipulate or modify it, or any portion of it, without our express written consent. This license does not include any resale or commercial use of this Site or any of its contents, and you may not reproduce, copy, sell, resell, access or otherwise exploit all or any portion this Site for any commercial purpose without our express written consent. You may not use or reproduce TOTSY’s name, trademarks, service marks or logos or other proprietary information of TOTSY without our express written consent.  The Site, including but not limited to its graphics, logos, page headers, icons and service names constitute the property of TOTSY and its affiliates.  Other trademarks that appear on the Site are the property of their respective owners, who may or may not be affiliated with, connected to, or sponsored by TOTSY. Any images of persons or personalities contained on the Site are not an indication or endorsement of TOTSY or any particular product unless otherwise indicated.
    </p>
    <br/>
    <p>
        <strong>9.2 Intellectual Property Indemnification.</strong> Member agrees to defend, indemnify and hold harmless TOTSY, its officers, agents and employees from any third party claims, demands, damages and liabilities arising from any actual or alleged violation or infringement by Member of such third party intellectual property rights, including any intellectual property rights of TOTSY's Affiliates and Suppliers, which may be affixed and used on TOTSY’s Site.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>10. Disclaimer of Warranties and Limitation of Liability</strong>
    </p>
    <hr />
    <p>
        10.1. Each Business is the supplier of the goods and services presented on this Site, and each Business, and not TOTSY shall be fully responsible for compliance with applicable law related thereto and for any and all injuries, illnesses, damages and liabilities caused by or arising in respect of any such goods and services. You hereby release TOTSY from any liability related to the foregoing. All offers and activities are void where prohibited by law.
    </p>
    <br/>
    <p>
        10.2 Items pictures featured on this Site may not necessarily reflect exact colors or appearances of actual items due to digital reproduction variations and/or variations by a Business.  We do our best to display products as accurately as possible.  However, TOTSY and its service providers disclaim any and all responsibility or liability for the accuracy, content or legality of information or material provided on or through the Site.  Neither TOTSY nor its service providers shall be responsible for computer or network connections, transmissions, failures or other technical malfunctions. The Site may not always be virus or error-free.
    </p>
    <br/>
    <p>
        10.3 OTHER THAN AS MAY BE PROVIDED BY A BUSINESS, ALL GOODS, AND SERVICES INCLUDED IN OR OBTAINED THROUGH THE SITE, ARE PROVIDED "AS IS," WITH NO WARRANTIES. TOTSY AND ITS SERVICE PROVIDERS EXPRESSLY DISCLAIM TO THE FULLEST EXTENT PERMITTED BY LAW ALL EXPRESS, IMPLIED, AND STATUTORY WARRANTIES, INCLUDING, WITHOUT LIMITATION, THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE OR USE, AND NON-INFRINGEMENT OF PROPRIETARY RIGHTS.
    </p>
    <br/>
    <p>
        10.4 IF YOU DOWNLOAD OR OTHERWISE OBTAIN CONTENT OR DATA FROM THE SITE, YOU DO SO AT YOUR OWN RISK AND YOU ARE SOLELY RESPONSIBLE FOR ANY ASSOCIATED DAMAGES TO YOUR COMPUTER OR LOSS OF DATA. TOTSY AND ITS SERVICE PROVIDERS SHALL NOT BE LIABLE TO ANY MEMBER FOR ANY INDIRECT, INCIDENTAL, CONSEQUENTIAL, SPECIAL, EXEMPLARY, OR PUNITIVE DAMAGES WHETHER ANY CLAIM IS BASED ON WARRANTY, CONTRACT, TORT (INCLUDING NEGLIGENCE), STRICT LIABILITY, PRODUCT LIABILITY OR OTHERWISE, (EVEN IF TOTSY AND/OR ITS SERVICE PROVIDERS SHALL HAVE BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES). NEITHER TOTSY NOR ITS SERVICE PROVIDERS WILL BE LIABLE FOR ANY DAMAGES ARISING FROM THE TRANSACTIONS BETWEEN YOU AND THIRD PARTY BUSINESSES OR SERVICE PROVIDERS. TOTSY AND ITS SERVICE PROVIDERS’ TOTAL LIABILITY FOR ANY PARTICULAR CLAIM ARISING FROM OR RELATED TO THE SITE IS LIMITED TO THE AMOUNT ACTUALLY PAID BY YOU WITH RESPECT TO SUCH CLAIM. TO THE EXTENT THAT WE MAY NOT, AS A MATTER OF APPLICABLE LAW, DISCLAIM ANY IMPLIED WARRANTY OR LIMIT LIABILITIES, THE SCOPE AND DURATION OF SUCH WARRANTY AND THE EXTENT OF OUR LIABILITY WILL BE THE MINIMUM PERMITTED UNDER SUCH APPLICABLE LAW.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>11. Termination by TOTSY</strong>
    </p>
    <hr />
    <p>
        TOTSY reserves the right to cancel an Account, a PO, or any part thereof, without penalty at any time and for any reasons, except that TOTSY shall immediately refund Member for the price of the goods cancelled. Upon any membership or Account cancellation for any reason, you will be responsible for any unpaid amounts associated with prior purchases.  Any cancellation or termination of your Account will result in forfeiture of any credits that may have been applied thereto. TOTSY reserves its right to suspend or terminate your Account and/or unlist you as a member of Totsy.com at any time, in the event that you are in breach of any terms of these Terms of Use, for any acts or type of willful misconduct (including but not limited to receiving goods that are not paid for, misuse of any promotional credits, creating multiple accounts to benefit from free shipping), or if you have attempted to, or have disparaged, defamed or tarnished TOTSY and its trademarks.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>12. Force Majeure</strong>
    </p>
    <hr />
    <p>
        TOTSY shall not be liable for default or delay due to causes beyond TOTSY's control and without fault or negligence on the part of TOTSY, such as, but not limited to, strikes, lock outs, shut down of internet connections by Internet provider, cyber attacks on the Site, loss or stolen items while in transit and while in custody of carriers hired by TOTSY, non delivery of items ordered from supplier; provided, however, that TOTSY gives Member prompt notice in writing when any such cause occurs, or appears likely to delay deliveries and/or frustrate performances of services by TOTSY and takes appropriate action to avoid or minimize such delay, or such performance. If any such default or delay threatens to impair TOTSY's ability to meet delivery obligations for its material, supplies and services, Member shall have the right, without any liability to TOTSY, to cancel the portion or portions of this PO so affected. Member shall not be liable for default or delay in the performance of its obligations due to cause beyond its control.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>13. Jurisdiction and Governing Law</strong>
    </p>
    <hr />
    <p>
        For any suit or proceeding to enforce the provisions of these Terms of Use, TOTSY and the Member irrevocably consent to the exclusive jurisdiction of the federal (if applicable) or state Courts of the State of New York, in the County of New York.  TOTSY and the Member agree further that these Terms of Use shall be interpreted, construed and enforced in all respects in accordance with the laws of the State of New York, excluding its conflicts-of-law rules.
    </p>
    <br />
        <p class="gray mar-b">
    	<strong>14. Amendments of the Terms of Use</strong>
    </p>
    <hr />
    <p>
        These Terms of Use may be amended from time to time by TOTSY, at its sole discretion. Any revised Terms of Use will appear at the top of the document with the mention "revised as of MM/DD/YYYY." If you do not agree to these terms at any time, you should not conduct any more transactions or participate in any other activity through the Site. Your use of the Site after any such changes indicates that you agree to such changes.
    </p>
    <br />
        <p class="gray mar-b">
    	<strong>15. Assignment</strong>
    </p>
    <hr />
    <p>
        The rights and liabilities of the parties hereto shall bind and inure to the benefit of their respective successors, heirs, executors and administrators, as the case may be.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>16. Headings. Terms in Capital.</strong>
    </p>
    <hr />
    <p>
        The headings in these Terms of Use are intended principally for convenience and shall not, by themselves, determine the rights and obligations of the parties to these Terms of Use.  Terms in capital letters and defined in the Terms of Use shall have the same meaning throughout the Terms of Use.
    </p>
    <br />
    <p class="gray mar-b">
    	<strong>17. Severability</strong>
    </p>
    <hr />
    <p>
        If any provision of these Terms of Use, or any terms of its other terms incorporated by reference, is held to be unenforceable for any reason, such provision shall be adjusted rather than voided, if possible, in order to achieve the intent of the parties to the maximum extent possible. In any event, all other provisions of these Terms of Use shall be deemed valid and enforceable to the full extent possible.
    </p>
    <br />
        <p class="gray mar-b">
    	<strong>18. Waiver.</strong>
    </p>
    <hr />
    <p>
        The waiver of any term or condition contained in these Terms of Use by any party to these Terms of Use shall not be construed as a waiver of a subsequent breach or failure of the same term or condition or a waiver of any other term or condition contained in these Terms of Use.
    </p>
    <br />
        <p class="gray mar-b">
    	<strong>19. Entire Agreement.</strong>
    </p>
    <hr />
    <p>
        These Terms of Use contains all of the terms and conditions agreed upon by the parties relating to the subject matter hereof and supersedes any and all prior and contemporaneous agreements, negotiations, correspondence, understandings and communications of the parties, whether oral or written, respecting the subject matter hereof.
    </p>
    <br />
        <p class="gray mar-b">
    	<strong>20. Injunctive Relief and Service of Process.</strong>
    </p>
    <hr />
    <p>
        The Member acknowledges and agrees that damages will not be an adequate remedy in the event of a breach of any of the Member's obligations under these Terms of Use. The Member therefore agrees that TOTSY shall be entitled (without limitation of any other rights or remedies otherwise available to TOTSY and without the necessity of posting a bond) to obtain an injunction from any court of competent jurisdiction prohibiting the continuance or recurrence of any breach of these Terms of Use. The Member further agrees that service upon the Member in any such action or proceeding may be made by first class mail, certified or registered, to the Member's address as last appearing on the records of TOTSY.
    </p>
    <!-- reward program begin -->
    <br />
        <h2 class="page-title gray">ANNEX 1</h2>
    <hr />
        <p class="gray mar-b">
    	<strong>Totsy Rewards Program Official Rules</strong>
    </p>
    <hr />
    <p>
        NO PURCHASE NECESSARY.  Totsy Rewards Program (“Totsy Rewards”) is a loyalty program unique to TOTSY and powered by 500friends, Inc.  It is offered automatically and only to Members of TOTSY, unless a Member desires not to participate in such Rewards Program (see below Termination by Member). Participation in Totsy Rewards constitutes acceptance of these Official Rules (also available at <a href="/totsyrewardsrules">Totsy Rewards Rules</a>), can earn Points (defined below) for activities described at Totsy.com.  Points are credited into the Member’s account (the “Account”) and can be redeemed into one or more rewards as specified at <a href="/totsyrewards">Totsy Rewards</a>, or through a particular promotion (“Rewards”).  Points can also be used to allow Members to participate in rankings, earn recognition for certain activities and purchases, and other program elements as may be determined by TOTSY from time to time at its sole discretion.  The type of Rewards may change from time to time, without notice, at TOTSY’s sole discretion.
    </p>
    <br/>
    <p>
        ELIGIBILITY.  Totsy Rewards is offered only in the United States (excluding, without limitation, all US territories and overseas military installations) to individuals who are:
    </p>
    <br/>
    <p>
        Over 18 years of age as of the date of their first participation in Totsy Rewards, Legal US residents, and Possess a valid, active, and personal Account on Totsy.com, Facebook, or Twitter issued in that individual’s name and used to access the Totsy Rewards.
    </p>
    <br/>
    <p>
        When registering for Totsy Rewards, you agree to only register one (1) account for the purpose of accruing or earning Points.  Any Member or person who tries to use more than one account, Member ID, or create more than one identity to obtain more Points shall be disqualified at TOTSY’s sole discretion.
    </p>
    <br/>
    <p>
        POINTS.  For the purposes of Totsy Rewards, “Points” mean those particular measurement increments which can be used to redeem certain Rewards.  Points and their value are:
    </p>
    <br/>
    <p>
        Determined by TOTSY at its sole discretion, Subject to change without notice, May vary among activities and promotions, Are subject to approval, Have no monetary value, and May be subject to a limit for the amount of Points that may be earned during a defined period and/or for specific actions as determined at the sole discretion of TOTSY.
    </p>
    <br/>
    <p>
        All Points, including but not limited to earning, saving, and using Points, must be used in compliance with these Official Rules.  Points are not transferable and Points cannot be earned if and after Totsy Rewards is terminated, as set forth below.  Unless prohibited by law, unused Points are forfeited upon termination of Totsy Rewards.
    </p>
    <br/>
    <p>
        EARNING AND REDEEMING POINTS.  Members may earn a certain number of Points for particular activities fully described in the program overview at <a href="/totsyrewards">Totsy Rewards</a> or through a particular promotion through which a Member is invited to participate.
    </p>
    <br/>
    <p>
        Points and point-earning activities shall be reflected in a widget displayed in your My Account area of Totsy.com and/or on Facebook.com and may be recorded for review by TOTSY.  Points will be deposited in a commercially reasonable time on your Account after they are earned.  Any inquiries regarding Points not correctly deposited must be received by TOTSY within ten (10) business days of the date of the alleged accrual of Points.  A Member’s earned Points are not transferable.
    </p>
    <br/>
    <p>
        Members may redeem their Points into Rewards.  Depending on the Rewards redeemed, the Member will receive the Reward either from TOTSY or from 500friends, Inc.  To report any problem with a Reward, please contact us at <a href="mailto:support@totsy.com">support@totsy.com</a> and a customer service representative will assist you.
    </p>
    <br/>
    <p>
        TERMINATION BY TOTSY. TOTSY may terminate at its sole discretion the Totsy Rewards upon notice of termination, or without notice of termination in the event of any action, petition, or adjudication associated with bankruptcy, insolvency, assignments to creditors or material business interruptions of TOTSY or 500friends, Inc.  Notice of termination shall be provided to Members in the manner TOTSY deems reasonable, including but not limited to, posting such notice on Totsy.com, the TOTSY fan page on <a href="http://www.facebook.com/totsyfan" target="_blank">Facebook</a>, or via email.  Subject to applicable law, Points have an expiration date.  Members must redeem and use all Points before their expiration date, or the effective date of termination of Totsy Rewards.
    </p>
    <br/>
    <p>
        TOTSY reserves the right to modify, suspend, and/or terminate Totsy Rewards without notice, in whole or in part, in the event of computer, programming, system errors, or other problems which are beyond TOTSY’s control and that affect TOTSY’s ability to proceed as intended.  If Totsy Rewards is not capable of running as planned for any reason, including due to:
    </p>
    <br/>
    <p>
        Infection by computer virus, bugs, tampering, unauthorized intervention, fraud, technical failure, or other causes which corrupt or affect the administration, security, fairness, integrity, or proper conduct of Totsy Rewards; or Earthquake, flood, fire, storm, or other natural disaster, act of God, labor controversy or threat thereof, civil disturbance or commotion, disruption of public markets, war or armed conflict (whether or not officially declared), TOTSY reserves the right at its sole discretion to cancel, terminate, or suspend Totsy Rewards without obligation or prior notice.
    </p>
    <br/>
    <p>
        Any attempts by any Member to access Totsy Rewards via a bot script or other brute-force attack shall result in that Member becoming ineligible and forfeiting any and all accrued Points.  Any use of automated means, whether programmatic or robotic or the like, to gather Points shall result in a disqualification of the Member from Totsy Rewards.  TOTSY, in its sole discretion, reserves the right to disqualify and terminate participation of any Member or other user of Totsy.com found to be: i) tampering with the operation of Totsy Rewards or Totsy.com; ii) acting in violation of the Official
        Rules; iii) violating the Terms of Use of Totsy.com; iv) acting in an unethical or disruptive manner; v) acting with intent to annoy, abuse, threaten,
        or harass TOTSY, their representatives, or any other Member in any manner related to Totsy Rewards; vi) tampering with, altering, or attempting to
        alter any medium that reflects the amount of Points a Member has accrued; or vii) tampering with, altering, attempting to alter, creating, attempting
        to create or duplicate the medium that reflects the amount of Points a Member has accrued.  A Member committing any of the foregoing violations shall,
        at TOTSY’s sole discretion, forfeit the Points earned.
    </p>
    <br/>
    <p>
        TERMINATION BY MEMBER (LIMITED TO REWARDS PROGRAM).  A Member may terminate at any time his/her participation in the Rewards Program by notifying TOTSY at <a href="mailto:support@totsy.com">support@totsy.com</a>, and upon  such termination the Member may use Points accumulated up to the date of termination.
    </p>
    <br/>
    <p>
        Member may opt not to receive any e-mails, or promotions related to the Rewards Program by pressing “Unsubscribe” in any e-mail received from the
        Rewards Program.
    </p>
    <br/>
    <p>
        TAXES.  All federal, state, and local laws and regulations apply.  Taxes on Rewards, if any, are the Member’s sole responsibility.  Once a Member
        accumulates 6,000 Points, whether or not those Points are redeemed and used for Rewards or accumulated over an extended period of time, the Member may
        be required to complete an Affidavit and Release, with Points subject to forfeiture if the Affidavit and Release is not returned within the stated
        time.
    </p>
    <br/>
    <p>
        INDEMNIFICATION (SPECIFIC TO REWARDS PROGRAM).  By participating in Totsy Rewards, the Member agrees to the following:
    </p>
    <br />
    <p>
        To be bound by these Official Rules and by the decisions of TOTSY, which are final on all matters pertaining to Totsy Rewards; and To release, indemnify, and hold harmless TOTSY (and their parents, affiliates, subsidiaries, related entities, divisions, distributors, wholesalers,
        partners, licensees, retailers, sponsors, partnerships, representatives, vendors, contractors, successors, assigns, principals, shareholders,
        directors, officers, employees and agents, and their advertising, promotion, fulfillment agencies, and all other promotional partners – collectively
        called the “Promotional Partners”) from any liability or claims for damages arising from or connected with (i) altered, late, lost, damaged, destroyed,
        inaccurate, defaced, misdirected, mutilated, illegible, stolen, delayed, garbled, misrouted, incomplete entries or human, telephone, computer, online,
        or technical malfunctions (including busy lines and disconnections), (ii) Member’s participation, (iii) the awarding, acceptance, receipt, use,
        redemption, misuse, possession, loss, or misdirection of any Points or Rewards or preparing for or participation in any related activity or, (iv) all
        damages or injury to persons or property related to use or misuse of Totsy Rewards.  
    </p>
    <br/>
    <p>
        TOTSY, and all entities involved in conducting Totsy Rewards, reserve the right in their sole discretion to limit their participation in Totsy Rewards,
        assess varying Point values to Point earning opportunities, and to terminate or disqualify any Member’s involvement in Totsy Rewards.
    </p>
    <br/>
    <p>
        LIMITATIONS OF LIABILITY (SPECIFIC TO REWARDS PROGRAM).  THIS REWARDS PROGRAM AND ALL REWARDS ARE PROVIDED ON AN “AS IS” BASIS AND WITHOUT WARRANTY,
        GUARANTEE, OR REPRESENTATION OF ANY KIND, EXPRESSED OR IMPLIED, INCLUDING WITHOUT LIMITATION, ANY IMPLIED WARRANTY OF MERCHANTABILITY, FITNESS FOR A
        PARTICULAR PURPOSE AND NON-INFRINGEMENT.
    </p>
    <br/>
    <p>
        TOTSY IS NOT RESPONSIBLE OR LIABLE FOR: A) INCOMPLETE OR INCORRECT INFORMATION, GARBLED TRANSMISSIONS, AND TELECOMMUNICATIONS FAILURES OR SERVICE
        INTERRUPTIONS; B) A MEMBER’S USE OF TOTSY.COM, POINTS, OR REWARDS; C) FOR TYPOGRAPHICAL, PRINTING, OR OTHER ERRORS IN THE OFFER OR ADMINISTRATION OF
        TOTSY REWARDS; D) ERRORS, IRREGULARITIES, OR FAILURES IN: I) AWARDING, ACCUMULATING, RECEIVING, REDEEMING, OR USING POINTS; II) ADVERTISING; OR III)
        ACCESSING TOTSY REWARDS; E) MISTAKES IN OR CHANGES TO THE OFFICIAL RULES, THE SELECTION, NOTIFICATION, AND ANNOUNCEMENT OF THE POINTS, OR THE
        DISTRIBUTION OF REWARDS; F) ANY DIRECT OR INDIRECT DAMAGE(S), LOSS(ES), EXPENSE(S), OR INJURY(IES) RELATING TO, OR ARISING OUT OF THE DESIGN,
        MANUFACTURE, OR USE OF ANY REWARDS; OR G) ANY INCORRECT OR INACCURATE INFORMATION, WHETHER CAUSED BY WEB SITE USERS OR BY ANY OF THE EQUIPMENT OR
        PROGRAMMING ASSOCIATED WITH OR UTILIZED IN TOTSY REWARDS OR BY ANY TECHNICAL OR HUMAN ERROR WHICH MAY OCCUR IN THE PROCESSING OF SUBMISSIONS IN TOTSY
        REWARDS.  
    </p>
    <br/>
    <p>
        TOTSY ASSUMES NO RESPONSIBILITY FOR ANY ERROR, OMISSION, INTERRUPTION, DELETION, DEFECT, DELAY IN OPERATION OR TRANSMISSION, COMMUNICATIONS LINE
        FAILURE, THEFT OF OR DESTRUCTION OR UNAUTHORIZED ACCESS TO, OR ALTERATION OF, POINTS OR POINT ACCRUING ACTIVITIES.  TOTSY IS NOT RESPONSIBLE FOR ANY
        PROBLEMS, FAILURES, OR TECHNICAL MALFUNCTION OF ANY ONLINE SYSTEMS, SERVERS OR PROVIDERS, COMPUTER EQUIPMENT, HARDWARE/SOFTWARE, PLAYERS OR BROWSERS,
        FAILURE OF EMAIL OR POINT OR POINT ACCRUING ACTIVITIES DUE TO OR RESULTING FROM TECHNICAL PROBLEMS OR TRAFFIC CONGESTION ON THE INTERNET, MOBILE
        NETWORKS, OR AT ANY WEBSITE OR COMBINATION THEREOF, INCLUDING INJURY OR DAMAGE TO MEMBERS OR TO ANY OTHER PERSON’S COMPUTER RELATED TO OR RESULTING
        FROM PARTICIPATING OR DOWNLOADING MATERIALS RELATED TO TOTSY REWARDS.  TOTSY IS NOT RESPONSIBLE FOR THE INABILITY OF A MEMBER TO ACCEPT, REDEEM, AND/OR
        USE REWARDS FOR ANY REASON, INCLUDING ANY THIRD-PARTY’S TERMS AND CONDITIONS AND/OR THE TERMS OF BUSINESS AND OPERATIONS FOR A PARTICULAR ENTITY.
    </p> 
    <!-- reward program end --> 
</div>

</div>
<div class="clear"></div>
