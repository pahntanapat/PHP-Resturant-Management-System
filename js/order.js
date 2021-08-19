$(document).ready(function(e) {
    $(":radio[name='style']").click(function(e) {
        $("label[for='table']").html(($(this).is("#style_0")?'โต๊ะที่':'ชื่อลูกค้า')+' :');
		$("input[name='table']").attr('placeholder','กรุณากรอก'+($(this).is("#style_0")?'หมายเลขโต๊ะ':'ชื่อลูกค้า'));
    });
	$("#new").submit(function(e) {
        e.preventDefault();
		$.post("order.scr.php?"+$.SK(),$(this).serialize(),function(data){
			$("#new").SKAjax(data,function(r,msg){
				if(r){
					$("a[href|='order_customer.php']").attr('href',"order_customer.php?id="+msg);
					$.page('#dialogOK',{rel:'dialog'});
				}else{
					alert(msg);
				}
			});
		});
    });
});