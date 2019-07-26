$(document).ready(function() {

	var co_idx = $("#idx").val();
	
	/* 데이터 테이블 */
	var table = $("#listTable").DataTable({
		dom: "rt",
		language: {
			lengthMenu: "_MENU_",
			zeroRecords: " ",
			info: "Showing page _PAGE_ of _PAGES_",
			infoEmpty: "No records available",
			infoFiltered: "(filtered from _MAX_ total records)"
		},
		paging: false,
		scrollX: true,
		ajax: {
			type: "POST",
			url: "/page/ajax/a_getCourseResult.php",
			'data': {
				idx: $("#idx").val()
			}
		},
		columns: [
			{ data: "no" },
			{ data: "group" },
			{ data: "team" },
			{ data: "id" },
			{ data: "name" },
			{ data: "complete" },
			{ data: "score" },
			{ data: "detail" },
			{ data: "ur_idx" }
		],
		order: [[4, "asc"]],
		columnDefs: [
			{
				targets: [0,7,8],
				orderable: false
			}
		],
		rowCallback: function(row, data, index) {
			// 번호
			$("td", row).eq(0).html(index+1);

			// 이수여부
			if(data.complete == 'X')
				$("td", row).eq(5).html("<span class='text-danger'>X</span>");
			
			// 점수 
			if(data.score < 80 || data.complete == 'X')
				$("td", row).eq(6).html("<span class='text-danger'>" + data.score + "</span>");

			// 초기화
			$("td", row).eq(8).html("<button type='button' class='btn btn-mini btn-danger btn-reset'>초기화</button>");
		}
	});

	$("#listTable tbody").on("click", ".btn-reset", function(e) {

		if (confirm("테스트결과 및 컨텐츠 재생기록을 초기화하시겠습니까?")) {
			var data = table.row($(this).parents('tr')).data();
			
			$.post(
				"/page/ajax/a_contentInfo.php",
				{
					actionType: "reset",
					ur_idx: data.ur_idx,
					co_idx: co_idx
				},
				function(result) {
					if (!result) {
						alert("처리에 실패했습니다.");
						return false;
					}
					table.ajax.reload();
				},
				"json"
			);
		}
	});

	$("#btn-all-reset").on("click", function(e) {

		if (confirm("모든 테스트결과 및 컨텐츠 재생기록을 초기화하시겠습니까?")) {
			
			$.post(
				"/page/ajax/a_contentInfo.php",
				{
					actionType: "allReset",
					co_idx: co_idx
				},
				function(result) {
					if (!result) {
						alert("처리에 실패했습니다.");
						return false;
					}
					table.ajax.reload();
				},
				"json"
			);
		}
	});

	$("#btn-excel").on("click", function(e) {
		location.href="excel/testResult_excel.php?c=" + co_idx;
	});

});
