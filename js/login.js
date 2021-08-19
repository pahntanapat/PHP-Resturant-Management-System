$(document).ready(function(e) {
	$("#content").html("<div></div>")
		.find("div").html($("#loginDialog").html()).css({"width":"300px"}).addClass("floatCenter")
		.find("label, :submit, :reset").parent().addClass("floatCenter").css({"width":"68%"});
	$("#loginDialog").empty();
    $("#reload").click(function(e) {
        $("#captchaIMG").attr("src",$("#captchaIMG").attr("src").split('?')[0]+'?'+Math.random());
		$("#captcha").val('');
    }).button();
	$("#header .floatBox").hide();
	$("#loginForm").submit(function(e) {
        $.post("login.scr.php?login=login&"+$.SK(),$(this).serialize(),function(data){
			$("#loginForm").SKAjax(data,function(result,msg){
				if(result) window.location="./";
			});
		},"json");
		return false;
    });
});