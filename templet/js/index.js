/**
 * @author Administrator
 */
function AddFavorite(sURL, sTitle) {
    try {
        window.external.addFavorite(sURL, sTitle);
    }
    catch (e) {
        try {
            window.sidebar.addPanel(sTitle, sURL, "");
        }
        catch (e) {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}
function SetHome(obj, vrl) {
    try {
        obj.style.behavior = 'url(#default#homepage)'; obj.setHomePage(vrl);
    }
    catch (e) {
        if (window.netscape) {
            try {
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
            }
            catch (e) {
                alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将 [signed.applets.codebase_principal_support]的值设置为'true',双击即可。");
            }
            var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
            prefs.setCharPref('browser.startup.homepage', vrl);
        }
    }
} 
$(function(){
	$(".se_type .select").click(function(){
		$(".search_type").val($(this).attr('type_id'));
		$(".se_type .in").html($(this).html());
		$(".se_type .select").css({display:'none'});
	});
	$(".se_type").hover(function(){
		$(".se_type .select").css({display:'block'});
	},function(){
		$(".se_type .select").css({display:'none'});
	});
	$('.pic_url').click(function(){
		$(".mousetrap").remove();
		$("#pic_url").attr('src',$(this)[0].src);
		$(".pic_url").removeClass('in');
		$(this).addClass('in');
		$("#zoom1").attr('href',$(this)[0].src);
		$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
	});
	setTimeout ("colos('#ad1')",5000);
});

function colos($id){
	$($id).remove();
}


function show_task() {document.write('<script type="text/javascript" src="/mini.html?&refresh='+Math.random()+'.js"></sc'+'ript>');}



/* 
* 
*/ 
(function($){ 
$.fn.JNMagnifier=function(setting){ 
if(setting&&setting.renderTo){ 
if(typeof(setting.renderTo)=="string"){ 
setting.renderTo = $(setting.renderTo); 
} 
}else{ 
return; 
} 
var _img_org_ = this.children("img"); 
_img_org_.css("cursor","pointer"); 
var __w = 0; 
var __h = 0; 
var __left = this.offset().left; 
var __top = this.offset().top; 
if(this.offsetParent()) 
{ 
__left+=this.offsetParent().offset().left; 
__top+=this.offsetParent().offset().top; 
} 
var _move_x = 0; 
var _move_y = 0; 
var _val_w = (setting.renderTo.width() / 2); 
var _val_h = (setting.renderTo.height() / 2); 
_img_org_.mouseover(function(){ 
setting.renderTo.html('<img src="' + _img_org_.attr("src") + '" style="position:absolute;" id="JNMagnifierrenderToImg" />'); 
setting.renderTo.show(); 
var timer = setInterval(function(){ 
__w = $("#JNMagnifierrenderToImg").width() / _img_org_.width(); 
__h = $("#JNMagnifierrenderToImg").height() /_img_org_.height(); 
if(__w>0){ 
clearInterval(timer); 
} 
},100); 
}); 
_img_org_.mouseout(function(){ 
setting.renderTo.hide(); 
}); 
_img_org_.mousemove(function(e){ 
_move_x =0-Math.round((document.documentElement.scrollLeft+e.clientX-__left) * __w - _val_w); 
_move_y =0-Math.round((document.documentElement.scrollTop+e.clientY-__top) * __h - _val_h); 
$("#JNMagnifierrenderToImg").css({"left":_move_x + "px ","top":_move_y + "px"}); 
}); 
} 
})(jQuery); 

