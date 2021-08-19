(function($){
	$.page=function(to,option){
		if(typeof option!=='undefined')
			if(option.ajax)
				return $(":mobile-pagecontainer").pagecontainer('change',to,option);
		window.location=to;
		return $(":mobile-pagecontainer");
	};
}(jQuery));
$(document).ready(function(e) {
    
});
