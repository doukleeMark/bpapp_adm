$(document).ready(function() {
	
	var filter_unit = 0;
	var filter_team = '';
	
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
	},"json");

	$("select[name='filter-unit']").on("change", function(){
		filter_unit = $("select[name='filter-unit'] option:selected").val();
		getSelectTeam(filter_unit);
	});
	
	// team
	getSelectTeam('0');

	$("select[name='filter-team']").on("change", function(){
		filter_team = $("select[name='filter-team'] option:selected").val();
	});

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
		},"json");
	}

	$("#btnExcel").click(function(e){
		var name = $("input[name='filter-name']").val();
		var uri =  encodeURI("/excel/contentResult_filter_excel.php?name=" + name + "&team=" + filter_team + "&unit=" + filter_unit);
		location.href = uri;
	});

});

