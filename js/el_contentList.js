$(document).ready(function() {
	var contentType = { V: "VIDEO", A: "AUDIO" };

	/* 데이터 테이블 */
	var table = $("#listTable").DataTable({
		dom: "<'row'<'col-xs-6'l><'pull-right'f>>rt<'bottom'p>",
		lengthMenu: [
			[10, 25, 50, 100],
			["10개 보기", "25개 보기", "50개 보기", "100개 보기"]
		],
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
			url: "/page/ajax/a_getContentList.php",
			data: function() {
				return;
			}
		},
		columns: [
			{ data: "idx" },
			{ data: "idx" },
			{ data: "ct_title" },
			{ data: "ct_speaker" },
			{ data: "play_sec" },
			{ data: "ct_type" },
			{ data: "rating" },
			{ data: "ct_hit" },
			{ data: "ct_dt_update" },
			{ data: "idx" },
			{ data: "ct_open" }
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

			// play sec
			$("td", row)
				.eq(4)
				.html(secondToTime(data.play_sec));

			// type
			$("td", row)
				.eq(5)
				.html(
					"<span class='label bg-cons " +
						data.ct_type +
						"'>" +
						contentType[data.ct_type] +
						"</span>"
				);

			if (!isNaN(data.rating)) {
				var rating = new Number(data.rating);
				$("td", row)
					.eq(6)
					.html(rating.toFixed(1));
			}

			// result
			$("td", row).eq(9).html("<a href='/excel/contentResult_excel.php?ct=" + data.idx + "' class='excel'>excel</a>");
			
			// open
			if(data.ct_open == '1') {
				$("td", row).eq(10).html("공개");
			} else {
				$("td", row).eq(10).html("<button type='button' class='btn btn-mini btn-white btn-open'>공개하기</button>");
			}	
		}
	});

	// 리스트 선택
	$("#listTable tbody").on("click", "tr", function(e) {
		if (e.srcElement.tagName == "TD") {
			var data = table.row(this).data();
			location.href = "/?page=el_contentInfo&idx=" + data.idx;
		}
	});

	$("#listTable tbody").on("click", ".btn-open", function(e) {

		if (confirm("공개하시겠습니까?")) {
			var data = table.row($(this).parents('tr')).data();
			$.post(
				"/page/ajax/a_contentInfo.php",
				{
					actionType: "open",
					idx: data.idx
				},
				function(result) {
					if (!result) {
						alert("권한이 필요합니다.");
						return false;
					}
					table.ajax.reload();
				},
				"json"
			);
		}
	});

	// 테이블 상단 select ui
	$("#listTable_wrapper select").select2({ minimumResultsForSearch: -1 });
	$("#listTable_wrapper .select2-container").removeClass(
		"form-control input-sm"
	);

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
		if (confirm("삭제하시겠습니까?")) {
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
				"/page/ajax/a_contentInfo.php",
				{
					actionType: "deletes",
					idxs: checkIdxs
				},
				function(result) {
					if (!result) {
						alert("사용 중인 컨텐츠입니다.");
						return false;
					}
					table.ajax.reload();
				},
				"json"
			);
		}
	});
});
