$(document).ready(function(e) {
    $(document).on("submit","form",function(e) {
        e.preventDefault();
		$.post("promotion.scr.php?"+$.SK(),$(this).serialize(),function(data){
			if($(e.target).SKAjax(data,true))
				$.page('promotion.php');
		});
		return false;
    }).on("click","a.del",function(e){
		e.preventDefault();
		$.post("promotion.scr.php?"+$.SK(),$(this).query(),function(data){
			if($(e.target).SKAjax(data,true))
				$.page('promotion.php',{reload:true});
		});
		return false;
	});
});