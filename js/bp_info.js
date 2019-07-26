$(document).on('change', '.btn-file :file', function() {
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label]);
});

$(document).ready(function(){

	$('select').select2({minimumResultsForSearch: -1});

	if($("#actionType").val()=="update"){

		// 첨부파일
		if($("#fileIdx").val() > 0){
			$.post("/page/ajax/a_file.php", {
					actionType: "fileInfo",
					fileIdx:$("#fileIdx").val()
				}, function(fileInfo){
					// 다운로드링크 추가
					$(".delInput1 a").attr("href", "/page/downloadData.php?idx=" + fileInfo['idx']);

					$(".dataFileInput").val(fileInfo['real_name'] + " (" + bytesToSize(fileInfo['file_size']) + ")");
					$(".findFile1").addClass("hidden");
					$(".delInput1").removeClass("hidden");	
				}, "json");
		}

		// 브랜드 리스트
		if($("#bp_unit").val() > 0 && $("#bp_brand").val() > 0 ){
			$.post("/page/ajax/a_getBrandOption.php", {
					actionType: "list",
					unit:$("#bp_unit").val(),
				}, function(addOption){
					$("#brand_select").html(addOption);
					$("#brand_select").val($("#bp_brand").val()).trigger('change');
				});
		}
	}

	$("#bp_unit").on("change", function(){
		$.post("/page/ajax/a_getBrandOption.php", {
				actionType: "list",
				unit:$(this).val(),
			}, function(addOption){
				$("#brand_select").html(addOption);
				$("#brand_select").val(null).trigger('change');
			});
	});

	$("#brand_select").on("change", function(){
		$("#bp_brand").val($(this).val());
	});

	/* 파일 업로드 버튼 */
	$('.btn-file :file').on('fileselect', function(event, numFiles, label) {		
		var input = $(this).parents('.input-group').find(':text'),
			log = numFiles > 1 ? numFiles + ' files selected' : label;
		
		if( input.length ) {
			input.val(log);
		} else {
			if( log ) alert(log);
		}			
	});

	/* DEL 버튼 - 데이터 내용에 있는 파일삭제 */
	$("#dataFileDel").on("click", function(){
		
		if(confirm("삭제하시겠습니까?")){
			
			/* DB삭제 */
			$.post("/page/ajax/a_file.php",
				{
					actionType: "bpDeleteFile",
					idx: $("#idx").val(),
					fileIdx: $("#fileIdx").val()
				},function(res) {
					if(res == "1"){
						$(".findFile1").removeClass("hidden");
						$(".delInput1").addClass("hidden");
						$("#fileIdx").val("0");
					}
				}, "json");	
		}
	});
	
	$("#board_submit").on('click', function(){
		$("#bp_info_form").submit();
	});

	$("#board_delete").on("click", function(){

		if(confirm("삭제하시겠습니까?")){
			/* DB삭제 */
			$.post("/page/ajax/a_bpInfo.php",
				{
					actionType: "bpDelete",
					idx: $("#idx").val()
				},function(res) {					
					if(res.trim() == "success"){
						location.href = '/?page=bp_list';
					}else{									
						alert("처리에 실패하였습니다.");
					}
				});	
		}
	});

	function textValue() {
		$('#bpr_content').bind("keyup input paste", function(){
			var max = parseInt($(this).attr('maxlength'));
			var countNum = $('.input_length');

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

		if($("#bpr_content").val()==''){
			alert("댓글을 입력하세요.");
			return;
		}
		
		var formData = new FormData();
		
		formData.append("actionType", "reply_insert");
		formData.append("bpr_parent", $("#idx").val());
		formData.append("bp_user", $("#bp_user").val());
		formData.append("bpr_user", $("#bpr_user").val());
		formData.append("bpr_content", $("#bpr_content").val());
		
		$("#reply_btn").prop('disabled', true);
		
		$.ajax({
			url: "/app/ajax/a_bp_reply.php",
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

	// 리플 삭제 버튼
	$(".reply_delete").on('click', function(){
		selectIdx = $(this).attr('reply_idx');
		if(confirm("삭제하시겠습니까?")){

			$.post("/app/ajax/a_bp_reply.php",
				{
					actionType: "reply_delete",
					replyIdx: selectIdx
				},function(res) {
					if(res == '1'){
						location.reload();
					}else{
						alert("다시 시도해주세요.");
					}
				},"JSON");
		}
	});

	/* Drop files */
	var dropzone1 = document.getElementById('dropinput1');

	dropzone1.ondrop = function(e){
		e.preventDefault();
		this.className = 'form-control';
		$("input[name=dataFile]").val("");
		$("input[name=dataFile]").replaceWith($("input[name=dataFile]").clone(true));
		$("input[name=dataFile]")[0].files = e.dataTransfer.files;
		var label = e.dataTransfer.files[0].name.replace(/\\/g, '/').replace(/.*\//, '');
		dropzone1.value = label;
	};
	
	dropzone1.ondragover = function(){
		this.className = 'form-control dragover';
		return false;
	};

	dropzone1.ondragleave = function(){
		this.className = 'form-control';
		return false;
	};
});
function bytesToSize(bytes) {
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return '0 Byte';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return Math.round(bytes / Math.pow(1024, i), 2) + '' + sizes[i];
};