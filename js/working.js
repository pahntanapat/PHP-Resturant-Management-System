$(document).ready(function(e) {
    $("#in, #out").button();
	$("#pinDialog form").prepend("<div data-role=\"fieldcontain\"><label for=\"ph\">Phone:</label><input id=\"ph\" type=\"tel\" required name=\"ph\"></div>");
	$("#in").click(function(e) {
		$("#loginDialog").Dialog(true,function(){
			$.post("working.scr.php?act=in&"+$.SK(),$("#loginDialog form").serialize(),function(data){
				$(":reset").click();
				return $("#loginDialog").SKAjax(data,true);
			});
		});
    });
	$("#out").click(function(e) {
        $("#pinDialog").Dialog(true,function(){
			$.post("working.scr.php?act=out&"+$.SK(),$("#pinDialog form").serialize(),function(data){
				$(":reset").click();
				return $("#pinDialog").SKAjax(data,true);
			});
		});
    });
});