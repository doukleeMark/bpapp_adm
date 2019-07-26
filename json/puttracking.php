<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// IN : ur_idx, dt_idx

	$result = (object)null;

	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['dt_idx'])){

		$sql = "select idx, ur_unit, ur_team, ur_point_bp from user_data where idx={$_REQUEST['ur_idx']} ";
		$userRes = $DB->GetOne($sql);

		if(isset($userRes['idx'])){

			$sql = "insert into data_tracking(tr_user, tr_data, tr_team, tr_unit, tr_point, tr_date) values(";
			$sql .= "{$_REQUEST['ur_idx']}, ";
			$sql .= "{$_REQUEST['dt_idx']}, ";
			$sql .= "'" . addslashes($userRes['ur_team']) . "', ";
			$sql .= "{$userRes['ur_unit']}, ";
			$sql .= "0, ";
			$sql .= "sysdate()) ";
			$DB->Execute($sql);
		}
		
		$result->result = "1";
	}	

	echo json_encode($result);
?>