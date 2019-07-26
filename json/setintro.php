<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, uuid, ver
	
	if(isset($_REQUEST['ur_idx'])){

		$sql = "select * from user_data where idx='{$_REQUEST['ur_idx']}'";
		$resUser = $DB->GetOne($sql);

		$result = (object)null;
		$state = "0";

		// 로그인한 기기 앱 버전 등록
		if(isset($_REQUEST['ver']) && isset($_REQUEST['uuid'])){

			$sql = "update user_data set ";
			$sql .= "ur_dt_last = sysdate() ";
			$sql .= "where idx = {$_REQUEST['ur_idx']}";
			$DB->Execute($sql);

			$sql = "update device_data set ";
			$sql .= "dvi_appver = '{$_REQUEST['ver']}', ";

			// 토큰 업데이트
			if(isset($token) && (strlen($token) > 0))$sql .= "dvi_token = '{$token}', ";

			$sql .= "dvi_dt_last = sysdate() ";
			$sql .= "where dvi_uuid = '{$_REQUEST['uuid']}'";
			$DB->Execute($sql);

			$sql = "select * from device_data ";
			$sql .= "where dvi_uuid = '{$_REQUEST['uuid']}'";
			$deviceRes = $DB->GetOne($sql);
			
			// System Log 추가
			$sql = "insert into system_log(log_type, log_source, log_user, log_title, log_appver, log_os, log_model, log_date) values(";
			$sql .= "'login', ";
			$sql .= "'app', ";
			$sql .= "'" . $_REQUEST['ur_idx'] . "', ";
			$sql .= "'" . $_REQUEST['uuid'] . "', ";
			$sql .= "'" . $_REQUEST['ver'] . "', ";
			$sql .= "'" . $deviceRes['dvi_os'] . "', ";
			$sql .= "'" . $deviceRes['dvi_model'] . "', ";
			$sql .= "sysdate()) ";
			$DB->Execute($sql);

			$state = "1";
		}
		$result->result = $state;
	}
	
	echo json_encode($result);
?>