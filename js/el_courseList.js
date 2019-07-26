$(document).ready(function() {
	
	/* 데이터 테이블 */
	var table = $("#listTable").DataTable({
		dom: "<'row'<'col-xs-6'l><'pull-right'f>>rt<'bottom'p>",
		lengthMenu: [[10, 25, 50, 100], ["10개 보기", "25개 보기", "50개 보기", "100개 보기"]],
		language: {
			lengthMenu: "_MENU_",
			zeroRecords: " ",
			info: "Showing page _PAGE_ of _PAGES_",
			infoEmpty: "No records available",
			infoFiltered: "(filtered from _MAX_ total records)"
		},
		serverSide: true,
		processing: true,
		scrollX: true,
		ajax: {
			type: "POST",
			url: "/page/ajax/a_getCourseList.php",
			data: function() {
				return;
			}
		},
		columns: [
			{ data: "idx" },
			{ data: "idx" },
			{ data: "co_title" },
			{ data: "co_status" },
			{ data: "com_cnt" },
			{ data: "cnt" },
			{ data: "co_dt_update" },
			{ data: "idx" }
		],
		order: [[1, "desc"]],
		columnDefs: [
			{
				targets: [0],
				orderable: false
			}
		],
		rowCallback: function(row, data, index) {
			// 체크박스
			$("td", row)
				.eq(0)
				.html(
					"<div class='checkbox check-default'>" +
						"<input id='cb_" +
						data.idx +
						"' type='checkbox' value='" +
						data.idx +
						"'/>" +
						"<label for='cb_" +
						data.idx +
						"'></label></div>"
				);

			// status
			var status_html;
			if (data.co_status == -2) {
				status_html = "<span class='label bg-cons bg-dark'>DONE</span>";
			} else if (data.co_status == -1) {
				status_html = "<span class='label bg-cons bg-blue'>INFINITE</span>";
			} else if (data.co_status == 0) {
				status_html = "<span class='label bg-cons bg-red'>D-day</span>";
			} else {
				status_html = "<span class='label bg-cons bg-red'>D-" + data.co_status + "</span>";
			}
			$("td", row)
				.eq(3)
				.html(status_html);

			// progress
			var percent = 0;
			var progress_html = "<div class='progress '>" + "<div class='progress-bar progress-bar-success animate-progress-bar' data-percentage=''></div></div>";
			if (data.cnt > 0) percent = (data.com_cnt / data.cnt) * 100;
			$("td", row).eq(4).html(progress_html).find(".progress-bar").css("width", percent + "%");
			// result
			$("td", row).eq(7).html("<a href='/?page=el_courseResult&idx=" + data.idx + "' class='excel'>진행결과</a>");
		}
	});

	// 리스트 선택
	$("#listTable tbody").on("click", "tr", function(e) {
		if (e.srcElement.tagName == "TD") {
			var data = table.row(this).data();
			location.href = "/?page=el_courseInfo&idx=" + data.idx;
		}
	});

	// 테이블 상단 select ui
	$("#listTable_wrapper select").select2({ minimumResultsForSearch: -1 });
	$("#listTable_wrapper .select2-container").removeClass("form-control input-sm");

	// 테이블 하단 table-action add
	$("#listTable_wrapper .bottom").prepend($("#add_table-action").html());

	// checkbox all select
	$("#listTable_wrapper").on("click", ".checkall", function() {
		if ($(this).is(":checked")) {
			table.$('input[type="checkbox"]').attr("checked", true);
		} else {
			table.$('input[type="checkbox"]').attr("checked", false);
		}
	});

	// Delete
	$("#listTable_wrapper").on("click", ".btn_delete", function() {
		if (confirm("삭제하시겠습니까?\n삭제시 복구가 불가능합니다.")) {
			var checkIdxs = "";

			table.$('input[type="checkbox"]').each(function() {
				if ($.contains(document, this)) {
					if (this.checked) {
						checkIdxs += $(this).val() + ",";
					}
				}
			});

			checkIdxs = checkIdxs.substring(0, checkIdxs.length - 1);
			$.post(
				"/page/ajax/a_courseInfo.php",
				{
					actionType: "deletes",
					idxs: checkIdxs
				},
				function(result) {
					table.ajax.reload();
				},
				"json"
			);
		}
	});

	// close
	$("#listTable_wrapper").on("click", ".btn_closed", function() {
		var checkCnt = 0;
		var checkIdx = '';
		table.$('input[type="checkbox"]').each(function() {
			if ($.contains(document, this)) {
				if (this.checked) {
					checkCnt++;
					checkIdx = $(this).val();
				}
			}
		});
		
		if(checkCnt != 1){
			alert("1개의 과정을 선택해주세요.");
			return;
		}
		
		if (confirm("과정을 종료하시겠습니까?")) {
			$.post(
				"/page/ajax/a_courseInfo.php",
				{
					actionType: "close",
					idx: checkIdx
				},
				function(result) {
					table.ajax.reload();
				},
				"json"
			);
		}
	});
});
