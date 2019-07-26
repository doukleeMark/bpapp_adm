var fileIdx, bpIdx;
$(document).on('change', '.btn-file :file', function() {
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label]);
});

$(document).ready(function(){
	
	// CP 일 경우 CSS 조절
	var height_bottom;
	if($("body").find(".check-grid").length > 0)height_bottom = 289;
	else height_bottom = 239;

	$('#bp_content').css('height', $(document).height()-height_bottom);

	$("#bp_brand").on("change", function(){
		if($(this).val() != '')$(this).removeClass("placeholder_color");
		else $(this).addClass("placeholder_color");
	});
	
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
		fileIdx = $("#bp_file").val();
		bpIdx = $("#idx").val();
		var str = encodeURIComponent("정말로 삭제하시겠습니까?");
		if(isMobile.iOS()){
			document.location = "jscall://confirm|" + str;
		}else if(isMobile.Android()){
			window.android.callAndroid("confirm|"+str);
		}else if(confirm("삭제하시겠습니까?")){
			/* DB삭제 */
			$.post("/app/ajax/a_bp_write.php",
				{
					actionType: "bp_deleteFile",
					fileIdx: fileIdx,
					bpIdx: bpIdx
				},function(res) {
					if(res == '1'){
						$(".upload_grid").removeClass("hidden");
						$(".fileInfo_grid").addClass("hidden");
						$("#bp_file").val("0");
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
	});

	$("#bp_submit").on('click', function(){

		if($("#bp_brand option:selected").val()==''){
			
			var str = encodeURIComponent("브랜드를 선택해주세요.");
			if(isMobile.iOS()){
				document.location = "jscall://alert|" + str;
			}else if(isMobile.Android()){
				window.android.callAndroid("alert|"+str);
			}else{
				alert("브랜드를 선택해주세요.");	
			}
			return;
		}
		if($("#bp_title").val()==''){
			
			var str = encodeURIComponent("제목을 입력하세요.");
			if(isMobile.iOS()){
				document.location = "jscall://alert|" + str;
			}else if(isMobile.Android()){
				window.android.callAndroid("alert|" + str);
			}else{
				alert("제목을 입력하세요.");	
			}
			return;
		}
		if($("#bp_approval").is(":checked")){
			var str;
			if($("#bp_unit").val()=='7')str = encodeURIComponent("등록을 완료하시겠습니까?");
			else str = encodeURIComponent("승인요청을 진행하시겠습니까?");
			if(isMobile.iOS()){
				document.location = "jscall://confirm|" + str;
			}else if(isMobile.Android()){
				window.android.callAndroid("confirm|" + str);
			}else if(confirm("승인요청을 진행하시겠습니까?")){
				nativeJScall("commit_ok");
			}
		}else{
			nativeJScall("commit_ok");
		}
	});
});
function nativeJScall(_callStr){
	if(_callStr == "attach_del_confirm_ok"){
		$.post("/app/ajax/a_bp_write.php",
			{
				actionType: "deletebbs",
				fileIdx: fileIdx,
				bpIdx: bpIdx
			},function(res) {
				if(res == '1'){
					$(".upload_grid").removeClass("hidden");
					$(".fileInfo_grid").addClass("hidden");
				}else{
					var str = encodeURIComponent("다시 시도해주세요.");
					if(isMobile.iOS()){
						document.location = "jscall://alert|" + str;
					}else if(isMobile.Android()){
						window.android.callAndroid("alert|" + str);
					}else{
						alert("다시 시도해주세요.");	
					}
				}
			},"JSON");	
	}else if(_callStr == "commit_ok"){
		var formData = new FormData();
		
		formData.append("actionType", $("#actionType").val());
		formData.append("bp_idx", $("#idx").val());
		formData.append("bp_updater", $("#bp_updater").val());
		formData.append("bp_user", $("#bp_user").val());
		formData.append("bp_unit", $("#bp_unit").val());
		formData.append("bp_brand", $("#bp_brand").val());
		formData.append("bp_title", $("#bp_title").val());
		formData.append("dataFile", $("input[name=dataFile]")[0].files[0]);
		formData.append("bp_file", $("#bp_file").val());
		formData.append("bp_content", $("#bp_content").val());
		formData.append("bp_approval", $("#bp_approval").is(":checked")?'1':'0');
		formData.append("bp_new_fu", $("#bp_new_fu").val());
		
		$("#bp_submit").prop('disabled', true);
		
		$.ajax({
			url: "/app/ajax/a_bp_write.php",
			data: formData,
			processData:false,
			contentType:false,
			type:'POST',
			dataType:'JSON',
			success:function(res){
				if(res=="open" || res=="private"){
					if(isMobile.iOS()){		
						document.location = "jscall://" + "submit_ok|" + res;
					}else if(isMobile.Android()){
						window.android.callAndroid("submit_ok|" + res);
					}
				}else{
					var str = encodeURIComponent("다시 시도해주세요.");
					if(isMobile.iOS()){
						document.location = "jscall://alert|" + str;
					}else if(isMobile.Android()){
						window.android.callAndroid("alert|" + str);
					}else{
						alert("다시 시도해주세요.");
					}
				}
				$("#bp_submit").prop('disabled', false);
			}
		});
	}
}
