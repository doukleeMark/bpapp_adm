$(document).ready(function() {	
	
	// 저장된 쿠키값을 읽어오기
	var c_user_id = $.cookie("userid");
	var c_user_pwd = $.cookie("userpwd");
	
	//저장된 값이 있다면 입력 요소에 값 출력
	if( c_user_id && c_user_pwd ) {
		$("#ur_id").val(c_user_id);
		$("#ur_pw").val(c_user_pwd);

		//체크박스는 다시 체크
		$("#autoSave").prop("checked", true);
	}
	
	$("#btnLogin").on("click", function(e){
		form_submit();
	});
});

function form_submit() {

	if (!$("#ur_id").val()) {
		alert("아이디를 입력하세요");
		$("#ur_id").focus();
		return false;
	}

	if (!$("#ur_pw").val()) {
		alert("비밀번호를 입력하세요");
		$("#ur_pw").focus();
		return false;
	}

	if ($("#autoSave").is(":checked")) {
		//체크 되어있다면, 해당 정보를 1년간 유효하도록 쿠키 저장
		$.cookie("userid", $("#ur_id").val(), {"expires":365});                        
		$.cookie("userpwd", $("#ur_pw").val(), {"expires":365});
	} else {
		//체크가 해제되었다면 쿠키 삭제.
		$.removeCookie("userid");
		$.removeCookie("userpwd");
	}

	$("#form_login").submit();
}