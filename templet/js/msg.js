var is_span = [];
$(function(){
	$(".mmmmsg").hover(function(){
		$(".mmmmsg_text").css({display:'block'});
	},function(){
		$(".mmmmsg_text").css({display:'none'});
	});
	
	$(".verification").click(function(){
		$(this).attr('src','/home/index/verification.html?'+(Math.random()*1000+1));
	});
	
	$("img").click(function(){
		var fuspan = $(this).parent('.span');
		var is_this = this;
		var i = 0;
		fuspan.children().each(function(index) {  
            if(is_this !=this ){
            	i++;
            }else{
            	if($(this).attr('class') == 'img1'){
            		is_span['i1'] = i;
            		$(".h1").val(index+1);
            	}else if($(this).attr('class') == 'img2'){
            		$(".h2").val(index+1);
            		is_span['i2'] = i;
            	}else if($(this).attr('class') == 'img3'){
            		$(".h3").val(index+1);
            		is_span['i3'] = i;
            	}else if($(this).attr('class') == 'img4'){
            		$(".h4").val(index+1);
            		is_span['i4'] = i;
            	};
            	return false;
            };
        });
	});
	$("img").hover(function(){
		var fuspan = $(this).parent('.span');
		var is_this = this;
		fuspan.children().each(function(index) {  
            $(this).attr('src','/templet/images/h2.gif');
            if(!is_this){
            	$(this).attr('src','/templet/images/h1.gif');
            }else{
	            if(is_this ==this ){
	            	is_this = false;
	            };
			};
        });
	});
	$(".span").hover(function(){},function(){
		var ng = false;
		if($(this).attr('data-cmd') == 'span1'){
    		ng = is_span['i1'];
    	}else if($(this).attr('data-cmd') == 'span2'){
    		ng = is_span['i2'];
    	}else if($(this).attr('data-cmd') == 'span3'){
    		ng = is_span['i3'];
    	}else if($(this).attr('data-cmd') == 'span4'){
    		ng = is_span['i4'];
    	};
    	$(this).children().each(function(index) {  
			if(index > ng || ng == null){
				$(this).attr('src','/templet/images/h1.gif');
			}else{
				$(this).attr('src','/templet/images/h2.gif');
			};
		});
	});
	$(".submit").click(function(){
		if(is_span['i1'] == null || is_span['i2'] == null || is_span['i3'] == null ||is_span['i4'] == null){
			alert("亲,请评分(点击五星)后评论!");
			return false;
		};
		if($(".book_msg_er").val() == ''){
			alert("亲,请填写评价内容!");
			return false;
		}
		if($(".name_necheng").val() == ''){
			$(".name_necheng").val("代发货评测网友");
		}
	});
/*	$(".span1 img").hover(function(){
		var is_this = this;
		$(".span1").children().each(function(index) {  
			$(this).attr('src','/templet/images/h2.gif');
			if(!is_this){
				$(this).attr('src','/templet/images/h1.gif');
			}else{
				if(is_this ==this ){
					is_this = false;
				};
			};
		})  ;
	});
	$(".span1").hover(function(){},function(){
		var is_this = this;
		$(".span1").children().each(function(index) {  
			$(this).attr('src','/templet/images/h1.gif');
		})  ;
	});*/
	$(".shoulu_sub").click(function(){
		if($(".sli1").val() == ''){
			$(".shoulu_span1").css({color:'red'});
			$(".sli1").focus();
			return false;
		}else{
			$(".shoulu_span1").css({color:'#585858'});
		};
	
		if($(".sli2").val() == ''){
			$(".shoulu_span2").css({color:'red'});
			$(".sli2").focus();
			return false;
		}else{
			$(".shoulu_span2").css({color:'#585858'});
		};
		
		/*if($(".sli3").val() == ''){
			$(".shoulu_span3").css({color:'red'});
			$(".sli3").focus();
			return false;
		}else{
			$(".shoulu_span3").css({color:'#585858'});
		};*/
		
		 if(!$(".Logistics1").attr("checked") && !$(".Logistics2").attr("checked") && !$(".Logistics3").attr("checked") && !$(".Logistics4").attr("checked") && !$(".Logistics5").attr("checked") && !$(".Logistics6").attr("checked") && !$(".Logistics7").attr("checked")){
			 alert('联盟类型必须要选择一种或以上!');
			 return false;
		 };
		
		if($(".sli7").val() == ''){
			$(".sli7").focus();
			alert('联盟简介必须填写!');
			return false;
		};
		
	});
	
	
	
	
	
	
	
	
});