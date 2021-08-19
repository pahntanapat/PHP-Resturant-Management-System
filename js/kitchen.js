$(document).ready(function(e) {
    $("#tabs").tabs({
		load:function(e,ui){
			$(ui.panel).find('.buttonset').buttonset();
		}
	});
	$(document).on("submit","#form1",function(e){
		$(this).postSK("kitchen.scr.php?"+$.SK(),$(this).serialize(),function(data){
			$(e.target).SKAjax(data,false);
			$("#edit").attr('href','kitchen.scr.php?id=0');
		},'');
		return false;
	}).on("click","a[href^='#act=del']",function(e){
		if(!confirm('คุณต้องการลบครัวนี้หรือไม่')) return false;
		$(this).postSK("kitchen.scr.php?"+$.SK(),$(this).query(),function(data){
			$(e.target).SKAjax(data,false);
			$("#tabs").tabs('load',$("#tabs").tabs('option','active'));
			return false;
		},'pin');
		return false;
	}).on("click","a.edit",function(e){
		$("#edit").attr('href',$(this).act());
		$("#tabs").tabs({active:2});
		return false;
	}).on("click","a.print",function(e){
		$(this).postSK('order_customer.scr.php?'+$.SK(),$(this).query(),function(data){
			$(e.target).SKAjax(data,function(r,msg){
				if(r) window.open(msg,"popup","top=50,left=50,menubar=0");
			});
		},'');
		return false;
	});
});