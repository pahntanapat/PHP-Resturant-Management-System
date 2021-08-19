$(document).ready(function(e) {
    $("#selectDir").click(function(e) {
        $.page('order_add.php?id='+$("ul.dir :checked").val(),{});
    });
	$("#dir").dir(true,false);
	$(document).prepare().on("click",":checkbox, label",function(e){
		setTimeout(sum,40);
		var mx=$(this).is('label')?$('#'+$(this).attr('for')):this;
		if(!$(mx).prop('checked')){
			mx=$(this).parents('.selectMax');
			if($(mx).length>0)
				if($(mx).data('select')>1 && $(mx).data('select')<=$(mx).find(':checked').length)
					return false;
		}
	}).on("change","#amount",sum);
	$("#q").searchFood({
		FoodDir:false,
		IngDir:false,
		IngFood:false,
		result:'#sr',
		search:'#sb'
	});
	function sum(){
		var price=parseFloat($('#price').val());
		$(".selectMax :checked").each(function(index, element) {
			price+=parseFloat($(element).data('price'));
        });
		$('#_price').val(price.toFixed(2));
		price*=parseFloat($('#amount').val());
		$('#total').val(price.toFixed(2));
	}
	$(document).on("submit","#form1",function(e){
		e.preventDefault();
		if(parseInt($("#amount").val())<1){
			alert('กรุณากรอกจำนวน');
		}else{
			$.post("order_add.scr.php?"+$.SK(), $(this).serialize(), function(data){
				$("#form1").SKAjax(data,false);
			});
		}
		return false;
	})
});