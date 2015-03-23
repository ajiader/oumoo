var QQ="191221838";
var QQ2="191221838";
var Telephone="13253685347";
//onmouseout='hideMsgBox(event);'
document.write("<div class='QQbox' id='divQQbox' >");
document.write("<div class='Qlist' id='divOnline'  style='display : block;'>");
document.write("<div class='t'><span id='close' onclick='hideMsgBox(event);'></span></div>");
document.write("<div class='infobox'>欧模网客服</div>");
document.write("<div class='con'>");
document.write("<ul>");

document.write("<li><img border='0' src='/templet/images/zx_qq.gif' width='100' height='31' alt='点击这里给我发消息' title='欧模网客服' id='jk_ToQQKF' style='cursor:pointer' onclick=\"javascript:window.open('http://b.qq.com/webc.htm?new=0&sid=800013519&o=mf.oumoo.com&q=7&ref='+document.location, '_blank', 'height=544, width=644,toolbar=no,scrollbars=no,menubar=no,status=no');\"/><br />企业QQ:800013519<br />客服QQ:928523819</li>");

document.write('<tr><td><li><font color=#FF0000 size="3"><b>订购热线</b></font><br /><font size="2"><b>0816-2388845</b><br /><b>400-7028008</b></font><br /><font size="2"><b></b></font></li></td></tr>');

document.write('<li><p>充值480元=4800欧币</p><p>充值800元=10000欧币</p><p>充值1800元=26000欧币</p><br />全国货到付款,亦可在线充值。送2014专辑一套');
document.write('<li><p><b>上班时间</b></p><p>周一至周日9:00-22:00</p></li>');


document.write("</ul>");
document.write("</div>");
document.write("<div class='b'></div>");
document.write("</div>");
document.write("<div id='divMenu' onclick='OnlineOver();' style='display : none;'><img src='/templet/images/kf-one.gif' class='press' alt='在线咨询'></div>");
document.write("</div>");
//<![CDATA[
var tips; var theTop = 70/*这是默认高度,越大越往下*/; var old = theTop;
function initFloatTips() {
tips = document.getElementById('divQQbox');
moveTips();
};
function moveTips() {
var tt=50;
if (window.innerHeight) {
pos = window.pageYOffset
}
else if (document.documentElement && document.documentElement.scrollTop) {
pos = document.documentElement.scrollTop
}
else if (document.body) {
pos = document.body.scrollTop;
}
pos=pos-tips.offsetTop+theTop;
pos=tips.offsetTop+pos/10;
if (pos < theTop) pos = theTop;
if (pos != old) {
tips.style.top = pos+"px";
tt=10;
//alert(tips.style.top);
}
old = pos;
setTimeout(moveTips,tt);
}
//!]]>
initFloatTips();
function OnlineOver(){
document.getElementById("divMenu").style.display = "none";
document.getElementById("divOnline").style.display = "block";
document.getElementById("divQQbox").style.width = "170px";
}
function OnlineOut(){
document.getElementById("divMenu").style.display = "block";
document.getElementById("divOnline").style.display = "none";
}
if(typeof(HTMLElement)!="undefined")    //给firefox定义contains()方法，ie下不起作用
{   
      HTMLElement.prototype.contains=function(obj)   
      {   
          while(obj!=null&&typeof(obj.tagName)!="undefind"){ //通过循环对比来判断是不是obj的父元素
   　　　　if(obj==this) return true;   
   　　　　obj=obj.parentNode;
   　　}   
          return false;   
      };   
}  

function hideMsgBox(theEvent){ //theEvent用来传入事件，Firefox的方式
　 if (theEvent){
　 var browser=navigator.userAgent; //取得浏览器属性
　 if (browser.indexOf("Firefox")>0){ //如果是Firefox
　　 if (document.getElementById('divOnline').contains(theEvent.relatedTarget)) { //如果是子元素
　　 return; //结束函式
} 
} 
if (browser.indexOf("MSIE")>0){ //如果是IE
if (document.getElementById('divOnline').contains(event.toElement)) { //如果是子元素
return; //结束函式
}
}
}
/*要执行的操作*/
document.getElementById("divMenu").style.display = "block";
document.getElementById("divOnline").style.display = "none";
}
