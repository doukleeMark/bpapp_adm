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
				//삼항연산자 추가해도 되는지 물어보기
				$("td", row).eq(6).html("<span class='text-danger'>" + data.score ? '-':data.score + "</span>");

			//201103 추가 - 이도욱
			//테스트명
			var td7= '';
			data.score2.forEach(function(scoreData,index){
				if(index > 0){
					td7 += ",";
				}
				td7 += "<span data-toggle='tooltip'  data-container='body' data-placement='top' title='"+scoreData.title+"'>"+scoreData.score+"</span>";
			});
			$("td",row).eq(7).html(td7);
			//

			// 초기화
			$("td", row).eq(8).html("<button type='button' class='btn btn-mini btn-danger btn-reset'>초기화</button> <button type='button' class='btn btn-mini btn-primary btn-score_reset'>점수 초기화</button>");
		},
		initComplete:function(settings){
			////201104 추가 - 이도욱
			//tooltip 공식 홈페이지에서 직접 초기화를 해야한다함 전체 tooltip 초기화
			$('[data-toggle="tooltip"]').tooltip({
				delay: {
					show: 30,
					hide: 50
				}
			});
		}
	});

	$.fn.resetScore = (_data, _no) => {
		$.post('/page/ajax/a_resetTestScore.php', {
			co_idx: co_idx,
			ur_idx: _data.ur_idx,
			q_no: _no,
		}, function(x) {
			$("#listTable").DataTable().ajax.reload();
		});
	};

	$("#listTable tbody").on("click", ".btn-score_reset", function(e) {

		var data = table.row($(this).parents('tr')).data();

		$.confirm({
			title: '점수 초기화',
			content: '' +
				'<form action="" class="formName">' +
				'<div class="form-group">' +
				'<label>초기화가 필요한 테스트의 순번을 입력하세요</label>' +
				'<input type="text" placeholder="ex) 1" class="qno form-control alphanum" required />' +
				'</div>' +
				'</form>',
			buttons: {
				formSubmit: {
					text: '처리하기',
					btnClass: 'btn-blue',
					action: function () {
						var qno = this.$content.find('.qno').val();
						if(!qno){
							$.alert('테스트 번호를 입력하세요');
							return false;
						}

						$.fn.resetScore(data, qno);
					}
				},
				cancel: {
					text: '취소',
					action: function () {

					}
				},
			},
			onContentReady: function () {
				// bind to events
				var jc = this;

				var input = this.$content.find("input");
					input.keyup(function () {
						if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
							this.value = this.value.replace(/[^0-9\.]/g, '');
						}
					});

				this.$content.find('form').on('submit', function (e) {
					// if the user submits the form by pressing enter in the field.
					e.preventDefault();
					jc.$$formSubmit.trigger('click'); // reference the button and click it
				});
			}
		});
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
