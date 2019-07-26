var easytree = $("#folder_tree").easytree();

$(document).ready(function(){
	$(".select2").select2({minimumResultsForSearch: -1});	
	
	getFolderTree();

	$("#folder_add").on("click", function(){

		if(!$("#form").valid())return;		

		var formData = new FormData();

		formData.append("actionType", "insert");
		formData.append("targetFolder", $("#targetFolder").val());
		formData.append("folderName", $("#folderName").attr("value"));
		formData.append("displayOrder", $("#displayOrder").attr("value"));		
		
		$.ajax({
			url: "/page/ajax/a_dataFolder.php",
			data: formData,
			processData:false,
			contentType:false,
			type:'POST',
			success:function(res){				
				$("#folderName").val("");
				$("#displayOrder").val("");
				getFolderTree();
			}
		});
	});

	$("#folder_edit").on("click", function(){

		if(!$("#form").valid())return;
		if($("#targetFolder").val()=="0"){
			alert("Target Folder를 선택해주세요.");
			return;
		}

		var formData = new FormData();

		formData.append("actionType", "update");
		formData.append("targetFolder", $("#targetFolder").val());
		formData.append("folderName", $("#folderName").attr("value"));
		formData.append("displayOrder", $("#displayOrder").attr("value"));		
		
		$.ajax({
			url: "/page/ajax/a_dataFolder.php",
			data: formData,
			processData:false,
			contentType:false,
			type:'POST',
			success:function(res){				
				$("#folderName").val("");
				$("#displayOrder").val("");
				getFolderTree();
			}
		});
	});

	$("#targetFolder").on("change", function(){
		if($(this).val() <= 8){
			$("#folder_delete").hide();
			$("#folder_edit").hide();
		}else{
			$("#folder_delete").show();
			$("#folder_edit").show();
		}

	});

	$("#folder_delete").on("click", function(){

		if($("#targetFolder").val()=="0"){
			alert("Target Folder를 선택해주세요.");
			return;
		}
		
		var formData = new FormData();
		
		formData.append("actionType", "delete");
		formData.append("targetFolder", $("#targetFolder").val());

		if(confirm("삭제하시겠습니까?")){
			$.ajax({
				url: "/page/ajax/a_dataFolder.php",
				data: formData,
				processData:false,
				contentType:false,
				type:'POST',							
				success:function(res){
					if(res == 1){
						$("#folderName").val("");
						$("#displayOrder").val("");
						getFolderTree();
					}else if(res == 0){
						alert("하위 폴더부터 삭제해주세요.");
					}else if(res == -1){
						alert("해당 폴더에 데이터가 포함되어있습니다.");
					}

				}
			});
		}
	});

	/* 입력박스 유효검사 */
	$("#form").validate({			
		focusInvalid: false,
		ignore: "",
		rules: {
			folderName: {
				required: true,
				maxlength: 25					
			}
		},
		messages: {				
			folderName: {				
				required: '필수입력!',
				maxlength: '25자 까지 입력가능!'
			}
		},
		errorPlacement: function (error, element) { // render error placement for each input type
			var icon = $(element).parent('.input-with-icon').children('i');
			var parent = $(element).parent('.input-with-icon');
			icon.removeClass('fa fa-check').addClass('fa fa-exclamation');  
			parent.removeClass('success-control').addClass('error-control');

			$('<span class="error"></span>').insertAfter(element).append(error);
		},
		highlight: function (element) { // hightlight error inputs
			var parent = $(element).parent();
			parent.removeClass('success-control').addClass('error-control'); 
		},
		success: function (label, element) {
			var icon = $(element).parent('.input-with-icon').children('i');
			var parent = $(element).parent('.input-with-icon');
			icon.removeClass("fa fa-exclamation");
			parent.removeClass('error-control');
		}
	});	

});
function stateChanged(nodes, nodesJson) {	
	if($("#folder_tree .easytree-node").hasClass("easytree-active")){
		var t_folder = $("#folder_tree .easytree-active .easytree-title").text();
		var t_folder_id = $("#folder_tree .easytree-active")[0].id;
		var t_folder_order = $("#folder_tree .easytree-active .easytree-order").text();

		$("#targetFolder").val(t_folder_id).trigger("change");
		$("#folderName").val(t_folder);
		$("#displayOrder").val(t_folder_order);
	}

}
function getFolderTree(){
	$.post("/page/ajax/a_getDataFolder.php", function(list){		
		//트리 모델로 변환        
		var tree = getTreeModel( list, "0");		
		easytree = $("#folder_tree").easytree({
			ordering: 'ordered',
			data:tree,
			stateChanged:stateChanged
		});
		loadSelectBox();
		$("#targetFolder").val("0").trigger("change");
	}, "JSON");
}
function loadSelectBox() {
	var select = $('#targetFolder')[0];
	var currentlySelected = $('#targetFolder :selected').val();

	select.length = 0;

	var root = new Option();
	root.text = 'Root';
	root.value = '0';

	var allNodes = easytree.getAllNodes();
	addOptions(allNodes, select, 'Root', currentlySelected);
}
function addOptions(nodes, select, prefix, currentlySelected) {
	var i = 0;
	for (i = 0; i < nodes.length; i++) {

		var option = new Option();
		
		option.text = prefix + ' > ' + nodes[i].text;
		option.value = nodes[i].id;
		option.selected = currentlySelected == nodes[i].id;
		select.add(option);

		if (nodes[i].children && nodes[i].children.length > 0) {
			addOptions(nodes[i].children, select, prefix + ' > ' + nodes[i].text, currentlySelected);
		}
	}
}
//트리 모델 변환 메서드
function getTreeModel( _list, _rootId ) {

	//최종적인 트리 데이터
	var _treeModel = [];

	//전체 데이터 길이
	var _listLength = _list.length;

	//트리 크기
	var _treeLength = 0;

	//반복 횟수
	var _loopLength = 0;


	//재귀 호출
	function getParentNode ( _children, item ) {

		//전체 리스트를 탐색
		for ( var i=0, child; child = _children[i]; i++ ) {

			//부모를 찾았으면,
			if ( child.id === item.parentId ) {

				var view =
				{
					"isFolder" : true,
					"isExpanded" : true,
					"id" : item.id,
					"text" : item.label,
					"order" : item.order,
					"children" : []
				};

				//현재 요소를 추가하고
				child.children.push(view);

				//트리 크기를 반영하고,
				_treeLength++;

				//데이터상에서는 삭제
				_list.splice( _list.indexOf(item), 1 );

				//현재 트리 계층을 정렬
				child.children.sort(function(a, b)
				{ 
					return a < b ? -1 : a > b ? 1 : 0;  
				});

				break;
			}

			//부모가 아니면,
			else
			{
				if( child.children.length )
				{					
					getParentNode( child.children, item );
				}
			}

		}
	}


	//트리 변환 여부 + 무한 루프 방지
	while ( _treeLength != _listLength && _listLength != _loopLength++ ) {

		//전체 리스트를 탐색
		for ( var i=0, item; item = _list[i]; i++ ) {

			//최상위 객체면,
			if ( item.parentId === _rootId ) {

				var view =
				{
					"isFolder" : true,
					"isExpanded" : true,
					"id" : item.id,
					"text" : item.label,
					"order" : item.order,
					"children" : []
				};

				//현재 요소를 추가하고,
				_treeModel.push(view);

				//트리 크기를 반영하고,
				_treeLength++;

				//데이터상에서는 삭제
				_list.splice(i, 1);

				//현재 트리 계층을 정렬
				_treeModel.sort( function ( a, b )
				{ 
					return a < b ? -1 : a > b ? 1 : 0;  
				});

				break;
			}

			//하위 객체면,
			else {
			//
				getParentNode( _treeModel, item );
			}
		}
	}

	return _treeModel;
};