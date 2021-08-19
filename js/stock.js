$(document).ready(function(e) {
    $("#dir").dir(true,true);
	$(document).prepare().on("submit", "#form1", function(e){
		$.post("stock.scr.php?"+$.SK(), $(this).serialize(), function(data){
			if($("#form1").SKAjax(data,true))	history.back();
		});
		return false;
	});
	$("#q").searchFood({
		FoodDir:false,
		IngDir:false,
		result:'#sr',
		search:'#sb'
	});
	$("#selectDir").click(function(e) {
        $.page('stock.php?root='+$("ul.dir :checked").rootClass()+"&id="+$("ul.dir :checked").val());
    });
});