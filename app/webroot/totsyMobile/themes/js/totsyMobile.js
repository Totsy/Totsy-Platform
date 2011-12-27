$(document).bind("mobileinit", function(){
  $.extend(  $.mobile , {
    ajaxEnabled: false,
    loadingMessage: 'Loading please wait...',
    metaViewportContent: "width=device-width, minimum-scale=1, maximum-scale=1",
  });
});