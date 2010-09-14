/*
 Highcharts JS v2.0.4 (2010-09-07)
 Exporting module

 (c) 2010 Torstein H�nsi

 License: www.highcharts.com/license
*/
(function(){var i=Highcharts,F=i.Chart,C=i.addEvent,u=i.defaultOptions,p=i.createElement,G=i.discardElement,y=i.css,D=i.merge,q=i.each,r=i.extend;u=Math;var J=u.max,s=document,K=window,v="M",w="L",z="div",L="hidden",A="none",M="highcharts-",H="absolute",o="px";u=i.setOptions({lang:{downloadPNG:"Download PNG image",downloadJPEG:"Download JPEG image",downloadPDF:"Download PDF document",downloadSVG:"Download SVG vector image",exportButtonTitle:"Export to raster or vector image",printButtonTitle:"Print the chart"}});
u.navigation={menuStyle:{border:"1px solid #A0A0A0",background:"#FFFFFF"},menuItemStyle:{padding:"0 5px",background:A,color:"#303030"},menuItemHoverStyle:{background:"#4572A5",color:"#FFFFFF"},buttonOptions:{align:"right",backgroundColor:{linearGradient:[0,0,0,20],stops:[[0.4,"#F7F7F7"],[0.6,"#E3E3E3"]]},borderColor:"#B0B0B0",borderRadius:3,borderWidth:1,height:20,hoverBorderColor:"#909090",hoverSymbolFill:"#81A7CF",hoverSymbolStroke:"#4572A5",symbolFill:"#E0E0E0",symbolStroke:"#A0A0A0",symbolX:11.5,
symbolY:10.5,verticalAlign:"top",width:24,y:10}};u.exporting={type:"image/png",url:"http://export.highcharts.com/",width:800,buttons:{exportButton:{symbol:"exportIcon",x:-10,symbolFill:"#A8BF77",hoverSymbolFill:"#768F3E",_titleKey:"exportButtonTitle",menuItems:[{textKey:"downloadPNG",onclick:function(){this.exportChart()}},{textKey:"downloadJPEG",onclick:function(){this.exportChart({type:"image/jpeg"})}},{textKey:"downloadPDF",onclick:function(){this.exportChart({type:"application/pdf"})}},{textKey:"downloadSVG",
onclick:function(){this.exportChart({type:"image/svg+xml"})}}]},printButton:{symbol:"printIcon",x:-36,symbolFill:"#B5C9DF",hoverSymbolFill:"#779ABF",_titleKey:"printButtonTitle",onclick:function(){this.print()}}}};r(F.prototype,{getSVG:function(b){var c=this,a,g,d,h,e,f=D(c.options,b);if(!s.createElementNS)s.createElementNS=function(k,j){var m=s.createElement(j);m.getBBox=function(){return c.renderer.Element.prototype.getBBox.apply({element:m})};return m};a=p(z,null,{position:H,top:"-9999em",width:c.chartWidth+
o,height:c.chartHeight+o},s.body);r(f.chart,{renderTo:a,renderer:"SVG"});f.exporting.enabled=false;f.chart.plotBackgroundImage=null;f.series=[];q(c.series,function(k){d=k.options;d.animation=false;d.showCheckbox=false;d.data=[];q(k.data,function(j){h=typeof j.config=="number"?{y:j.y}:j.config;h.x=j.x;d.data.push(h);(e=j.config.marker)&&/^url\(/.test(e.symbol)&&delete e.symbol});f.series.push(d)});b=new Highcharts.Chart(f);g=b.container.innerHTML;f=null;b.destroy();G(a);return g=g.replace(/zIndex="[^"]+"/g,
"").replace(/isShadow="[^"]+"/g,"").replace(/symbolName="[^"]+"/g,"").replace(/jQuery[0-9]+="[^"]+"/g,"").replace(/isTracker="[^"]+"/g,"").replace(/url\([^#]+#/g,"url(#").replace(/id=([^" >]+)/g,'id="$1"').replace(/class=([^" ]+)/g,'class="$1"').replace(/ transform /g," ").replace(/:path/g,"path").replace(/style="([^"]+)"/g,function(k){return k.toLowerCase()})},exportChart:function(b,c){var a,g=this,d=g.getSVG(c);b=D(g.options.exporting,b);a=p("form",{method:"post",action:b.url},{display:A},s.body);
q(["filename","type","width","svg"],function(h){p("input",{type:L,name:h,value:{filename:b.filename||"chart",type:b.type,width:b.width,svg:d}[h]},null,a)});a.submit();G(a)},print:function(){var b=this,c=b.container,a=[],g=c.parentNode,d=s.body,h=d.childNodes;if(!b.isPrinting){b.isPrinting=true;q(h,function(e,f){if(e.nodeType==1){a[f]=e.style.display;e.style.display=A}});d.appendChild(c);K.print();setTimeout(function(){g.appendChild(c);q(h,function(e,f){if(e.nodeType==1)e.style.display=a[f]});b.isPrinting=
false},1E3)}},contextMenu:function(b,c,a,g,d,h){var e=this,f=e.options.navigation,k=f.menuItemStyle,j=e.chartWidth,m=e.chartHeight,t="cache-"+b,l=e[t],n=J(d,h),x="3px 3px 10px #888",I,E;if(!l){e[t]=l=p(z,{className:M+b},{position:H,zIndex:1E3,padding:n+o},e.container);I=p(z,null,r({MozBoxShadow:x,WebkitBoxShadow:x},f.menuStyle),l);E=function(){y(l,{display:A})};C(l,"mouseleave",E);q(c,function(B){if(B)p(z,{onclick:function(){E();B.onclick.apply(e,arguments)},onmouseover:function(){y(this,f.menuItemHoverStyle)},
onmouseout:function(){y(this,k)},innerHTML:B.text||i.getOptions().lang[B.textKey]},r({cursor:"pointer"},k),I)});e.exportMenuWidth=l.offsetWidth;e.exportMenuHeigh=l.offsetHeight}b={display:"block"};if(a+e.exportMenuWidth>j)b.right=j-a-d-n+o;else b.left=a-n+o;if(g+h+e.exportMenuWidth>m)b.bottom=m-g-n+o;else b.top=g+h-n+o;y(l,b)},addButton:function(b){function c(){l.attr(x);t.attr(n)}var a=this,g=a.renderer,d=D(a.options.navigation.buttonOptions,b),h=d.onclick,e=d.menuItems;b=a.getAlignment(d);var f=
b.x,k=b.y,j=d.width,m=d.height,t,l;b=d.borderWidth;var n={stroke:d.borderColor},x={stroke:d.symbolStroke,fill:d.symbolFill};if(d.enabled!==false){t=g.rect(0,0,j,m,d.borderRadius,b).translate(f,k).attr(r({fill:d.backgroundColor,"stroke-width":b,zIndex:19},n)).add();b=g.rect(f,k,j,m,0).attr({fill:"rgba(255, 255, 255, 0.001)",title:i.getOptions().lang[d._titleKey],zIndex:21}).css({cursor:"pointer"}).on("mouseover",function(){l.attr({stroke:d.hoverSymbolStroke,fill:d.hoverSymbolFill});t.attr({stroke:d.hoverBorderColor})}).on("mouseout",
c).add();C(b.element,"click",c);if(e)h=function(){a.contextMenu("export-menu",e,f,k,j,m)};C(b.element,"click",function(){h.apply(a,arguments)});l=g.symbol(d.symbol,f+d.symbolX,k+d.symbolY,(d.symbolSize||12)/2).attr(r(x,{"stroke-width":d.symbolStrokeWidth||1,zIndex:20})).add()}}});i.Renderer.prototype.symbols.exportIcon=function(b,c,a){return[v,b-a,c+a,w,b+a,c+a,b+a,c+a*0.5,b-a,c+a*0.5,"Z",v,b,c+a*0.5,w,b-a*0.5,c-a/3,b-a/6,c-a/3,b-a/6,c-a,b+a/6,c-a,b+a/6,c-a/3,b+a*0.5,c-a/3,"Z"]};i.Renderer.prototype.symbols.printIcon=
function(b,c,a){return[v,b-a,c+a*0.5,w,b+a,c+a*0.5,b+a,c-a/3,b-a,c-a/3,"Z",v,b-a*0.5,c-a/3,w,b-a*0.5,c-a,b+a*0.5,c-a,b+a*0.5,c-a/3,"Z",v,b-a*0.5,c+a*0.5,w,b-a*0.75,c+a,b+a*0.75,c+a,b+a*0.5,c+a*0.5,"Z"]};i.Chart=function(b){b=new F(b);var c,a=b.options.exporting,g=a.buttons;if(a.enabled!==false)for(c in g)b.addButton(g[c]);return b}})();
