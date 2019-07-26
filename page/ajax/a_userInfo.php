<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	include_once(CLASS_PATH . "/user.class.lib");

	$userClass = new UserClass();

	if($_POST['actionType'] == "statusToggle"){
		$status_temp = ($_POST['status'] == "1")?"0":"1";		
		$sql = "update user_data set ";
		$sql .= "ur_state={$status_temp} ";
		$sql .= "where idx={$_POST['idx']}";
		$DB->Execute($sql);
	}else if($_POST['actionType'] == "getDeviceIdx"){
		$sql = "select idx from device_data ";
		$sql .= "where dvi_user={$_POST['idx']}";
		$output = $DB->GetAll($sql);
		echo json_encode($output);
	}else if($_POST['actionType'] == "checkCntDevice"){
		$sql = "select count(idx) as cnt from device_data ";
		$sql .= "where dvi_user={$_POST['idx']}";
		$output = $DB->GetOne($sql);
		echo json_encode($output['cnt']);
	}else if($_POST['actionType'] == "deleteDeviceInfo"){
		$output = $userClass->deviceDelete($_POST['dIdx']);
		echo json_encode($output);
	}else if($_POST['actionType'] == "deleteUsers"){
		$userClass->usersDelete($_POST['ur_idxs']);
		echo json_encode("1");
	}else if($_POST['actionType'] == "sendPoint"){
		$result = "0";
		if(isset($_POST['targetEmail']) && isset($_POST['eventName']) && isset($_POST['eventPoint'])){

			$id = addslashes($_POST['targetEmail']);
			$event = addslashes($_POST['eventName']);
			$point = addslashes($_POST['eventPoint']);

			// 유저 정보 가져오기
			$sql = "select * from user_data where ur_id='{$id}'";
			$userRes = $DB->GetOne($sql);

			if(isset($userRes['idx']) && strlen($event) != 0 && strlen($point) != 0 ){
				
				$totalPoint = $userRes['ur_point_bp'] + $point;
				
				$sql = "update user_data set ";
				$sql .= "ur_point_bp = {$totalPoint} where idx = {$userRes['idx']}";
				$DB->Execute($sql);

				$sql = "insert into point_data(ptd_user, ptd_role, ptd_event, ptd_point, ptd_total, ptd_date) values(";
				$sql .= "'" . $userRes['idx'] . "', ";
				$sql .= "'adm_push', ";
				$sql .= "'". $event . "', ";
				$sql .= "'" . $point . "', ";
				$sql .= "'" . $totalPoint . "', ";
				$sql .= "sysdate()) ";
				$DB->Execute($sql);

				$result = "1";
			}

		}
		echo json_encode($result);
		return;	
	}else if($_POST['actionType'] == "getUnit"){
		
		$sql = "select * from unit_data ";
		$output = $DB->GetAll($sql);
		
		echo json_encode($output);
	}else if($_POST['actionType'] == "getTeam"){
		
		$sql = "select DISTINCT(ur_team) from user_data ";
		$sql .= "where ur_hidden = 0 and ur_state = 1 ";
		if (isset($_POST['unit']) && (int)$_POST['unit'] > 0) $sql .= "and ur_unit = {$_POST['unit']} ";
		$sql .= "order by ur_team ";
		$output = $DB->GetAll($sql);
		
		echo json_encode($output);
	}
	
?>