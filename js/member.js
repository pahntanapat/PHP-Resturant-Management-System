$(document).ready(function(e) {
	$("#q").keyup(get);
	$(":radio[name='show']").click(get);
	$("#refresh").click(function(e) {
        $("#q").val("");
		return !get();
    }).click();
	$("#checkAll").checkAll("#acd h3>:checkbox");
	$(".buttonset").buttonset();
	$(document).on("click","#add, a[href='#edit']",edit)
	.on("submit","#dialog form",function(e) {
			/*(function f(result,msg){
				return (result)?true:$.SKAuth(msg,function(){
					$.post("member.scr.php?"+$.SK(),$("#dialog form").serialize(),function(data){
						if($("#dialog form").SKAjax(data,f)){
							$("#refresh").click();
							$("#dialog").dialog('close').remove();
						}
					});
				});
			})(false,"pin");*/
			e.preventDefault();
			$(this).postSK("member.scr.php?"+$.SK(),$("#dialog form").serialize(),function(r,m){
				if(!r) return;
				$("#refresh").click();
				$("#dialog").dialog('close').remove();
			},'pin');
			return false;
	});
	$("#member_form").submit(function(e) {
        /*(function fn(result,msg){
			return (result)?true:$.SKAuth(msg,function(){
				$.post("member.scr.php?"+$.SK(),$("#form1").serialize(),function(data){
					if($("#form1").SKAjax(data,fn))
						$("#refresh").click();
				});
			});
		})(false,"pin");*/
		$(this).postSK("member.scr.php?"+$.SK(),$("#form1").serialize(),function(r,m){
			if(r) $("#refresh").click();
		},'pin');
		return false;
    });
	function get(){
		$.get("member.scr.php",{q:$("#q").val(),show:$(":checked[name='show']").val()},function(data){
			$("#acd").html("<div>"+data+"</div>")
			$("#acd>div").accordion({
				collapsible:true,
				active:false
			}).find("h3>:checkbox").click(function(e) {
				 //$(e.target).prop("checked",!$(e.target).prop("checked"));
				 e.stopPropagation();
			});
		});
		return true;
	}
	function edit(e){
		 $.get("member.scr.php",{id:$(e.target).data('id'),t:Date()},function(data){
			$("#edit").html(data).find(".buttonset").buttonset()
				.parents("#dialog").dialog({width:'55%'})
				.find(".date").datepicker({
					changeMonth:true,
					changeYear:true,
					dateFormat:"yy-mm-dd",
					showAnim:"slideDown"
			});
		});
		return false;
	}
});