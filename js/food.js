(function($){
	$.fn.root=function(){
		return $(this).parents("ul.dir");
	};
	$.fn.rootClass=function(){
		root=$(this).root();
		return root.hasClass("ing")?"ing":(root.hasClass("food")?"food":"");
	};
	$.fn.parentID=function(){
		return $(this).parent().parent().siblings(":radio").val();
	};
	$.loadList=function(id,root,add){
		var post={parent:id,"root":root};
		if(add)		post.add=Math.random();
		var get=function(){				
			$('#'+root+id+"~label").getSK("stock.scr.php",post,function(r,data){
				$('#'+root+id+"~label").after(data);
			},'');
		};
		if($('#'+root+id+"~ul").fadeOut("fast",function(){
				$(this).remove();
				get();
			}).length==0) get();
		return $('#'+root+id);
	};
	$.fn.loadChild=function(add){
		return $.loadList($(this).val(),$(this).rootClass(),add);
	};
	$.fn.listClick=function(add){
		if($(this).siblings("ul").length==0)
			$(this).loadChild(add);
		else
			$(this).siblings("ul").toggle();
		return $(this);
	};
	$.fn.dir=function(food,ing){
		var obj=[{name:"parent", value:-Math.random()},{name:"root", value:""}];
		if(food) obj[1].value+="food";
		if(ing) obj[1].value+="ing";
		return $(this).load("stock.scr.php?"+$.param(obj));
	};
	$.fn.prepare=function(){
		return $(this).on("click","ul.dir :radio",function(e){
			$(this).listClick(false);
		}).on("dblclick","ul.dir :radio",function(e){
			$(this).loadChild(false);
		});
		return this;
	};
	$.fn.searchFood=function(option){
		option=$.extend({
				FoodDir:true,
				IngDir:true,
				FixFood:true,
				IngFood:true,
				AdjFood:true,
				linkTo:window.location.href.split('?')[0],
				result:false,
				search:false
			},option);
		option.result=(option.result==false)?$(this).after("<div></div>").next("div"):$(option.result);
		option.search=(option.search==false)?$(this).after("<button>ค้นหา</button>").next(":button"):$(option.search);
		option.my=this;
		
		var query={q:'',a:0};
		for(var k in option){
			switch(typeof option[k]){
				case "boolean":query[k]=(option[k])?1:0;break;
				case "string": query[k]=option[k];break;
			}
		}
		
		$(option.search).click(function(e){
			e.preventDefault();
			query.q=$(option.my).val();
			query.a=Math.random();
			$(option.result).getSK("stock.scr.php",query,function(r,data){
				$(option.result).html(data);
		//		if($("[data-role='page']").length>0)
		//			$(option.result).find('ol').listview();
			},'');
		});
		if($("[data-role='page']").length<=0)
			$(option.search).button({icons:{primary:"ui-icon-search"}});
		return this;
	};
})(jQuery);