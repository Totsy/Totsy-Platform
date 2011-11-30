var cartItemsTimer =  function(){

	$(".counter").each( function() {
		    				
		var fecha  = parseInt(this.title);
		var itemExpires = new Date();
		var now = new Date();
		
		itemExpires = new Date(fecha);	
		
		var expireNotice = (itemExpires.valueOf() - 120000);
		expireNotice = new Date( expireNotice );
		
		//show 2 minutes notice
		if(expireNotice < now && itemExpires > now){
		    $("#" + this.id + "_display").html('<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item will expire in 2 minutes</div>');
		} 
		
		//show item expired notice
		if(now > itemExpires) {
		    $("#" + this.id + "_display").html('<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item is no longer reserved</div>');
		}
		
		$("#" + this.id).countdown({until: expireNotice, 
		    						expiryText: '<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item will expire in 2 minutes</div>', 
		    						layout: '{mnn}{sep}{snn} seconds',
		    						onExpiry: resetTimer
		    						});
		
		function refreshCart() {
		    window.location.reload(true);
		}
		
		//call when item expires
		function notifyEnding() {
		    $("#" + this.id).countdown('change', { expiryText: '<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item is no longer reserved</div>', 
		    										onExpiry: refreshCart});
		
		    $("#" + this.id + "_display").html( '<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item is no longer reserved</div>' );
		}
		
		//call 2 minutes before the item expires							
		function resetTimer() {	
		    $("#" + this.id + "_display").html( $("#" + this.id).countdown('settings', 'expiryText') );
		    $("#" + this.id).countdown('change', { until: itemExpires, 
		    									   expiryText: '<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item is no longer reserved</div>',
		    									    onExpiry: notifyEnding
		    									   });
		}							
	});	
};	
