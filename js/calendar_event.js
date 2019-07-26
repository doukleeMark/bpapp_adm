$(document).on('change', '.btn-file :file', function() {
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label]);
});
$(document).ready(function(){

	$('select').select2({minimumResultsForSearch: -1});

	var datepicker_object;

	// 브랜드 리스트
	if($("#cal_unit").val() > 0){
		$.post("/page/ajax/a_getBrandOption.php", {
				actionType: "list",
				unit:$("#cal_unit").val(),
			}, function(addOption){
				$("#cal_brand").html(addOption);
				$("#cal_brand").val(null).trigger('change');
			});
	}

	$("#cal_unit").on("change", function(){
		$.post("/page/ajax/a_getBrandOption.php", {
				actionType: "list",
				unit:$("#cal_unit").val(),
			}, function(addOption){
				$("#cal_brand").html(addOption);
			});
	});

	var today = $.datepicker.formatDate('yy-mm-dd', new Date());

	// 켈린더에 이벤트 표시 및 datepicker 플러그인 적용
	getEventDate();

	// 오늘 이벤트 리스트 가져오기
	getEventList(today);

	// 이벤트 선택
	$("#eventList").on('click', 'li', function(){

		if($(this).hasClass("blank"))return;

		var idx = $(this).attr("cal_idx");
		$("#eventList li").removeClass("actionEvent");
		$(this).addClass("actionEvent");

		$.post("/page/ajax/a_calendar.php", {
				actionType: "getEventDetail",
				idx: idx
			}, function(eventDetail){
				// 수정 이벤트 디테일
				setEventDetail(eventDetail);
		}, "JSON");
	});

	$("#event_submit").on('click', function(e){

		if($.trim($("#cal_title").val()).length == 0){
			alert("제목을 입력하세요.");
			return;
		}
		
		// 선택 날짜
		var sel_date = $("#selectDate").val();

		var formData = new FormData();
		
		formData.append("actionType", $("#actionType").val());
		formData.append("idx", $("#idx").val());
		formData.append("cal_unit", $("#cal_unit").val());
		formData.append("cal_brand", $("#cal_brand").val());
		formData.append("cal_title", $("#cal_title").val());
		formData.append("cal_content", $("#cal_content").val());
		formData.append("cal_date", $("#selectDate").val());

		formData.append("fileIdx", $("#fileIdx").val());
		formData.append("dataFile", $("input[name=dataFile]")[0].files[0]);
		$.ajax({
			type:"POST",
			url:"/page/ajax/a_calendar.php",
			data:formData,
			processData:false,
			contentType:false,
			success:function(){
				getEventDate(sel_date);
				getEventList(sel_date);
				
				// 신규 이벤트 디테일 초기화 
				setEventDetail();
			}
		});

		e.preventDefault();
		
	});

	$("#event_delete").on("click", function(){

		if(confirm("삭제하시겠습니까?")){
			
			// 선택 날짜
			var sel_date = $("#selectDate").val();
			
			/* DB삭제 */
			$.post("/page/ajax/a_calendar.php",
				{
					actionType: "delete",
					idx: $("#idx").val()
				},function(res){
					getEventDate(sel_date);
					getEventList(sel_date);
					
					// 신규 이벤트 디테일 초기화 
					setEventDetail();
				});
		}
	});

	$("#dataFileDel").on("click", function(){
		
		if(confirm("삭제하시겠습니까?")){
			
			/* DB삭제 */
			$.post("/page/ajax/a_file.php",
				{
					actionType: "cal_deleteFile",
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
	
	function getEventDate(){
		var sel_date;
		if(arguments.length == 1){
			sel_date = new Date(arguments[0]);
		}else{
			sel_date = new Date();
		}
		$.post("/page/ajax/a_calendar.php", {
				actionType: "getEventDate"
			}, function(eventDate){		
				
				if(datepicker_object != null){
					datepicker_object.datepicker("destroy");
				}
				datepicker_object = $('#datepicker').datepicker({
					defaultDate: sel_date,
					dateFormat: 'yy-mm-dd',
					inline: true,
					prevText: "<",
					nextText: ">",
					dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
					onSelect: function (dateText, inst) {
						// 선택된 날짜 이벤트 리스트 가져오기
						getEventList(dateText);

						// 신규 이벤트 디테일 초기화
						setEventDetail();
					},
					beforeShowDay: function (dateStr, inst) {
						// 일자 선택되기전 이벤트 발생
						var addStr = '';
						if ($.inArray($.datepicker.formatDate('yy-mm-dd', dateStr), eventDate.user) >= 0) {
							addStr = 'user_highlighted ';
						}
						if ($.inArray($.datepicker.formatDate('yy-mm-dd', dateStr), eventDate.admin) >= 0) {
							addStr += 'admin_highlighted';
						}
						return [true, addStr, ''];
					},
					onChangeMonthYear: function (year, month, inst) {}
				});

				setEventDetail();
				
		}, "JSON");
	}

	function getEventList(_date){
		// 날짜 표시
		$(".dateText").text(_date);
		$("#selectDate").val(_date);

		$.post("/page/add/add_eventList.php", {
				date: _date,
			}, function(addData){
				$("#eventList").html('');
				$("#eventList").append(addData);
		});
	}

	function setEventDetail(){

		// 파일 초기화 
		$("input[name=dataFile]").val("");
		$("input[name=dataFile]").replaceWith($("input[name=dataFile]").clone(true));
		$("#dropinput1").val("");
		$("#fileIdx").val("0");

		if(arguments.length == 0){
			$(".actionText").text("NEW");
			$(".actionText").removeClass("edit");
			$("#actionType").val("insert");
			$("#idx").val("");

			$("#radio_off").prop("checked", true);
			$("#cal_title").val("");
			$("#cal_content").val("");
			$("#cal_content").text("");
			
			// 파일 폼
			$(".findFile1").removeClass("hidden");
			$(".delInput1").addClass("hidden");

			$("#event_delete_grid").addClass("hidden");

		}else{
			$(".actionText").text("EDIT");
			$(".actionText").addClass("edit");
			$("#actionType").val("update");
			$("#idx").val(arguments[0]['idx']);

			// 유닛 선택
			$("#cal_unit").val(arguments[0]['cal_unit']).trigger("change");

			$("#cal_brand").val(arguments[0]['cal_brand']).trigger("change");

			$("#cal_title").val(arguments[0]['cal_title']);
			$("#cal_content").val(arguments[0]['cal_content']);

			// 파일
			if(arguments[0]['cal_img'] > 0){
				$("#fileIdx").val(arguments[0]['cal_img']);

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
			}else{
				$(".findFile1").removeClass("hidden");
				$(".delInput1").addClass("hidden");
			}

			$("#event_delete_grid").removeClass("hidden");
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

});
$( window ).resize(function() {
	// Event List Title width resize
	$(".eventList_grid li b").width($(".eventList_grid").width()-55);
});
function bytesToSize(bytes) {
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return '0 Byte';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return Math.round(bytes / Math.pow(1024, i), 2) + '' + sizes[i];
};