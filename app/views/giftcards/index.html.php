        <div class="giftcard_content">
        
            <div class="giftcard_header">
                <h1 class="p-header">Totsy's <span class="gray bigger">e-Gift Card</span></h1>
            </div>
            <hr />

			<!-- giftcard form and image -->
            <div class="giftcard_form">
                <div style="float: left">
                
                <!-- giftcard image -->
                    <div class="giftcard_image" id="photo">
                    
                    <div id="gc_img_header_text" style="padding-left:70px; padding-top: 10px; padding-right:20px; font-weight: BOLD; color:#FFF; font-size:15px"></div> 
                     <div id="gc_img_text" style="padding-left:200px; padding-top: 20px; padding-right: 20px; color:#FFF; word-wrap:break-word">
                     <p style="width:100px;">
					 </p>
                     </div> 
                    </div>

				<!-- giftcard disclaimer -->
                    <div class="giftcard_disclaimer">
                        E-gift cards will be emailed to the recipient once your credit card is approved. You will also have the option to print. Totsy credits may not be used to purchase e-gift cards. Read our <a href="/pages/terms">terms of service</a>.
                    </div>
                </div>

				<!-- giftcard snding form -->
                <div id="giftcard_sending_form">
                    <div id="mb-frmdiv">
                        <?php echo $this->form->create('', array('method' => 'post', 'url'=>'/giftcard/preview', 'options'=>Array('name'=>'gc_form'))); ?>
                        
						 <!-- new row for each control. clear: both -->
                        <div class="giftcard_form_row">
                        
                        <!-- giftcard form label width -->
                            <label for="ocassions" class="form_labels">Occasion <span>*</span></label> <?php echo $this->form->select("ocassions", array(0 => 'Select an option...', 1 => 'Happy Birthday', 2 => 'Baby shower', 3 => 'Wedding', 4 => 'Anniversary'), array('id'=>'ocassions')); ?>
                        </div>

                        <div class="giftcard_form_row">
                            <label for="to" class="form_labels">To <span>*</span></label> <?php echo $this->form->text("to", array(0 => "Recipient Name"));  ?>
                        </div>

                        <div class="giftcard_form_row">
                            <label for="value" class="form_labels">Value <span>*</span></label> <?php echo $this->form->select("value", array(0 => 15.00, 1 => 50.00, 2 => 75.00)); ?>
                        </div>

                        <div class="giftcard_form_row">
                            <label for="recipients_email" class="form_labels">Recipient's email <span>*</span></label> <?php echo $this->form->text("recipients_email", array(0 => "Recipients email"));  ?>
                        </div>

                        <div class="giftcard_form_row">
                            <label for="recipients_email_confirmation" class="form_labels">Re-enter recipient's email <span>*</span></label> <?php echo $this->form->text("recipients_email_confirmation", array(0 => "Re-enter Recipient's email"));  ?>
                        </div>
                        
                        <div id="email_error_msg"></div>

						<!-- didn't use the formhelper herre because it doesn't have a nice way fo seprarting the label and the control. needed special width assigned to the text area...for whatever reason it was getting the same width as the other fields -->
                        <div class="giftcard_form_row">
                        	<label for="message" class="form_labels">Message <span>*</span></label>
                           <textarea name="gc_message" id="gc_message" style="width:153px; height: 100px;"></textarea>
                        </div>
                        
                        <div id="gc_msg_error" class="giftcard_form_row"></div>
                        <!--
                        <div class="giftcard_form_row">
                        	 <label for="preview" class="form_labels">Preview <span>*</span></label> 
                        	 <input id="preview" value="Preview" class="button fr" type="submit" />
                    	</div> -->
                        
                    </div>
                </div>
            </div>

			
			<!-- giftcard billing information -->
            <div id="giftcard_billing_info">
                <hr />

				<!-- billing address -->
                <div style="float: left">
                    <div id="giftcard_billing_address">
                        <strong>Billing Address</strong>
                        <hr />

                        <div id="address_text">
                            <ul>
                                <li>Micah Miller</li>
                                <li>1500 Avenue at Port Imperial # 603</li>
                                <li>Weehawken, NJ 07806</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
				<!-- credit card information form -->
				
				<!-- id for card form: width. float, height -->
                <div id="giftcard_sending_form">
                
                	<!-- card form header -->
                    <div id="cc_form_header">
                        Card Information
                    </div>

                    <div id="mb-frmdiv">
                        <?php echo $this->form->create(); ?>

						<!-- new row for each control. clear: both -->
                        <div class="giftcard_form_row">
                        
                        	<!-- giftcard form label width -->
                            <label for="first_name" class="form_labels">First Name<span>*</span></label> <?php echo $this->form->text("first_name", array(0 => 'first_name')); ?>
                        </div>

                        <div class="giftcard_form_row">
                            <label for="last_name" class="form_labels">Last Name<span>*</span></label> <?php echo $this->form->text("last_name", array(0 => 'last_name')); ?>
                        </div>

                        <div class="giftcard_form_row">
                            <label for="card_number" class="form_labels">Card Number<span>*</span></label> <?php echo $this->form->text("card_number", array(0 => 'card_number')); ?>
                        </div>

                        <div class="giftcard_form_row">
                            <label for="exp_month" class="form_labels">Expiration Date <span>*</span></label> <?php echo $this->form->select("exp_month", array(0 => 'January', 
                                                                                                          1 => 'February', 
                                                                                                          2 => 'March',
                                                                                                          3 => 'April', 
                                                                                                          4 => 'May', 
                                                                                                          5 => 'June',
                                                                                                          6 => 'July', 
                                                                                                          7 => 'August', 
                                                                                                          8 => 'September',
                                                                                                          9 => 'October', 
                                                                                                          10 => 'November', 
                                                                                                          11 => 'December'
                                                                                                          )); ?> 
                                                                                                          
                        <?php echo $this->form->select("exp_year", array(0 => '2012', 1 => '2013', 2 => '2014')); ?>
                        </div>

                        <div style="clear:both">
                            <label for="cvv_cvn" class="form_labels">CVV/CVN <span>*</span></label> <?php echo $this->form->text("cvv_cvn", array(0 => 'test')); ?>
                        </div>
                    </div>

                    <div style="padding-top:20px">
                        <input id="continue" value="Continue" class="button fr" type="submit" />
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>

   $(document).ready (function(){
   
   	   function validateEmail($email){
	     var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	     
	     if( !emailReg.test( $email ) ) {
	      	return false;
	     } else {
	      	return true;
	     }
	   }
	   
	    $("#ocassions").change(function () {
          var str = "";
          str = $("#ocassions option:selected").text();
          
          if($(this).val() > 0){
          	$("#gc_img_header_text").text(str);
          } else {
          	$("#gc_img_header_text").text("");
          }
       })
       
       $("#gc_form").submit( function(){
       
       		//validate the email and make sure the 
       		var email_error_msg = "";
       		       		
       		if( validateEmail( $("#recipients_email").val() || $("#recipients_email").val().length==0) ){
       			email_error_msg = "Invalid email, please re-enter";
       			$('#email_error_msg').html(email_error_msg);
       			$('#email_error_msg').show();
       			return false;
       		} else if ( $("#recipients_email_confirmation").val()!==$("#recipients_email").val() ) {
       			email_error_msg = "The emails do not match. Please re-enter.";	
       			$('#email_error_msg').html(email_error_msg);
       			$('#email_error_msg').show();
       			return false;
       		} else {
       			email_error_msg = "";
       			$('#email_error_msg').html(email_error_msg);
       			$("#email_error_msg").hide();
       		}
       		       		
       		//validate the personal message
       		if ( (message.length > 360 || $("#gc_img_text").val().length > 360 )  ) {
       			 $('#gc_msg_error').html("The message is too long. You can only input 360 characters");
       			 $('#gc_msg_error').show();
       			 return false;
       		}
      	 }
      )
              
      $("#gc_message").bind('keydown', function(e){

       var message = $("#gc_message").val();       
       
       //check for length of message. only let them enter delete of backspace key for editing       
       if ( (message.length > 360 || $("#gc_img_text").val().length > 360 ) && e.keyCode!=8 && e.keyCode!=46 ) {
       
       $('#gc_msg_error').html("The message is too long. You can only input 360 characters");
       $('#gc_msg_error').show();
       	
       	return false;
       } else {
       	$('#gc_msg_error').hide();
       	$('#gc_img_text').html(message);
       }
      });

   
   
      
   });
       
</script>


<script src="http://www.google.com/jsapi"></script>
   <script> google.load("jquery", "1.5.2", {uncompressed:true});</script>
   <script> google.load("jqueryui", "1.8.11", {uncompressed:true});</script>
       <!-- end jQuery / jQuery UI -->
