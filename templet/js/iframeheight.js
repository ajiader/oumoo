$(function(){
	iframeHeight();
});
function iframeHeight(){
	window.parent.iframeHeight(100);
	window.parent.iframeHeight($(document.body).outerHeight(true));
}