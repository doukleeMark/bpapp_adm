var fileIdx, calIdx;
$(document).on('change', '.btn-file :file', function() {
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label]);
});

$(document).ready(function(){
	
	$('#cal_content').css('height', $(document).height()-340);

	$("#cal_date").on("click", function(){

		var d_str;

		if($(this).val() == '')
			d_str = $.datepicker.formatDate('yy-mm-dd', new Date());
		else
			d_str = $(this).val()
		
		if(isMobile.iOS()){
			document.location = "jscall://select_date|" + d_str;
		}else if(isMobile.Android()){
			window.android.callAndroid("select_date|"+d_str);
		}
	});

	$("#cal_time").on("click", function(){

		var t_str;

		if($(this).val() == '')
			t_str = '00:00';
		else
			t_str = $(this).val()
		
		if(isMobile.iOS()){
			document.location = "jscall://select_time|" + t_str;
		}else if(isMobile.Android()){
			window.android.callAndroid("select_time|"+t_str);
		}
	});

	$("#cal_brand").on("change", function(){
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
		fileIdx = $("#cal_img").val();
		calIdx = $("#idx").val();
		var str = encodeURIComponent("정말로 삭제하시겠습니까?");
		if(isMobile.iOS()){
			document.location = "jscall://confirm|" + str;
		}else if(isMobile.Android()){
			window.android.callAndroid("confirm|"+str);
		}else if(confirm("삭제하시겠습니까?")){
			/* DB삭제 */
			$.post("/app/ajax/a_cal_write.php",
				{
					actionType: "cal_img_delete",
					fileIdx: fileIdx,
					calIdx: calIdx
				},function(res) {
					if(res == '1'){
						$(".upload_grid").removeClass("hidden");
						$(".fileInfo_grid").addClass("hidden");
						$("#cal_img").val("0");
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

	$("#cal_submit").on('click', function(){

		if($("#cal_brand option:selected").val()==''){
			var str = encodeURIComponent("브랜드를 선택하세요.");
			if(isMobile.iOS()){
				document.location = "jscall://alert|" + str;
			}
			if(isMobile.Android()){
				window.android.callAndroid("alert|"+str);
			}
			return;
		}
		if($("#cal_title").val()==''){
			var str = encodeURIComponent("제목을 입력하세요.");
			if(isMobile.iOS()){
				document.location = "jscall://alert|" + str;
			}
			if(isMobile.Android()){
				window.android.callAndroid("alert|"+str);
			}
			return;
		}
		var pattern = /20\d{2}-[0-9]{2}-[0-9]{2}$/;
		if(!pattern.test($("#cal_date").val())) {
			var str = encodeURIComponent("날짜를 입력하세요.");
			if(isMobile.iOS()){
				document.location = "jscall://alert|" + str;
			}
			if(isMobile.Android()){
				window.android.callAndroid("alert|"+str);
			}
			return;
		}
		pattern = /[0-9]{2}:[0-9]{2}$/;
		if(!pattern.test($("#cal_time").val())) {
			var str = encodeURIComponent("시간을 입력하세요.");
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
		formData.append("calIdx", $("#idx").val());
		formData.append("cal_date", $("#cal_date").val());
		formData.append("cal_time", $("#cal_time").val());
		formData.append("cal_user", $("#cal_user").val());
		formData.append("cal_title", $("#cal_title").val());
		formData.append("calFile", $("input[name=calFile]")[0].files[0]);
		formData.append("cal_img", $("#cal_img").val());
		formData.append("cal_content", $("#cal_content").val());
		formData.append("cal_unit", $("#cal_unit").val());
		formData.append("cal_brand", $("#cal_brand").val());
		
		$("#cal_submit").prop('disabled', true);
		
		$.ajax({
			url: "/app/ajax/a_cal_write.php",
			data: formData,
			processData:false,
			contentType:false,
			type:'POST',
			dataType:'JSON',
			success:function(res){				
				if(res=="1"){
					if(isMobile.iOS()){		
						document.location = "jscall://" + "schedule_submit_ok";
					}
					if(isMobile.Android()){
						window.android.callAndroid("schedule_submit_ok");
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
				$("#cal_submit").prop('disabled', false);
			}
		});
	});
});
function nativeJScall(){
	if(arguments.length <= 0)return;

	if(arguments[0] == "attach_del_confirm_ok"){
		$.post("/app/ajax/a_cal_write.php",
			{
				actionType: "cal_img_delete",
				fileIdx: fileIdx,
				calIdx: calIdx
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
	}else if(arguments[0] == "select_date"){
		$("#cal_date").val(arguments[1]);
	}else if(arguments[0] == "select_time"){
		$("#cal_time").val(arguments[1]);
	}
}
