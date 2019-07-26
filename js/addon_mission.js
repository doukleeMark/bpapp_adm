$(document).ready(function(){

	$(".btn-submit").on('click', function(e){
		$.ajax({
			type:"POST",
			url:"/page/ajax/a_mission.php",
			data:$(this).closest("form").serialize(),
			dataType:"json",
			success:function(res){
				if(res == '1'){
					alert("적용되었습니다.");
				}
			}
		});

		e.preventDefault();
		
	});
	
	$('#tab_list a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});


});
