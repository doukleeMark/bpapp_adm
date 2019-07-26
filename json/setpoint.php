<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// IN : ur_idx, ptr_code

	$result = (object)null;
	$result->result = "1";

	/*
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['ptr_code'])){

		// 유저 정보 가져오기
		$sql = "select * from user_data where idx='{$_REQUEST['ur_idx']}'";
		$userRes = $DB->GetOne($sql);

		// 포인트 정보 가져오기
		$sql = "select * from point_role where ptr_code='{$_REQUEST['ptr_code']}'";
		$pointRoleRes = $DB->GetOne($sql);

		if(isset($userRes['idx']) && isset($pointRoleRes['ptr_code'])){
			
			$totalPoint = $userRes['ur_point_bp'] + $pointRoleRes['ptr_point'];
			
			$sql = "update user_data set ";
			$sql .= "ur_point_bp = {$totalPoint} where idx = {$userRes['idx']}";
			$DB->Execute($sql);

			$sql = "insert into point_data(ptd_user, ptd_role, ptd_event, ptd_point, ptd_total, ptd_date) values(";
			$sql .= "'" . $userRes['idx'] . "', ";
			$sql .= "'". $pointRoleRes['ptr_code'] . "', ";
			$sql .= "'". $pointRoleRes['ptr_title'] . "', ";
			$sql .= "'" . $pointRoleRes['ptr_point'] . "', ";
			$sql .= "'" . $totalPoint . "', ";
			$sql .= "sysdate()) ";
			$DB->Execute($sql);

			// System Log 추가
			$sql = "insert into system_log(log_type, log_source, log_user, log_title, log_date) values(";
			$sql .= "'point', ";
			$sql .= "'app', ";
			$sql .= "'" . $userRes['idx'] . "', ";
			$sql .= "'포인트 발생 : ". $pointRoleRes['ptr_title'] . "', ";
			$sql .= "sysdate()) ";
			$DB->Execute($sql);

			$result->result = "1";
		}

	}
	*/

	echo json_encode($result);
?>
