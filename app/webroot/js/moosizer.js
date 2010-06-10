/*
MooSizer - a Mootools rewrite of: Supersized - Full Screen Background/Slideshow jQuery Plugin

License:
	MIT-style license.

Credits:
	Original jQuery supersized script By Sam Dunn ( <http://buildinternet.com> / <http://onemightyroar.com>	 )
	found here: <http://buildinternet.com/2009/02/supersized-full-screen-backgroundslideshow-jquery-plugin/>
	rewritten for Mootools 1.2 by Markus Timtner ( <http://mtness.net> ) 2009-03-27 1100-1500 GMT+1
*/

var DEBUG;(typeof(window.console) != "undefined")?DEBUG=1:DEBUG=0;//alert(DEBUG);

var mooSizer = new Class({

	Implements: [Options, Events],
	options: {
		startwidth: 640,  
		startheight: 480,
		minsize: .5,
		slideshow: 1,
		slideinterval: 5000,
		bgElement: ''
	},

	initialize: function(options){													//if(DEBUG==1)console.log("class initialised");
        this.setOptions(options);

		//Define image ratio & minimum dimensions
		var minwidth	= this.options.minsize*(this.options.startwidth);			//if(DEBUG==1)console.log("minwidth	"+minwidth	);
		var minheight	= this.options.minsize*(this.options.startheight);			//if(DEBUG==1)console.log("minheight	"+minheight	);
		var ratio		= this.options.startheight/this.options.startwidth;		//if(DEBUG==1)console.log("ratio		"+ratio		);

		this.resizenow(minwidth,minheight,ratio);

 		window.addEvent('resize', function(){										//if(DEBUG==1)console.log("resizenow event fired");
			this.resizenow(minwidth,minheight,ratio);
		}.bind(this));

	},
	
	resizenow: function(minwidth,minheight,ratio) {								// if(DEBUG==1)console.log("resizenow called");

		//Gather browser and current image size
		var imagesize		= $(this.options.bgElement).getSize();
		var imagewidth		= imagesize.x;											//if(DEBUG==1)console.log("imagewidth		"+imagewidth	);
		var imageheight		= imagesize.y;											//if(DEBUG==1)console.log("imageheight	"+imageheight	);
		var clientsize		= window.getSize();
		var browserwidth	= clientsize.x;											//if(DEBUG==1)console.log("browserwidth	"+browserwidth	);
		var browserheight	= clientsize.y;											//if(DEBUG==1)console.log("browserheight	"+browserheight	);
		
 		//Check for minimum dimensions
		if ((browserheight < minheight) && (browserwidth < minwidth)){				//if(DEBUG==1)console.log("within minimum dimensions");
				//$(this).height(minheight);
				$(this.options.bgElement).setStyle('height',minheight);
				//$(this).width(minwidth);
				$(this.options.bgElement).setStyle('width',minwidth);
		}
		else{	
			//When browser is taller	
			if (browserheight > browserwidth){										//if(DEBUG==1)console.log("browserheight > browserwidth");
				imageheight = browserheight;
					$(this.options.bgElement).setStyle('height',browserheight);
				imagewidth = browserheight/ratio;									//if(DEBUG==1)console.log("imagewidth		"+imagewidth	);
					$(this.options.bgElement).setStyle('width',imagewidth);
				
				if (browserwidth > imagewidth){										//if(DEBUG==1)console.log("browserheight > imagewidth");
					imagewidth = browserwidth;										//if(DEBUG==1)console.log("imagewidth		"+imagewidth	);
						$(this.options.bgElement).setStyle('width',browserwidth);
					imageheight = browserwidth * ratio;								//if(DEBUG==1)console.log("imageheight	"+imageheight	);
						$(this.options.bgElement).setStyle('height',imageheight);
				}
			}			
			//When browser is wider
			if (browserwidth >= browserheight){										//if(DEBUG==1)console.log("browserwidth >= browserheight");
				imagewidth = browserwidth;											//if(DEBUG==1)console.log("imagewidth		"+imagewidth	);
					$(this.options.bgElement).setStyle('width',browserwidth);
				imageheight = browserwidth * ratio;									//if(DEBUG==1)console.log("imageheight	"+imageheight	);
					$(this.options.bgElement).setStyle('height',imageheight);
				
				if (browserheight > imageheight){									//if(DEBUG==1)console.log("browserheight > imageheight");
					imageheight = browserheight;									//if(DEBUG==1)console.log("imageheight	"+imageheight	);
						$(this.options.bgElement).setStyle('height',browserheight);
					imagewidth = browserheight/ratio;								//if(DEBUG==1)console.log("imagewidth		"+imagewidth	);
						$(this.options.bgElement).setStyle('width',imagewidth);
				}
			}
		}
	}

});