$(document).ready(function(){

	$("#scopeSettingBtn").on('click', function(e){
		
		$.ajax({
			type:"POST",
			url:"/page/ajax/a_setPointSetting.php",
			data:$("#form_scope").serialize(),
			dataType:"json",
			success:function(res){
				if(res == '1')alert("적용되었습니다.")
			}
		});
		e.preventDefault();
	});

	$("#roleSettingBtn").on('click', function(e){
		
		$.ajax({
			type:"POST",
			url:"/page/ajax/a_setPointSetting.php",
			data:$("#form_role").serialize(),
			dataType:"json",
			success:function(res){
				if(res == '1')alert("적용되었습니다.")
			}
		});
		e.preventDefault();
	});

	$('#tab-4 a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
});
