window.addEvent('domready', function(){
	
	$$('.main-nav li').each(function(e){
		e.getLast().addEvent('mouseenter', function(){
			e.addClass('over');
		});	
		e.getLast().addEvent('mouseleave', function(){
			e.removeClass('over');
		});	
	});

	
});

function equalHeights( container ){
	var height = 0;
	
	divs = $$( container );
	 
	divs.each( function(e){
		if (e.offsetHeight > height){
			height = e.offsetHeight;
		}
	});
	 
	divs.each( function(e){
		e.setStyle( 'height', height + 'px' );
		if (e.offsetHeight > height) {
			e.setStyle( 'height', (height - (e.offsetHeight - height)) + 'px' );
		}
	});
}
