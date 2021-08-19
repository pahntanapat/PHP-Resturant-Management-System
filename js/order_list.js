$(document).ready(function(e) {
    $("a.cancel").click(function(e) {
        e.preventDefault();
		if(!confirm('คุณต้องการยกเลิกรายการอาหารนี้ใช่หรือไม่?')) return false;
		$.SKAuth('pin',function(){
			var reason=encodeURIComponent(prompt('กรุณาระบุเหตุผลที่ยกเลิกเมนู'));
			$.post('order.scr.php?'+$.SK(),$(e.target).query()+"&reason="+reason,function(data){
				if($(e.target).SKAjax(data,true))
					$.page(window.location,{reload:true});
			});
		});
    });
});