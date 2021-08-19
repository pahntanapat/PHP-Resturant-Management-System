(function($){
	$.fn.id=function(){
		return $(this).attr('id');
	};
	$.SK=function(){
		return "ajax="+Math.random();
	};
	$.fn.act=function(){
		if($(this).is('form'))	return $(this).attr('action');
		else if($(this).is('a')) return $(this).attr('href');
		else	return this;
	};
	$.fn.SKAjax=function(data,callback){
		if(typeof data =="string"){
			try{
				data=$.parseJSON(data);
			}catch(e){
				return callback(true,data);
			}
		}
		var recall=false;
		for(var k in data.action){
			var json=data.action[k];
			switch(json.act){
				case "alert":
					alert(json.message);continue;
				case "redirect":
					window.location=json.url;continue;
				case "eval":
					$.globalEval(json.script);continue;
				case "setText":
					$(json.selector).text(json.message);continue;
				case "setHTML":
					$(json.selector).html(json.message);continue;
				case "setVal":
					$(json.selector).val(json.message);continue;
				case "resetForm":
					$(this).find(":reset").click();
				case "reloadCAPTCHA":
					 $(this).find("#reload").click();continue;
				case "scrollTo":
					$("html, body").animate({scrollTop:$(json.selector).offset().top},"fast");continue;
				case "focus":
					$(json.selector).focus();continue;
				case "recall":
					recall=json.call;continue;
				/*
				case "":
					continue;
				*/
				default:
			}
		}
		if(recall) return callback(recall);
		if($.isFunction(callback))
			return callback(data.result,data.message);
		else if(callback==true)
			return data.result;
		return callback;
	};
	$.fn.Dialog=function(noSession,callback){
		var me=$(this);
		$(this).find("form").unbind('submit').submit(function(e) {
        	$(me).dialog('close');
			var serial=$(this).serializeArray();
			serial.push({"name":"no_session","value":(noSession)?1:0});
			$.post($(this).attr("action")+"?"+$.SK(),serial,function(data){
				$("#reload").click();
				return  $(e.target).SKAjax(data,callback);
		//		return $(e.target).SKAjax(data, function(r,msg){
		//			if(r){
		//				if(typeof callback=='function') return callback();
		//				else return true;
		//			}else{
		//				$.SKAuth(msg,callback);
		//			}
		//		});
		//		if($(this).SKAjax(data,true)){
		//			if(typeof callback=="function") return callback();
		//		}else return $(me).Dialog(noSession,callback);
			},"json");
			return false;
   		});
		if($(this).data('role')=="page" || $(this).data('role')=="dialog") //Detect if use jQuery Mobile
			$.page('#'+$(this).id(),{rel:"dialog"});
		else	$(this).dialog('open');
		return this;
	};
	$.SKAuth=function(msg,func){
		if(msg.toLowerCase().search('log in')!=-1) $("#loginDialog").Dialog(true,func);
		else if(msg.toLowerCase().search('pin')!=-1) $("#pinDialog").Dialog(true,func);
	//	else return func();
	//	return false;
		else return func(true,msg);
	};
	$.fn.ajaxSK=function(setting,require){
		var me=this;
		if(typeof require=="undefined"||require==null) require="";
		return (function f(result,msg){
			if(result==null){
				var m=msg.toLowerCase();
				if(!(m.search('log in')==-1 && m.search('login')==-1)) $("#loginDialog").Dialog(true,function(r,m){return f(null,m);});
				else if(msg.toLowerCase().search('pin')!=-1) $("#pinDialog").Dialog(true,function(r,m){return f(null,m);});	
				else	return $.ajax($.extend({},setting,{
					success:function(data){
						return $(me).SKAjax(data,f);
					}
				}));
			}else return $.isFunction(setting.success)?setting.success(result,msg):result;
		})(null, require);
	};
	$.fn.getSK=function(url,data,success,require){
		return $(this).ajaxSK({
			"url":url,
			"data":data,
			"success":success
		},require);
	};
	$.fn.postSK=function(url,data,success,require){
		return $(this).ajaxSK({
			type:"POST",
			"url":url,
			"data":data,
			"success":success
		},require);
	};
	$.fn.checkAll=function(checkbox){
		return $(this).click(function(e) {
            if($(this).data("checked")===1){
				$(checkbox).prop("checked",false);
				$(this).data("checked",0).find("span").text("เลือกทั้งหมด");
			}else{
				$(checkbox).prop("checked",true);
				$(this).data("checked",1).find("span").text("ไม่เลือกทั้งหมด");
			}
			return false;
        }).button().data("checked",0).find("span").text("เลือกทั้งหมด");
	};
	$.query=function(str){
		return str.replace('#','').split('?',2)[1];
	};
	$.fn.query=function(){
		return $.query($(this).act());
	};
}(jQuery));
$(document).ready(function(e) {
    $("#reload").click(function(e) {
        $("#captchaIMG").attr("src",$("#captchaIMG").attr("src").split('?')[0]+'?'+Math.random());
		$("#captcha").val('');
    }).button();
	(function rcs(time){	
		if(typeof time=="boolean")
			time=(time)?20:300;
		return setTimeout(function(){
			$.get('order_customer.scr.php',$.SK(),function(data){
				$(document).SKAjax(data,rcs);
			});
		}, time*1000);
	})(5);
});