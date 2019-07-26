/* data_info.js */
var file_idx;
var easytree = $("#folder_tree").easytree();

$(document).on('change', '.btn-file :file', function() {
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label]);
});

$(document).ready(function(){

	$('select').select2({minimumResultsForSearch: -1});

	if($("#actionType").val()=="insert"){
		getFolderTree("0");
	}else if($("#actionType").val()=="update"){
		getFolderTree($("#folderIdx").val());
	}

	/* 파일 업로드 버튼 */
	$('.btn-file :file').on('fileselect', function(event, numFiles, label) {		
		var input = $(this).parents('.input-group').find(':text'),
			log = numFiles > 1 ? numFiles + ' files selected' : label;
		
		if( input.length ) {
			input.val(log);
		} else {
			if( log ) alert(log);
		}			
	});

	$("#data_submit").on('click', function(){

		var dt_folders = $("#dt_folder").val();
		if(dt_folders==null){
			alert("폴더를 선택해주세요.");
			return;
		}

		var formData = new FormData();
		
		formData.append("actionType", $("#actionType").val());
		
		var checkChar = "";
		if(dt_folders != ""){
			for(var i=0;i<dt_folders.length;i++){
				checkChar += "X" + dt_folders[i] + ",";
			}
		}
		formData.append("dt_folders", checkChar);
		formData.append("dt_title", $("#dt_title").val());
		if($("#actionType").val()=="insert"){

			if(!$("input[name=dataFile]")[0].files[0]){
				alert("파일을 선택해주세요.");
				return;
			}

			// 사이즈체크
			var maxSize  = 300 * 1024 * 1024;
			var fileSize = 0;

			// 브라우저 확인
			var browser=navigator.appName;

			// 익스플로러일 경우
			if (browser=="Microsoft Internet Explorer")
			{
				var oas = new ActiveXObject("Scripting.FileSystemObject");
				fileSize = oas.getFile( $("input[name=dataFile]")[0].files[0].value ).size;
			}
			// 익스플로러가 아닐경우
			else
			{
				fileSize = $("input[name=dataFile]")[0].files[0].size;
			}

			if(fileSize > maxSize)
			{
				alert("첨부파일 사이즈는 300MB 이내로 등록 가능합니다.");
				return;
			}

			formData.append("dt_type", $("input[name=dt_type]:radio:checked").val());
			formData.append("dataFile", $("input[name=dataFile]")[0].files[0]);
		}else{
			formData.append("idx", $("#idx").val());	
		}
		formData.append("push_send", $("#push_checkbox").is(':checked')?'1':'0');
		
		$("#data_submit").prop('disabled', true);
		$.ajax({
			url: "/page/ajax/a_dataInfo.php",
			data: formData,
			processData:false,
			contentType:false,
			type:'POST',
			success:function(res){				
				result = res.trim().split(",");				
				if(result[0]=="error"){
					alert(result[1]);
					
				}else if(result[0]=="success"){
					location.href = '/?page=data_list';
				}
				$("#data_submit").prop('disabled', false);
			}
		});
	});

	/* DEL 버튼 */
	$("#data_delete").on("click", function(){

		if(confirm("삭제하시겠습니까?")){
			/* DB삭제 */
			$.post("/page/ajax/a_dataInfo.php",
				{
					actionType: "deleteDataInfo",
					idx: $("#idx").val(),
				},function(res) {					
					if(res.trim() == "success"){
						location.href = '/?page=data_list';
					}else{									
						alert("처리에 실패하였습니다.");
					}
				});	
		}

	});

	/* Drop files */
	var dropzone1 = document.getElementById('dropinput1');

	dropzone1.ondrop = function(e){
		e.preventDefault();
		this.className = 'form-control';
		$("input[name=dataFile]").val("");
		$("input[name=dataFile]").replaceWith($("input[name=dataFile]").clone(true));
		$("input[name=dataFile]")[0].files = e.dataTransfer.files;
		var label = e.dataTransfer.files[0].name.replace(/\\/g, '/').replace(/.*\//, '');
		dropzone1.value = label;
	};
	
	dropzone1.ondragover = function(){
		this.className = 'form-control dragover';
		return false;
	};

	dropzone1.ondragleave = function(){
		this.className = 'form-control';
		return false;
	};

});
function getFolderTree(_folder){
	$.post("/page/ajax/a_getDataFolder.php", function(list){		
		//트리 모델로 변환        
		var tree = getTreeModel( list, "0");		
		easytree = $("#folder_tree").easytree({
			allowActivate:false,
			ordering: 'ordered',
			data:tree
		});
		loadSelectBox();
		$("#dt_folder").select2({minimumResultsForSearch: -1});
		
		if(_folder != "0"){
			var folders = _folder.replace(/X/g, "").split(',');
			folders.splice(folders.indexOf(""), 1);
			$("#dt_folder").val(folders).trigger("change");
		}
		else
			$("#dt_folder").val(null).trigger("change");
		
	}, "JSON");
}
function loadSelectBox() { 
	var select = $('#dt_folder')[0];
	var currentlySelected = $('#dt_folder :selected').val();
	select.length = 0; // clear select box

	var root = new Option();
	root.text = 'Root';
	root.value = '0';

	var allNodes = easytree.getAllNodes();
	addOptions(allNodes, select, '', currentlySelected);
}
function addOptions(nodes, select, prefix, currentlySelected) {
	var i = 0;
	for (i = 0; i < nodes.length; i++) {

		if(nodes[i].depth > 0){
			var option = new Option();
			
			option.text = prefix + ' > ' + nodes[i].text;
			option.value = nodes[i].id;
			option.selected = currentlySelected == nodes[i].id;
			select.add(option);
		}

		if (nodes[i].children && nodes[i].children.length > 0) {

			if(nodes[i].depth > 0){
				addOptions(nodes[i].children, select, prefix + ' > ' + nodes[i].text, currentlySelected);
			}else{
				addOptions(nodes[i].children, select, prefix + nodes[i].text, currentlySelected);
			}
			
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
					"depth" : item.depth,
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
					return a.order < b.order ? -1 : a.order > b.order ? 1 : 0;  
				});

				break;
			}

			//부모가 아니면,
			else
			{
				if( child.children.length )
				{					
					getParentNode( child.children, item );
					//getParentNode.callee( child.children, item );
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
					"depth" : item.depth,
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
					return a.order < b.order ? -1 : a.order > b.order ? 1 : 0;  
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
function bytesToSize(bytes) {
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return '0 Byte';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return Math.round(bytes / Math.pow(1024, i), 2) + '' + sizes[i];
};