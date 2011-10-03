$(document).ready( function() {

$( function () {
	    
	var now = new Date();
	
	$('#itemCounter').countdown( { until: cartExpires, onExpiry: refreshCart, expiryText: "<div class='over' style='color:#EB132C; padding:5px;'>no longer reserved</div>", layout: '{mnn}{sep}{snn} minutes'} );
	
	if (cartExpires < now) {
	    $('#itemCounter').html("<span class='over' style='color:#EB132C; padding:5px;'>No longer reserved</span>");
	}
	
	function refreshCart() {
	    window.location.reload(true);
	}
	
	//applying tooltip
	$('#shipping_tooltip').tipsy({gravity: 'e'}); // nw | n | ne | w | e | sw | s | se
	$('#tax_tooltip').tipsy({gravity: 'e'}); // nw | n | ne | w | e | sw | s | se
		});
});