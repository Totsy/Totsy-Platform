/**
 * Marquee - Content Slideshow
 * JQuery version Converted from MooTools version cause I can
 *
 * Allows marquee items to be controlled and animated
 *
 * @version		1.0
 *
 * @license		GPL
 * @author		Jonathan Lackey / Zuno Studios & Joshua Ross / JavaPimp
 * @copyright	Author
 */

var Marquee = new Class({
	
	total: 0,
	current: 0,
	fadeTime: 0,
	pauseTime: 0,
	bUserClick: false,
	
	initialize: function( fadeTime, total, pauseTime ){
			
		this.current = 1;
		this.total = total;	
		this.fadeTime = fadeTime * 1000;
		this.pauseTime = pauseTime * 1000;
		this.fadeIn(this.current);
		
		//add functionality for thumbnails
		$$('span.citem').each(function(el){
		
			el.click(function(e){
				this.bUserClick = true;
				this.removeEvent('play', this.play);
				this.fadeOut(this.current);	
				this.current = el.getProperty('rel');
				this.fadeIn(this.current);
		
			}.bind(this));
		
		}.bind(this));
		
		//add functionality for left/previous arrow
		$('lt-arrow').click(function(e){
			this.bUserClick = true;
			this.removeEvent('play', this.play);
			this.fadeOut( this.current );
			
			if( this.current == 1 ){
				this.current = this.total;
			}else{
				this.current--;
			}
			
			this.fadeIn( this.current );
			
		}.bind(this));
		
		//add functionality for right/next arrow
		$('rt-arrow').click(function(e){
			this.bUserClick = true;
			this.removeEvent('play', this.play);
			this.fadeOut( this.current );
			
			if( this.current == this.total ){
				this.current = 1;
			}else{
				this.current++;
			}
			
			this.fadeIn( this.current );
			
		}.bind(this));
		
		this.addEvent('play', this.play);
		this.fireEvent('play', [], this.pauseTime);
		
	},
	
	play: function(){
		
		if (this.bUserClick) {
			return;
		}
		
		this.fadeOut(this.current);
		
		if( this.current == this.total ){
			this.current = 1;
		}else{
			this.current++;
		}
		
		this.fadeIn(this.current);	
		return this.fireEvent('play', [], this.pauseTime);
			
	},
	
	fadeOut: function( iNum ){
		
		var player = $('player_'+iNum);
		if( player ){
			if( player.getPlayerState() == 1 ){
				player.pauseVideo();
			}
		}
		
		$('item_'+iNum).effect('opacity', { duration:this.fadeTime }).start(0);
		$('citem_'+iNum).removeClass('active-th');
	
	},
	
	fadeIn: function( iNum ){
	
		$('item_'+iNum).effect('opacity', { duration:this.fadeTime }).start(1);
		$('citem_'+iNum).addClass('active-th');	
	
	},
	
	stop: function(){
	
		this.bUserClick = true;
				
	}

	
});

Marquee.implement(new Events)