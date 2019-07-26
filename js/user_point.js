$(document).ready(function(){

	var level_txt = ["-", "-", "MR", "PM", "-", "-", "-", "-", "-", "ADMIN", " "];
	var level_color = ["", "", "bg-yellow", "bg-blue", "", "", "", "", "", "bg-orange", "bg-dark"];
	
	/* 데이터 테이블 */
	var table = $("#listTable").DataTable({
		"dom": "<'row'<'col-xs-6'l><'pull-right'f>>rt<'bottom'p>",
		"lengthMenu": [[10, 25, 50, 100], ["10개 보기", "25개 보기", "50개 보기", "100개 보기"]],
		"language": {
			"lengthMenu": "_MENU_",
			"zeroRecords": " ",
			"info": "Showing page _PAGE_ of _PAGES_",
			"infoEmpty": "No records available",
			"infoFiltered": "(filtered from _MAX_ total records)"
		},
		"serverSide": true,
		"processing":true,
		"scrollX": true,
		"ajax": {
			'type': 'POST',
			'url': '/page/ajax/a_getPointList.php',
			'data': function() {
				return;
			}
		},
		"order": [[ 1, 'desc' ]],
		"columnDefs": [
			{
				"targets": [0],
				"orderable": false
			}
		],
		"rowCallback": function( row, data, index ) {
			// 체크박스
			$('td', row).eq(0).html("<input type='checkbox' value='" + data[0] + "'/>");
			
			$('td', row).eq(5).html("<span class='label bg-cons " 
				+ level_color[data[5]] + "'>" + level_txt[data[5]] + "</span>");

			// 포인트 표시
			$('td', row).eq(7).html(numberWithCommas(data[7]));
			$('td', row).eq(8).html(numberWithCommas(data[8]));
		}
	});

	// 테이블 상단
	$('#listTable_wrapper select').select2({minimumResultsForSearch: -1});
	$('#listTable_wrapper .select2-container').removeClass("form-control input-sm");

	// 테이블 하단 table-action 추가
	$("#listTable_wrapper .bottom").prepend($("#add_table-action").html());

	$("#pointSendBtn").on("click", function(){

		if($("#targetEmail").val().length == 0){
			alert("이메일을 입력해주세요.");
			return;
		}
		if($("#eventName").val().length == 0){
			alert("이벤트 내용을 입력해주세요.");
			return;
		}
		if($("#eventPoint").val().length == 0 || $("#eventPoint").val() == 0){
			alert("포인트를 입력해주세요.");
			return;
		}

		$.post("/page/ajax/a_userInfo.php", 
			{
				actionType: "sendPoint",
				targetEmail: $("#targetEmail").val(),
				eventName: $("#eventName").val(),
				eventPoint: $("#eventPoint").val()
			},function(res) {
				if(res == '1'){
					table.ajax.reload();
					$("#targetEmail").val('');
					$("#eventName").val('');
					$("#eventPoint").val('');
					$("#targetEmail").focus();
				}else{
					alert("입력된 정보가 잘못되었습니다.");
				}
		}, "JSON");
	});
});
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}