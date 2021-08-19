$(document).ready(function(e) {
	$("#checkAll").checkAll("#accordion>h3 :checkbox");
	$("span.buttonset").buttonset();
	$("#q").keyup(q);
	$("#refresh").click(function(e) {
		$("#q").val('');
		return q();
    }).click();
	$(document).on("click","#accordion h3>:checkbox",function(e){
		e.stopPropagation();
		return true;
	}).on("click","a[href='#edit']",function(e) {
		$(this).getSK("employee_manage.scr.php",{'id':$(this).data('id'), 'time':Date()},function(r,data){
			$("#edit").html(data).find('#editDialog').dialog();
		},'');
		return false;
	}).on("submit","#editDialog form",function(e) {
		e.preventDefault();
		$(this).postSK("employee_manage.scr.php?"+$.SK(),$("#editDialog form").serialize(),
			function(r, m){
				$("#refresh").click();
				$("#pinDialog, #loginDialog").dialog('close');
				$("#editDialog").dialog('close').remove();
			},'pin');
		return false;
	});
	function setAccordion(){
		$("#accordion").accordion({
			collapsible:true,
			active:false
		}).find("h3>:checkbox").click(function(e) {
        	 e.stopPropagation();
			 return true;
        });
	}
	$("#stop_work, #del").click(function(e) {
		e.preventDefault();
		$("#form").postSK("employee_manage.scr.php?act"+($(this).is($("#del"))?"=del":"")+"&"+$.SK(),
			$("#form").find(":checked").serialize(),function(result,message){
				$("#refresh").click();
				$("#pinDialog, #loginDialog").dialog('close');
			},'pin');
		return false;
    });
	function q(){
		$("#q").getSK("employee_manage.scr.php",{q:$("#q").val()},function(r,d){$("#acd").html(d);setAccordion();},'');
		return false;
	}
});