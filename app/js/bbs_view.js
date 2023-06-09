var selectIdx;
$(document).ready(function(){

	function textValue() {
		$('.content textarea').bind("keyup input paste change", function(){
			var max = parseInt($(this).attr('maxlength'));
			var countNum = $(this).parent().find('.input_length');

			if($(this).val().length >= max){ 
				$(this).blur(); 
				$(this).val($(this).val().substr(0, max)); 
			}

			//브라우저 구분
			if ($.browser.webkit) {
				countNum.html($(this).val().replace(/\r(?!\n)|\n(?!\r)/g, "\r\n").length + '/' + max);
			} else {
				countNum.html($(this).val().length + '/' + max);
			} 
		});
	}textValue();

	$("#reply_btn").on('click', function(e){

		e.stopPropagation();
		if($("#bbr_content").val()==''){
			var str = encodeURIComponent("댓글을 입력하세요.");
			if(isMobile.iOS()){
				document.location = "jscall://alert|" + str;
			}
			if(isMobile.Android()){
				window.android.callAndroid("alert|"+str);
			}else {
				alert("댓글을 입력하세요.");
			}
			return;
		}
		
		var formData = new FormData();
		
		formData.append("actionType", "reply_insert");
		formData.append("bbr_parent", $("#idx").val());
		formData.append("bbs_user", $("#bbs_user").val());
		formData.append("bbs_mode", $("#bbs_mode").val());
		formData.append("bbr_user", $("#bbr_user").val());
		formData.append("bbr_content", $("#bbr_content").val());
		
		$("#reply_btn").prop('disabled', true);
		
		$.ajax({
			url: "/app/ajax/a_bbs_reply.php",
			data: formData,
			processData:false,
			contentType:false,
			type:'POST',
			dataType:'JSON',
			success:function(res){
				if(res=="1"){
					location.reload();
				}
			}
		});

	});

	$("#reply_btn_update").on('click', function(e){

		e.stopPropagation();
		if($("#bbr_content_update").val()==''){
			var str = encodeURIComponent("댓글을 입력하세요.");
			if(isMobile.iOS()){
				document.location = "jscall://alert|" + str;
			}
			if(isMobile.Android()){
				window.android.callAndroid("alert|"+str);
			}else {
				alert("댓글을 입력하세요.");
			}
			return;
		}
		
		var formData = new FormData();
		
		formData.append("actionType", "reply_update");
		formData.append("bbr_content_update", $("#bbr_content_update").val());
		formData.append("reply_idx", $("#reply_idx").val());
		
		$("#reply_btn").prop('disabled', true);
		
		$.ajax({
			url: "/app/ajax/a_bbs_reply.php",
			data: formData,
			processData:false,
			contentType:false,
			type:'POST',
			dataType:'JSON',
			success:function(res){
				if(res=="1"){
					location.reload();
				}
			}
		});

	});

	// 리플 수정 버튼
	$(".reply_update").on('click', function(){
		$("#reply_idx").val($(this).attr('reply_idx'));
		$("#bbr_content_update").val($(this).parent().find(".reply_content").text());
		$("#bbr_content_update").trigger("change");
		$(".bbs_reply_bg").show();
	});

	// 리플 수정 취소 배경
	$(".bbs_reply_bg").on('click', function(){
		$(".bbs_reply_bg").hide();
	});

	$(".bbs_reply").on('click', function(e){
		e.stopPropagation();
	});

	// 리플 삭제 버튼
	$(".reply_delete").on('click', function(){
		selectIdx = $(this).attr('reply_idx');
		var str = encodeURIComponent("정말로 삭제하시겠습니까?");
		if(isMobile.iOS()){
			document.location = "jscall://confirm|" + str;
		}else if(isMobile.Android()){
			window.android.callAndroid("confirm|"+str);
		}else if(confirm("삭제하시겠습니까?")){

			$.post("/app/ajax/a_bbs_reply.php",
				{
					actionType: "reply_delete",
					replyIdx: selectIdx,
					bbs_user: $("#bbs_user").val()
				},function(res) {
					if(res == '1'){
						location.reload();
					}else{
						if(isMobile.iOS()){		
							document.location = "jscall://alert|다시 시도해주세요";
						}else if(isMobile.Android()){
							window.android.callAndroid("alert|다시 시도해주세요");
						}
					}
				},"JSON");
		}
	});
});
function nativeJScall(_callStr){
	if("reply_del_confirm_ok"){
		$.post("/app/ajax/a_bbs_reply.php",
			{
				actionType: "reply_delete",
				replyIdx: selectIdx,
				bbs_user: $("#bbs_user").val()
			},function(res) {
				if(res == '1'){
					location.reload();
				}else{
					var str = encodeURIComponent("다시 시도해주세요.");
					if(isMobile.iOS()){
						document.location = "jscall://alert|" + str;
					}else if(isMobile.Android()){
						window.android.callAndroid("alert|"+str);
					}
				}
			},"JSON");
	}
}
