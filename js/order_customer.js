$(document).ready(function(e) {
    $("a[href^='order_add.php?act=prepare']").click(function(e) {
        $.post("order.scr.php?"+$.SK(),$(this).query(),function(data){
			if($(document).SKAjax(data,true))
				$.page('order_add.php',{ajax:false});
		});
		return false;
    });
	$(".delFood").click(function(e) {
		 $.SKAuth('pin',function(){
			 $.post("order.scr.php?"+$.SK(),$(e.target).query(),function(data){
				if($("#form_order").SKAjax(data,true))
					$.page(window.location.href,{reload:true});
			});
		 });
		return false;
    });
	$("#form_order").submit(function(e) {
		e.preventDefault();
        $.SKAuth('pin',function(){
			$.post('order_customer.scr.php?'+$.SK(),$(e.target).serialize(),function(data){
				if($("#food_order").SKAjax(data,true))
					$.page('order_customer.php');
			});
		});
		return false;
    });
});