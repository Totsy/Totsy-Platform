var cartTimer = function (cartExpires){
	var now = new Date();
		
	$('#itemCounter').countdown( { until: cartExpires, onExpiry: refreshCart, expiryText: "<div class='over' style='color:#EB132C; padding:5px;'>no longer reserved</div>", layout: '{mnn}{sep}{snn} minutes'} );
	
	if (cartExpires < now) {
	    $('#itemCounter').html("<span class='over' style='color:#EB132C; padding:5px;'>No longer reserved</span>");
	}
		
	function refreshCart() {
	    window.location.reload(true);
	}
};