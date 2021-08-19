$(document).ready(function(e) {
    $("#tabs").tabs();
	$(":button,:reset,:submit").button();
	$('.date').datepicker({
		changeMonth:true,
		changeYear:true,
		dateFormat:"yy-mm-dd",
		showAnim:"slideDown"
	});
	function cusList(e){
		if(typeof e =="object")
			if($(e.target).is(':button, :reset'))
				$('#cq').val('');
		$.get('cashier.scr.php',{"cq":$('#cq').val(),"r":Math.random()},function(data){
			$('#cusList').html(data);
		});
	}
	cusList();
	$("#cq").keyup(cusList).next(':button').click(cusList);
	$('#oldBill').submit(function(e) {
        e.preventDefault();
		$(this).postSK($(this).act()+'?_'+$.SK(),$(this).serialize(),function(data){
			$('#billList').html(data);
		});
    });
	$(document).on("click","a.confirm",function(e) {
        e.preventDefault();
		if(!confirm("คุณต้องการจะ"+$(this).data('title')+"ใช่หรือไม่?"))	return false;
		$(this).getSK($(this).act(),{ajax:Math.random()},function(r,msg){},"log in");
    }).on("click","a.cash",function(e){
		e.preventDefault();
		$.getSK("cashier.scr.php",$(this).query(),function(r,data){},'pin');
	});
});