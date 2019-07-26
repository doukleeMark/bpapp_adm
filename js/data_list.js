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
		"processing":true,
		"scrollX": true,
		"ajax": {
			'type': 'POST',
			'url': '/page/ajax/a_getDataList.php',
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
		},
		"drawCallback": function( settings ) {
			var _table = this;
			_table.$('tr').on("click", function(e){
				if(e.srcElement.tagName=='TD'){
					var data = _table.fnGetData(this);					
					location.href="/?page=data_info&idx=" + data[0];
				}
			});	
		}
	});

	// 테이블 상단
	$('#listTable_wrapper select').select2({minimumResultsForSearch: -1});
	$('#listTable_wrapper .select2-container').removeClass("form-control input-sm");

	// 테이블 하단 table-action 추가
	$("#listTable_wrapper .bottom").prepend($("#add_table-action").html());

	$('#listTable_wrapper').on('click', ".sel_deleterBtn", function() {
		if(confirm("삭제하시겠습니까?")){

			var checkDataIdxs = "";
			
			table.$('input[type="checkbox"]').each(function(){
				if($.contains(document, this)){
					if(this.checked){
						checkDataIdxs+= $(this).val() + ",";
					}
				}
			});
			
			checkDataIdxs = checkDataIdxs.substring(0, checkDataIdxs.length-1);

			$.post("/page/ajax/a_dataInfo.php", 
				{
					actionType: "ChkDeleteDataInfo",
					data_idxs: checkDataIdxs
				},function(d) {
					table.ajax.reload();
			});
		}
	});
});
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}