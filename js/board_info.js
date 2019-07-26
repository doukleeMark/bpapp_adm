var file_idx;

$(document).on('change', '.btn-file :file', function() {
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label]);
});

$(document).ready(function(){

	$('select').select2({minimumResultsForSearch: -1});

	if($("#actionType").val()=="update"){

		if($("#fileIdx").val() != '0'){
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
	}

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
					actionType: "deleteFile",
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
		
		var bod_units = $("#bod_unit").val();
		
		if(bod_units == null){
			alert("Unit을 선택해주세요.");
			return;
		}

		var checkChar = "";
		if(bod_units != ""){
			for(var i=0;i<bod_units.length;i++){
				checkChar += "X" + bod_units[i] + ",";
			}
			$("#bod_units").val(checkChar);
		}

		$("#board_info_form").submit();
	});

	$("#board_delete").on("click", function(){

		if(confirm("삭제하시겠습니까?")){
			/* DB삭제 */
			$.post("/page/ajax/a_boardInfo.php",
				{
					actionType: "deleteBoardInfo",
					idx: $("#idx").val()
				},function(res) {					
					if(res.trim() == "success"){
						location.href = '/?page=board_list';
					}else{									
						alert("처리에 실패하였습니다.");
					}
				});	
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