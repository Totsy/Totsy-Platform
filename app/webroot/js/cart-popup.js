//holds the timeout ID for the popup when the mouse leaves it
var timeout = ""; 
//fields related to this cart
var visibleItems = new Array(); 
//cart items not immediately visible
var invisibleItems = new Array();
//track whether state of of popup is collpased or not
var isCollapsed = false; 
//check whether the cart popup is open or not
var isOpen = false;

$(document).ready( function() {
	
	$(document).click( function(event) {
		if(event.target.id!='cart_popup_checkout' && event.target.id!='cart_popup_cont_shop') {
    		closeCartPopup();
	    }
	});
	
	$("#cart_popup").click( function(event) {
		if(event.target.id!='cart_popup_checkout' && event.target.id!='cart_popup_cont_shop'){
	    	return false;
	    }
	});

	//showing popup cart based on data returned from addItem Ajax vars
	var showCartPopup = function(cart) { 
		
		//reset all items
		if (invisibleItems.length > 0) {
			invisibleItems = [];
		}
		if (visibleItems.length > 0) {
			visibleItems = [];
		}
		
		//maximum amount of items visible before "see more" button is added
		var visibleItemCount = 2;
		var invisibleItemCount = 0; 
		
		//convert JSON string to JS Object
		var cartObj = eval('(' + cart + ')');
		
		//set divs based on data returned in addItem call
		$("#ship_date").text(cartObj.shipDate);
		$("#cart_popup_cont_shop").attr('href', cartObj.eventURL);
		
		//clear out this DIV....it seems to be caching old values...
		$("#order_total_num").html("");
		$("#order_total_num").text("$" + cartObj.subTotal.toFixed(2) + "");
		
		//nav header variables
		$("#cart-count").text(cartObj.itemCount);
		$("#cart-subtotal").text(cartObj.subTotal.toFixed(2));
		
		//set var for cart timer
		var cartExpirationDate = new Date(cartObj.cartExpirationDate * 1000);
		
		var cartItems = cartObj.cart;
						
		for (i in cartItems) { 
		    //formatting price and line totals
		    cartItems[i]['sale_retail'] = cartItems[i]['sale_retail'].toFixed(2);
		    cartItems[i]['line_total'] = (cartItems[i]['quantity'] * cartItems[i]['sale_retail']).toFixed(2);
		    		    
		    if (i < visibleItemCount) {
		    	visibleItems.push(cartItems[i]);
		    } else {
		    	invisibleItems.push(cartItems[i]);
		    	invisibleItemCount++;
		    }
		}
		
		//unset cart_item DIV
		$("#cart_item").html(""); 
		
		//attach template to cart_item DIV
		$("#template").tmpl(visibleItems).appendTo("#cart_item");
		
		if (invisibleItemCount > 0) {
			isCollapsed = true;
		    addScrollBar();
		}
			
		//set the cart timer		
		cartTimer(cartExpirationDate);				
		//set the timer per item
		cartItemsTimer();
		
		if( cartObj.itemCount > 0 ) {
			//set these
			$("#savings").text(cartObj.savings.items.toFixed(2));
			$("#cart_popup").fadeIn(100);			
		
			//set the popup to timeout after 8 seconds
			timeout = setTimeout(function() {
			closeCartPopup(); }, 8000);
		} else {
			//clear these out
			$("#savings").text("");
			$("#itemCounter").text("");
		}
	}
	
	//get cart data without having to add an item
	var getCartPopup = function() {		
		$.ajax ({
			url: $.base + 'cart/getCartPopupData',
			context: document.body,
			success: function(data) {
				if(data){
					showCartPopup(data);
				} 
			}
		});
	};
	
	//add items to cart
	var addItem = function() {
		var item_size = "";
		
		if (typeof $('#size-select').attr('value') != 'undefined' || $('#size-select').attr('value') == '') {
			item_size = $('#size-select').attr('value');
		} else {
			item_size = "no size";
		}
		
		$.ajax({
			url: $.base + 'cart/add',
			data: "item_id=" + item_id + "&" + "item_size=" + item_size,
			context: document.body,
			success: function(data) {	
				if(data!=="noPopup") {
					//tracking add to cart in GA
					showCartPopup(data);
					_gaq.push(['_trackEvent', 'Cart', 'Add', 'Add to Cart', 1]);
				} else {
					window.location = "/cart/view";
				}
			}
		});
	};
		
	var closeCartPopup = function() { 
		isCollapsed = false;
		//set isCollapsed to false so that the link doesn't appear on re-open
		$("#cart_popup").fadeOut(200); 
	}; 
	
	//make popup disappear 8 seconds after their mouse leaves it  
	$("#cart_popup").mouseleave(function() {
		closeCartPopup();
	}); 
	
	//click handler for adding items to cart
	$("#add-to-cart").click(function() {
	
		//if there is an active timeout, clear it
		if (timeout) {
			clearTimeout(timeout);
		}
		addItem();
	}); 
	
	$(".cart_icon").mouseover( function(){
		//if there is an active timeout, clear it
		if (timeout) {
			clearTimeout(timeout);
		}
		getCartPopup();
	});
	
	//toggle items for carts with more than 3 different types of items
	var addScrollBar = function() {
		if (isCollapsed) {
			//add a scrollbar
			$("#cart_item").css({
				"overflow-y": "scroll",
				"overflow-x": "hidden",
				"height": "227px"
			}); 
			
			//set label to toggle up
			//add all items to template
			$("#template").tmpl(invisibleItems).appendTo("#cart_item");
		} else {
			//remove scrollbar
			$("#cart_item").css({
				"overflow-y": "hidden",
				"height": "auto"
			}); 
			
			//unset cart_item DIV
			$("#cart_item").html(""); //set label to toggle down
			//load template only with 3 items
			$("#template").tmpl(visibleItems).appendTo("#cart_item");
		}
	}; 
	
	//close cart popup
	$("#cart_popup_close_button").click(function() {
		closeCartPopup();
	});
});