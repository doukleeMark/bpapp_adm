var fileIdx, bbsIdx;
$(document).on('change', '.btn-file :file', function() {
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label]);
});

$(document).ready(function(){
	
	$('#bbs_content').css('height', $(document).height()-230);
	
	/* 파일 업로드 버튼 */
	$('.btn-file :file').on('fileselect', function(event, numFiles, label) {		
		var input = $(".fileName"),
			log = numFiles > 1 ? numFiles + ' files selected' : label;
		
		if( input.length ) {
			input.text(log);
		}		
	});

	// 파일 삭제 버튼
	$(".del_btn").on('click', function(){
		fileIdx = $("#bbs_file").val();
		bbsIdx = $("#idx").val();
		var str = encodeURIComponent("정말로 삭제하시겠습니까?");
		if(isMobile.iOS()){
			document.location = "jscall://confirm|" + str;
		}else if(isMobile.Android()){
			window.android.callAndroid("confirm|"+str);
		}else if(confirm("삭제하시겠습니까?")){
			/* DB삭제 */
			$.post("/app/ajax/a_bbs_write.php",
				{
					actionType: "bbs_deleteFile",
					fileIdx: fileIdx,
					bbsIdx: bbsIdx
				},function(res) {
					if(res == '1'){
						$(".upload_grid").removeClass("hidden");
						$(".fileInfo_grid").addClass("hidden");
						$("#bbs_file").val("0");
					}else{
						var str = encodeURIComponent("다시 시도해주세요.");
						if(isMobile.iOS()){
							document.location = "jscall://alert|" + str;
						}
						if(isMobile.Android()){
							window.android.callAndroid("alert|"+str);
						}
					}
				},"JSON");	
		}
	});

	$("#bbs_submit").on('click', function(){

		if($("#bbs_mode option:selected").val()==''){
			var str = encodeURIComponent("종류를 선택해주세요.");
			if(isMobile.iOS()){
				document.location = "jscall://alert|" + str;
			}
			if(isMobile.Android()){
				window.android.callAndroid("alert|"+str);
			}
			return;
		}
		if($("#bbs_title").val()==''){
			var str = encodeURIComponent("제목을 입력하세요.");
			if(isMobile.iOS()){
				document.location = "jscall://alert|" + str;
			}
			if(isMobile.Android()){
				window.android.callAndroid("alert|"+str);
			}
			return;
		}
		
		var formData = new FormData();
		
		formData.append("actionType", $("#actionType").val());
		formData.append("bbsIdx", $("#idx").val());
		formData.append("bbs_user", $("#bbs_user").val());
		formData.append("bbs_unit", $("#bbs_unit").val());
		formData.append("bbs_mode", $("#bbs_mode").val());
		formData.append("bbs_title", $("#bbs_title").val());
		formData.append("bbsFile", $("input[name=bbsFile]")[0].files[0]);
		formData.append("bbs_file", $("#bbs_file").val());
		formData.append("bbs_content", $("#bbs_content").val());
		
		$("#bbs_submit").prop('disabled', true);
		
		$.ajax({
			url: "/app/ajax/a_bbs_write.php",
			data: formData,
			processData:false,
			contentType:false,
			type:'POST',
			dataType:'JSON',
			success:function(res){				
				if(res=="1"){
					if(isMobile.iOS()){		
						document.location = "jscall://" + "submit_ok";
					}
					if(isMobile.Android()){
						window.android.callAndroid("submit_ok");
					}
				}else{
					var str = encodeURIComponent("다시 시도해주세요.");
					if(isMobile.iOS()){
						document.location = "jscall://alert|" + str;
					}
					if(isMobile.Android()){
						window.android.callAndroid("alert|"+str);
					}
				}
				$("#bbs_submit").prop('disabled', false);
			}
		});
	});
});
function nativeJScall(_callStr){
	if("attach_del_confirm_ok"){
		$.post("/app/ajax/a_bbs_write.php",
			{
				actionType: "deletebbs",
				fileIdx: fileIdx,
				bbsIdx: bbsIdx
			},function(res) {
				if(res == '1'){
					$(".upload_grid").removeClass("hidden");
					$(".fileInfo_grid").addClass("hidden");
				}else{
					var str = encodeURIComponent("다시 시도해주세요.");
					if(isMobile.iOS()){
						document.location = "jscall://alert|" + str;
					}
					if(isMobile.Android()){
						window.android.callAndroid("alert|"+str);
					}
				}
			},"JSON");	
	}
}
