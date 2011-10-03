//holds the timeout ID for the popup when the mouse leaves it
var timeout = ""; //cart items immediately visible 
var visibleItems = new Array(); //cart items not immediately visible
var invisibleItems = new Array();
var isCollapsed = false; //jQuery for adding items.
$(document).ready(function() {
	var showCartPopup = function(cart) { //reset all items
		if (invisibleItems.length > 0) {
			invisibleItems = [];
		}
		if (visibleItems.length > 0) {
			visibleItems = [];
		}
		var visibleItemCount = 3;
		var invisibleItemCount = 0; //convert JSON string to JS Object
		var cartObj = eval('(' + cart + ')');
		$("#ship_date").html(cartObj.shipDate);
		$("#savings").html(cartObj.savings.items.toFixed(2));
		$("#order_total_num").html("$" + cartObj.total.toFixed(2) + "");
		$("#cart-count").html(cartObj.itemCount);
		$("#itemCounter").html(cartObj.cartExpirationDate);
		cartData = cartObj.cart;
		for (i in cartData) { //formatting price and line totals
			cartData[i]['sale_retail'] = cartData[i]['sale_retail'].toFixed(2);
			cartData[i]['line_total'] = (cartData[i]['quantity'] * cartData[i]['sale_retail']).toFixed(2);
			if (i < visibleItemCount) {
				visibleItems.push(cartData[i]);
			} else {
				invisibleItems.push(cartData[i]);
				invisibleItemCount++;
			}
		} //unset cart_item DIV
		$("#cart_item").html(""); //attach template to cart_item DIV
		$("#template").tmpl(visibleItems).appendTo("#cart_item");
		if (invisibleItemCount > 0) {
			$("#more_cart_items").css("visibility", "visible");
		}
		$("#cart_popup").fadeIn(500);
	}; //add items to cart
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
				showCartPopup(data);
			}
		});
	};
	var closeCartPopup = function() { //isCollapsed = false;
		$("#more_cart_items a").html("See more...");
		$("#cart_popup").fadeOut(500); //set isCollapsed to false so that the link doesn't appear on re-open
	}; //make popup disappear 8 seconds after their mouse leaves it  
	$("#cart_popup").mouseleave(function() {
		timeout = setTimeout(function() {
			$("#more_cart_items a").html("See more...");
			$("#cart_popup").fadeOut(500);
		}, 3000);
	}); //interrupt JS setTimeout using its timeout ID  
	$("#cart_popup").mouseover(function() {
		clearTimeout(timeout);
	}); //click handler for adding items to cart
	$("#add-to-cart").click(function() {
		if (timeout) {
			clearTimeout(timeout);
		}
		addItem();
	}); //toggle items for carts with more than 3 different types of items
	$("#more_cart_items a").click(function() {
		if (isCollapsed == false) {
			isCollapsed = true; //add a scrollbar
			$("#cart_item").css({
				"overflow-y": "scroll",
				"overflow-x": "hidden",
				"height": "227px"
			}); //set label to toggle up
			$("#more_cart_items a").html("...see less"); //add all items to template
			$("#template").tmpl(invisibleItems).appendTo("#cart_item");
		} else {
			isCollapsed = false; //remove scrollbar
			$("#cart_item").css({
				"overflow-y": "hidden",
				"height": "auto"
			}); //unset cart_item DIV
			$("#cart_item").html(""); //set label to toggle down
			$("#more_cart_items a").html("See more..."); //load template only with 3 items
			$("#template").tmpl(visibleItems).appendTo("#cart_item");
		}
	}); //close cart popup
	$("#cart_popup_close_button").click(function() {
		closeCartPopup();
	});
});
