$(document).ready(function() {
	// datepicker
	$(".datepicker").datepicker({
		orientation: "up",
		format: "yyyy-mm-dd",
		autoclose: true,
		todayHighlight: true
	});

	// radio
	$("input:radio[name='co_status']").click(function() {
		if ($(this).val() == "1") {
			$(".datepicker").attr("disabled", true);
		} else {
			$(".datepicker").attr("disabled", false);
		}
	});

	// file upload
	$(".file-upload").each(function(e) {
		$(this).fileupload({
			url: $(this).attr("action"),
			type: $(this).attr("method"),
			datatype: "xml",
			dropZone: $(this).find(".dropinput"),
			add: function(event, data) {
				window.onbeforeunload = function() {
					return "You have unsaved changes.";
				};

				var file = data.files[0];
				var maxFileSize = $(this)
					.find(".file_size")
					.val();

				var fileExt = file.name.split(".").pop();

				if (!isImage(fileExt)) {
					window.onbeforeunload = null;
					alert("등록가능한 파일이 아닙니다.");
					return false;
				}

				// 사이즈체크
				if (!fileSizeCheck(file, maxFileSize)) {
					window.onbeforeunload = null;
					alert("첨부파일 사이즈는 " + maxFileSize + "MB 이내로 등록 가능합니다.");
					return false;
				}

				data.submit();

				var bar = $('<div class="progress" data-mod="' + file.size + '"><div class="bar"></div></div>');
				$(this)
					.find(".progress-bar-area")
					.html(bar);
				bar.slideDown("fast");
			},
			progress: function(e, data) {
				var percent = Math.round((data.loaded / data.total) * 100);
				$(this)
					.find('.progress[data-mod="' + data.files[0].size + '"] .bar')
					.css("width", percent + "%")
					.html(percent + "%");
			},
			fail: function(e, data) {
				window.onbeforeunload = null;
				$(this)
					.find('.progress[data-mod="' + data.files[0].size + '"] .bar')
					.css("width", "100%")
					.addClass("red")
					.html("");
			},
			done: function(event, data) {
				window.onbeforeunload = null;

				$(this)
					.find(".progress-bar-area .progress")
					.slideUp("fast");
				$(this)
					.find(".progress-bar-area")
					.html("");

				$(this)
					.find(".fileInput")
					.addClass("hidden");
				$(this)
					.find(".fileDel")
					.removeClass("hidden");

				var original = data.files[0];
				var s3Result = JSON.parse(data.result);

				$(this)
					.find(".link-filename")
					.attr("href", s3Result["tmpUrl"])
					.text(original.name);
				$(this)
					.find(".file_idx")
					.val(s3Result["s3Idx"]);
				$(this)
					.find("img")
					.attr("src", s3Result["tmpUrl"]);
			}
		});
	});

	$(".btn-delfile").on("click", function() {
		var fileGroup = $(this).closest(".file-group");

		$.post(
			"/page/ajax/a_courseInfo.php",
			{
				actionType: "deleteFile",
				idx: $("#idx").val(),
				file_idx: fileGroup.find(".file_idx").val(),
				target: fileGroup.find(".file_idx").attr("name")
			},
			function(res) {
				fileGroup.find(".fileDel").addClass("hidden");
				fileGroup.find(".fileInput").removeClass("hidden");
				fileGroup.find(".file_idx").val("0");
			},
			"json"
		);
	});

	$("#btnSubmit").on("click", function() {
		if (!$('input[name=co_title]').val().length) {
			$('input[name=co_title]').focus();
			alert('타이틀명을 입력해주세요.');
			return;
		}

		const status = parseInt($("input[name='co_status']:checked").val());
		const start_date = $("input[name='co_dt_start']").val();
		const end_date = $("input[name='co_dt_end']").val();
		if (status == 2 && !(start_date.length && end_date)) {
			alert("기간을 입력해주세요.");
			return;
		}

		const formData = new FormData();

		formData.append("actionType", $("input[name='actionType']").val());
		formData.append("idx", $("input[name='idx']").val());
		formData.append("co_title", $("input[name='co_title']").val());
		formData.append("co_desc", $("textarea[name='co_desc']").val());
		formData.append("co_status", $("input[name='co_status']:checked").val());
		formData.append("co_s3_thumb", $("input[name='co_s3_thumb']").val());
		formData.append("co_dt_start", $("input[name='co_dt_start']").val());
		formData.append("co_dt_end", $("input[name='co_dt_end']").val());

		$("#btnSubmit").prop("disabled", true);
		$.ajax({
			url: "/page/ajax/a_courseInfo.php",
			data: formData,
			processData: false,
			contentType: false,
			type: "POST",
			success: function(res) {
				$("#btnSubmit").prop("disabled", false);
				if ($("input[name='actionType']").val() == "insert") {
					location.href = "/?page=el_courseInfo&idx=" + res;
				} else {
					location.href = "/?page=el_courseList";
				}
			}
		});
	});

	if ($("#actionType").val() == "update") {
		// Content Tables
		const includedContentTable = $("#includedContentTable").DataTable({
			dom: "rt<'bottom'<'pull-right'p><'pull-left'B>i>",
			buttons: [
				{
					text: "Remove",
					className: "btn btn-danger",
					action: function(e, dt, node, config) {
						const rows = dt.rows({ selected: true }).data();

						if (rows < 1) return;

						var idxs = "";
						rows.each(function(item) {
							idxs += item.idx + ",";
						});
						idxs = idxs.substring(0, idxs.length - 1);

						$.post(
							"/page/ajax/a_courseContent.php",
							{
								actionType: "delContent",
								courseIdx: $("#idx").val(),
								idxs: idxs
							},
							function(d) {
								dt.ajax.reload();
								notIncludedContentTable.ajax.reload();
							}
						);
					}
				}
			],
			language: {
				lengthMenu: "_MENU_",
				zeroRecords: "컨텐츠를 추가해주세요.",
				info: "",
				infoEmpty: "",
				infoFiltered: ""
			},
			paging: false,
			serverSide: true,
			processing: true,
			scrollX: true,
			ajax: {
				type: "POST",
				url: "/page/ajax/a_getCourseContentList.php",
				data: {
					course_idx: $("#idx").val()
				}
			},
			select: {
				style: "os",
				blurable: true
			},
			columns: [
				{ data: "idx" }, 
				{ data: "cc_order" }, 
				{ data: "ct_code_di" }, 
				{ data: "ct_code_pd" }, 
				{ data: "ct_title" }, 
				{ data: "ct_speaker" }
			],
			ordering: false,
			columnDefs: [],
			rowReorder: {
				dataSrc: "cc_order"
			},
			rowCallback: function(row, data, index) {
				$("td", row)
					.eq(0)
					.html("<i class='fa fa-bars'></i>");
			}
		});

		includedContentTable.on("row-reorder", function(e, diff, edit) {
			const idxs = new Array();
			const order = new Array();
			for (var i = 0, ien = diff.length; i < ien; i++) {
				const rowData = includedContentTable.row(diff[i].node).data();

				idxs.push(rowData.idx);
				order.push(diff[i].newData);
			}
			$.post("/page/ajax/a_courseContent.php", {
				actionType: "changeOrder",
				courseIdx: $("#idx").val(),
				ccIdx: idxs,
				order: order
			});
		});

		var filter_code_di = 0;
		var filter_code_pd = 0;

		const notIncludedContentTable = $("#notIncludedContentTable").DataTable({
			dom: "<<'pull-left'l><'pull-left filter-group'><'pull-right'f>>rt<'bottom'<'pull-right'p><'pull-left'B>i>",
			lengthMenu: [[10, 25, 50, 100, -1], ["10", "25", "50", "100", "All"]],
			buttons: [
				{
					text: "Add",
					className: "btn btn-success",
					action: function(e, dt, node, config) {
						const rows = dt.rows({ selected: true }).data();

						if (rows < 1) return;

						var idxs = "";
						rows.each(function(item) {
							idxs += item.idx + ",";
						});
						idxs = idxs.substring(0, idxs.length - 1);

						$.post(
							"/page/ajax/a_courseContent.php",
							{
								actionType: "addContent",
								courseIdx: $("#idx").val(),
								idxs: idxs
							},
							function(d) {
								dt.ajax.reload();
								includedContentTable.ajax.reload();
							}
						);
					}
				}
			],
			language: {
				lengthMenu: "_MENU_",
				zeroRecords: "검색 결과가 없습니다.",
				info: "",
				infoEmpty: "",
				infoFiltered: ""
			},
			pagingType: "numbers",
			serverSide: true,
			processing: true,
			scrollX: true,
			ajax: {
				type: "POST",
				url: "/page/ajax/a_getCourseContentListAll.php",
				data: function(d) {
					d.course_idx = $("#idx").val();
					d.code_di = filter_code_di;
					d.code_pd = filter_code_pd;
				}
			},
			select: {
				style: "os",
				blurable: true
			},
			columns: [
				{ data: "idx" }, 
				{ data: "ct_code_di" }, 
				{ data: "ct_code_pd" }, 
				{ data: "ct_title" }, 
				{ data: "ct_speaker" }
			],
			order: [[1, "asc"]],
			columnDefs: [
				{
					targets: 0,
					visible: false
				}
			],
			initComplete: function(settings, json) {
				const $filter_group = $("#notIncludedContentTable").closest(".dataTables_wrapper").find(".filter-group");
				
				$filter_group.append("<div style='display:inline-block;margin-bottom:5px;'><span style='margin-left:20px;'>질환</span><select class='select2' name='filter-code-di' style='width:150px;margin-left:5px;'></select></div>");
				$filter_group.append("<div style='display:inline-block;margin-bottom:5px;'><span style='margin-left:20px;'>제품</span><select class='select2' name='filter-code-pd' style='width:150px;margin-left:5px;'></select></div>");
				
				// 질환 구분
				$.post("/page/ajax/a_code.php", 
				{
					actionType: "getList",
					code_group: "DI"
				},function(res) {
					var option = '<option value=0>-</option>';
					res.forEach(e => {
						option += '<option value="'+ e.idx + '">' + e.code_name + '</option>';
					});
					$filter_group.find("select[name='filter-code-di']").append(option);
					$filter_group.find("select[name='filter-code-di']").select2();
					$filter_group.find("select[name='filter-code-di']").on("change", function(){
						filter_code_di = $filter_group.find("select[name='filter-code-di'] option:selected").val();
						notIncludedContentTable.ajax.reload();
					});
				},"json");
				
				// 제품 구분
				$.post("/page/ajax/a_code.php", 
				{
					actionType: "getList",
					code_group: "PD"
				},function(res) {
					var option = '<option value=0>-</option>';
					res.forEach(e => {
						option += '<option value="'+ e.idx + '">' + e.code_name + '</option>';
					});
					$filter_group.find("select[name='filter-code-pd']").append(option);
					$filter_group.find("select[name='filter-code-pd']").select2();
					$filter_group.find("select[name='filter-code-pd']").on("change", function(){
						filter_code_pd = $filter_group.find("select[name='filter-code-pd'] option:selected").val();
						notIncludedContentTable.ajax.reload();
					});
				},"json");
			}
		});

		// Attender Tables
		const leftAttenderTable = $("#leftAttenderSelect").DataTable({
			dom: "<<'pull-left'l><'pull-right'f>>rt<'bottom'<'pull-right'p><'pull-left'B>i>",
			lengthMenu: [[10, 25, 50, 100, -1], ["10", "25", "50", "100", "All"]],
			buttons: [
				{
					text: "Remove",
					className: "btn btn-danger",
					action: function(e, dt, node, config) {
						const rows = dt.rows({ selected: true }).data();

						if (rows < 1) return;

						var idxs = "";
						rows.each(function(item) {
							idxs += item.idx + ",";
						});
						idxs = idxs.substring(0, idxs.length - 1);

						$.post(
							"/page/ajax/a_courseAttender.php",
							{
								actionType: "delAttender",
								courseIdx: $("#idx").val(),
								idxs: idxs
							},
							function(d) {
								dt.ajax.reload();
								rightAttenderTable.ajax.reload();
							}
						);
					}
				}
			],
			language: {
				lengthMenu: "_MENU_",
				zeroRecords: "인원을 추가해주세요.",
				info: "",
				infoEmpty: "",
				infoFiltered: ""
			},
			pagingType: "numbers",
			serverSide: true,
			processing: true,
			scrollX: true,
			ajax: {
				type: "POST",
				url: "/page/ajax/a_getCourseAttenderList.php",
				data: {
					course_idx: $("#idx").val()
				}
			},
			select: {
				style: "os",
				blurable: true
			},
			columns: [{ data: "idx" }, { data: "unit" }, { data: "ur_team" }, { data: "ur_name" }, { data: "ur_position" }, { data: "ur_id" }],
			order: [[0, "desc"]],
			columnDefs: [
				{
					targets: 0,
					visible: false
				}
			],
			rowCallback: function(row, data, index) {},
			drawCallback: function(settings) {
				$(this).closest(".grid").find(".count")
					.text(this.api().page.info().recordsTotal);
			}
		});

		// Attender 추가 필터
		var filter_unit = 0;
		var filter_team = '';
		var filter_position = '';

		// unit
		$.post("/page/ajax/a_userInfo.php", 
		{
			actionType: "getUnit"
		},function(res) {
			var option = '<option value=0>-</option>';
			res.forEach(e => {
				option += '<option value="'+ e.idx + '">' + e.unit_name + '</option>';
			});
			$("select[name='filter-unit']").append(option);
			$("select[name='filter-unit']").select2({
				minimumResultsForSearch: -1
			});
			$("select[name='filter-unit']").on("change", function(){
				filter_unit = $("select[name='filter-unit'] option:selected").val();
			});
		},"json");

		$("select[name='filter-unit']").on("change", function(){
			var select_unit = $("select[name='filter-unit'] option:selected").val();
			getSelectTeam(select_unit);
		});
		
		// team
		getSelectTeam('0');

		function getSelectTeam(unit) {
			$.post("/page/ajax/a_userInfo.php", 
			{
				actionType: "getTeam",
				unit: unit
			},function(res) {
				var option = '<option value="">-</option>';
				res.forEach(e => {
					option += '<option value="'+ e.ur_team + '">' + e.ur_team + '</option>';
				});
				$("select[name='filter-team']").empty();
				$("select[name='filter-team']").append(option);
				$("select[name='filter-team']").select2();
				$("select[name='filter-team']").on("change", function(){
					filter_team = $("select[name='filter-team'] option:selected").val();
				});
			},"json");
		}
	
		$("select[name='filter-position']").select2({
			minimumResultsForSearch: -1
		});
		$("select[name='filter-position']").on("change", function(){
			filter_position = $("select[name='filter-position'] option:selected").val();
		});

		$("#btnAddAttender").click(function(e){

			$.post(
				"/page/ajax/a_courseAttender.php",
				{
					actionType: "addAttender",
					courseIdx: $("#idx").val(),
					f_unit: filter_unit,
					f_team: filter_team,
					f_position: filter_position,
					f_name: $("input[name='filter-name']").val()
				},
				function(d) {
					leftAttenderTable.ajax.reload();
				}
			);
		});

		$(".btn-pushsend").click(function(e) {
			var push_type = $("input:radio[name='radio_push']:checked").val();
			var push_title = $("#push_title").val();
			var push_body = $("#push_body").val();
			var course_idx = $("#idx").val();

			if (!push_title || !push_body) {
				alert('알림 제목과 내용을 입력하세요');
				return false;
			}

			$.post("http://bp.markit.co.kr:7788/brain",
				{
					stype: 'course-send',
					ptype: push_type,
					idx: course_idx,
					ptitle: push_title,
					pbody: push_body
				},function(res) {},"json");

			alert('알림이 발송되었습니다.')
		});

	}
});

var fileSizeCheck = function(file, max) {
	var maxSize = max * 1024 * 1024;
	var fileSize = 0;

	// 브라우저 확인
	var browser = navigator.appName;

	// 익스플로러일 경우
	if (browser == "Microsoft Internet Explorer") {
		var oas = new ActiveXObject("Scripting.FileSystemObject");
		fileSize = oas.getFile(file.value).size;
	}
	// 익스플로러가 아닐경우
	else {
		fileSize = file.size;
	}

	return fileSize < maxSize;
};

var isImage = function(ext) {
	switch (ext.toLowerCase()) {
		case "jpg":
		case "jpeg":
		case "png":
			return true;
	}
	return false;
};
