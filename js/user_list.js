$(document).ready(function(){

	var level_txt = ["-", "-", "USER", "MANAGER", "-", "-", "-", "-", "-", "ADMIN", "MASTER"];
	var level_color = ["", "", "bg-yellow", "bg-blue", "", "", "", "", "", "bg-orange", "bg-dark"];
	
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
			'url': '/page/ajax/a_getUserList.php',
			'data': function() {
				return;
			}
		},
		
		"order": [[ 1, 'desc' ]],
		"columnDefs": [
			{
				"targets": [0],
				"orderable": false
			},
			{
				"targets": [6],
				"visible": false,
				"searchable": false
			}
		],
		"rowCallback": function( row, data, index ) {
			// 체크박스
			$('td', row).eq(0).html("<input type='checkbox' value='" + data[0] + "'/>");
			
			$('td', row).eq(5).html("<span class='label bg-cons " 
				+ level_color[data[5]] + "'>" + level_txt[data[5]] + "</span>");


			let openTypeHtml;
			if(data[7] == 0) openTypeHtml = '<div class="label label-warning">내부</div>'
			else openTypeHtml = '<div class="label label-success">외부</div>'
			$("td", row).eq(6).html(openTypeHtml);

		},
		"drawCallback": function( settings ) {
			var _table = this;
			_table.$('tr').on("click", function(e){
				if(e.srcElement.tagName=='TD'){
					var data = _table.fnGetData(this);					
					location.href="/?page=user_info&idx=" + data[0];
				}
			});	
		}
	});

	// 테이블 상단
	$('#listTable_wrapper select').select2({minimumResultsForSearch: -1});
	$('#listTable_wrapper .select2-container').removeClass("form-control input-sm");

	// 테이블 하단 table-action 추가
	$("#listTable_wrapper .bottom").prepend($("#add_table-action").html());

	// 테이블 내 상품 삭제 버튼
	$("#listTable_wrapper tbody").on("click", ".status_btn", function() {
		var data = table.row( $(this).parents("tr") ).data();
		$.post("/page/ajax/a_userInfo.php", 
			{
				actionType: "statusToggle",
				idx: data[0],
				status: data[8]
			},function(d) {
				table.ajax.reload();
			});
	} );

	$('#listTable_wrapper').on('click', ".sel_deleterBtn", function() {

		if(confirm("삭제하시겠습니까?")){

			var checkUserIdxs = "";
			
			table.$('input[type="checkbox"]').each(function(){
				if($.contains(document, this)){
					if(this.checked){
						checkUserIdxs+= $(this).val() + ",";
					}
				}
			});
			
			checkUserIdxs = checkUserIdxs.substring(0, checkUserIdxs.length-1);

			$.post("/page/ajax/a_userInfo.php", 
				{
					actionType: "deleteUsers",
					ur_idxs: checkUserIdxs
				},function(d) {
					table.ajax.reload();
			});
		}
	});
});
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}