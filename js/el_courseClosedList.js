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
			url: "/page/ajax/a_getCourseClosedList.php",
			data: function() {
				return;
			}
		},
		columns: [
			{ data: "idx" },
			{ data: "idx" },
			{ data: "co_title" },
			{ data: "co_dt_closed" },
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

			// result
			$("td", row).eq(4).html("<a href='/excel/courseClosedResult_excel.php?c=" + data.idx + "' class='excel'>excel</a>");
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
					actionType: "closedDeletes",
					idxs: checkIdxs
				},
				function(result) {
					table.ajax.reload();
				},
				"json"
			);
		}
	});
});
