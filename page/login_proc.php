<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

	include_once(CLASS_PATH . "/user.class.lib");
	$userClass = new UserClass();
	$res = $userClass->userLogin($_POST);

	if (!$res['ur_id']) {
		echo "<script>alert('아이디 혹은 비밀번호가 일치 하지 않습니다.');history.go(-1);</script>";
	} else {
		$_SESSION['USER_ID'] = $res['ur_id'];
		$_SESSION['USER_NAME'] = $res['ur_name'];		
		$_SESSION['USER_NO'] = $res['idx'];
		$_SESSION['USER_LEVEL'] = $res['ur_level'];
		$_SESSION['USER_UNIT'] = $res['ur_unit'];

		echo "<script>location.href='/?page=user_list';</script>";
	}
	
?>