var move=[], add=true, adjCheck=false;
$(document).ready(function(e) {
    $("#tabs").tabs({
		beforeLoad: function(e, u){
			return u.panel.html()=="";
		},
		load:function(e,u){
			$(u.panel).find(":button, :submit, :reset").button();
			$(u.panel).find(".buttonset").buttonset();
			$(u.panel).find(".acd").accordion({
				collapsible:true,
				active:false
			});
			return true;
		}
	});
	$("#mainDir").dir(true,true);
	$(".buttonset").buttonset();
	$(document).on("keydown","input[name='add']",function(e) {
		if(e.keyCode!=13 || $(this).val().length==0)	return true;
		e.preventDefault();
		return addDir(this);
    })
//	.off("click","ul.dir:radio").off("dblclick","ul.dir :radio")
	.on("click","#dir :radio",function(e) {
		if(adjCheck && $(this).rootClass()=="ing"){
			$("#selectIng :button").show();
			$("#selectIng #ingredient").val($(this).val());
			$("#selectIng #ingName").val($(this).siblings('label').html());
			$(this).listClick(add);
		}else if(move.length>0){
			if(!confirm("คุณต้องการย้ายเข้าหมวดหมู่นี้ใช่หรือไม่?")){
				return false;
			}else if($(this).rootClass()!=move[1] || $(this).val()==move[2]){
				alert("คุณไม่สามารถวางในนี้ได้\nกรุณาเลือกหมวดหมู่ใหม่");
				return false;
			}
			move[3]=$(this).val();
			$.post("menu.scr.php?"+$.SK(),{act:"move",id:move[0],root:move[1],newprn:move[3]},function(data){
				if($("#dir").SKAjax(data,true)){
					$.loadList(move[3],move[1],add);
					$.loadList(move[2],move[1],add);
					move=[];
				}
			});
		}else{
			$(this).listClick(add);
			setMenu($(this).rootClass(),$(this).val());
		}
    }).on("dblclick","#dir :radio",recall);
	$("#dir :reset").click(recall);
	$("#dir :submit").click(function(e) {
        e.preventDefault();
		if(!confirm("คุณต้องการจะลบหมวดหมู่นี้หรือไม่?")) return false;
		var me="#dir :checked";
		$.post("menu.scr.php?"+$.SK(),
			{act:"delDir", id:$(me).val(), root:$(me).rootClass()},
			function(data){
				if($("#dir").SKAjax(data,true))
					$(me).parent().parent().siblings(':radio').loadChild();
		});
    });
	$("#move").click(function(e) {
        var me=$("#dir :checked");
		move=[
			me.val(),
			me.rootClass(),
			me.parentID(),
			-1];
		me.siblings("ul").fadeOut();
		alert("กรุณาคลิกเลือกหมวดหมู่ที่จะวาง");
    });
	$("#rename").click(function(e) {
        var name=prompt("กรุณากรอกชื่อ",$("#dir :checked~label").text());
		if(name.length>0)
			$.post("menu.scr.php?"+$.SK(),{act:"rename", id:$("#dir :checked").val(),
				"name":name, root:$("#dir :checked").rootClass()}, function(data){
					if($("#dir").SKAjax(data,true))
						$("#dir :checked").parent().parent().siblings(":radio").loadChild(add);
			});
    });
	function addDir(where){
		$.post("menu.scr.php?"+$.SK(),{act:"addDir", name:$(where).val(),
			parent:$(where).parentID(), root:$(where).rootClass()}, function(data){
			$(where).SKAjax(data,function(r,msg){
				if(r)	$.loadList(msg,$(where).rootClass(),add);
			});
		});
		return false;
	}
	function recall(e){
		 $("#dir :checked").loadChild(add);
		$("#ui-tabs-1").empty();
		 return false;
	}
	
	$(document).on("click","a[href='#edit']",function(){
		if(typeof $(this).data('table')=="undefined") return;
		loadFood($(this).data('id'),$(this).data('table'),$("#dir :checked").val());
		return false;
	}).on("submit","#foodForm, #limForm",function(e){
		e.preventDefault();
		var me=this;
		$.post("menu.scr.php?"+$.SK(),$(me).serialize(),function(data){
			$(me).SKAjax(data,false);
		});
	}).on("click",".delIng",function(e){
		$(this).parent().remove();
	}).on("click","#addIng",function(e){
		$("#selectIng").dialog('open');
	}).on("click","a[href='#del']",function(e){
		if(confirm("คุณต้องการลบเมนูอาหารนี้หรือไม่?"))
			$.post("menu.scr.php?"+$.SK(), {act:"del",table:$(this).data('table'),id:$(this).data('id')}, function(data){
				$("#dir").SKAjax(data,false);
			});
		e.preventDefault();
		return false;
	}).on("click","a[href='#directory']",function(e){
		$("#tabs").tabs({active:0});
	}).on("click","a[href='#search']",function(e){
		$("#tabs").tabs({active:3});
	});
	
	$("#selectIng").dialog({
		autoOpen:false,
		position:[100,100],
		open:function(){
			adjCheck=true;
		},
		close: function(){
			adjCheck=false;
			$(this).find("#ingredient").val("");
			$(this).find("#ingName").val("");
			$(this).find(":button").hide();
			$("#tabs").tabs({active:2});
		}
	}).find(":button").click(function(e) {
        if($("#selectIng #ingredient").val()=="") return;
		$("#ingList").append("<li>"+$("#selectIng #ingName").val()+
			" <input name=\"ing[]\" type=\"hidden\" id=\"ing[]\" value=\""+
			$("#selectIng #ingredient").val()+"\" />"+
			"<button type=\"button\" class=\"delIng\">ลบ</button></li>")
		.find(":button").button();
		$("#selectIng").dialog('close');
    }).button().hide();
	$("#q").searchFood({linkTo:'#',result:'#sr'});
	
	$(document).on("click","#sr a",function(e){
		if(adjCheck){
			if($(this).data('root')=="food") return false;
			$("#selectIng :button").show();
			$("#selectIng #ingredient").val($(this).data('id'));
			$("#selectIng #ingName").val($(this).text());
			$(this).listClick(add);
		}else if($(this).data('table').indexOf('_dir_')!=-1){
			setMenu($(this).data('root'), $(this).data('id'));
			$("#tabs").tabs({active:1});
		}else{
			setMenu($(this).data('root'),$(this).data('parent'));
			loadFood($(this).data('id'),$(this).data('table'),$(this).data('parent'));
		}
		return false;
	});
	function setMenu(root, id){
		$("#menu").attr("href","menu.scr.php?root="+root+"&id="+id+"&menu="+$.SK().split('=')[1]);
		$("#ui-tabs-1").empty();
	}
	function loadFood(id,table,parent){
		$("#edit").load("menu.scr.php?form="+Math.random()+"&table="+table+"&id="+id+"&parent="+parent,
			function(){
				$("#edit .buttonset").buttonset();
				$("#edit :button").button();
				$("#ingList").sortable({placeholder:"ui-state-highlight"}).disableSelection();
				$("#tabs").tabs({active:2});
		});
	}
});