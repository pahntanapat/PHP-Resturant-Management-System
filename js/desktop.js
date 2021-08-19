$(document).ready(function(e) {
    $("#nav>div").buttonset();
	$("div[data-role=controlgroup]").buttonset();
	$(".dialog").dialog({
		autoOpen:false,
		modal:true
	});
	$(document).tooltip();
	$('.btnset').buttonset();
});