$(document).ready(function(){

	/* 데이터 테이블 */
	var table = $("#listTable").DataTable({
		"dom": "<'row'<'col-xs-6'l><'pull-right'f>>rt<'bottom'p>",
		"lengthMenu": [[10, 25, 50, -1], ["10개 보기", "25개 보기", "50개 보기", "All"]],
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
			'url': '/page/ajax/a_getReportData.php',
			'data': function() {
				return;
			}
		},
		"order": [[ 0, 'desc' ]],
		"columnDefs": []
	});

	// 테이블 상단
	$('#listTable_wrapper select').select2({minimumResultsForSearch: -1});
	$('#listTable_wrapper .select2-container').removeClass("form-control input-sm");

	// 테이블 하단 table-action 추가
	$("#listTable_wrapper .bottom").prepend($("#add_table-action").html());

});
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}