<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// IN : bp_idx, bp_state

	$result = (object)null;
	$result->result = "0";

	if(isset($_REQUEST['bp_idx']) && isset($_REQUEST['bp_state'])){

		// 구버전 CP 반려건에 대한 처리
		//if((int)$_REQUEST['bp_state'] == 3)	$_REQUEST['bp_state'] = '0';

		$sql = "select bp_user, bp_brand, bp_title, bp_content, bp_file from bp_data where idx={$_REQUEST['bp_idx']}";
		$bpRes = $DB->GetOne($sql);

		if(isset($bpRes['bp_user'])){
			$sql = "select idx, ur_id, ur_point_bp from user_data where idx={$bpRes['bp_user']}";
			$userRes = $DB->GetOne($sql);
		}

		if((int)$_REQUEST['bp_state'] >= 0 && (int)$_REQUEST['bp_state'] <= 5 && isset($bpRes['bp_user']) && isset($userRes['idx']) ){
			$sql = "update bp_data set ";
			$sql .= "bp_state = {$_REQUEST['bp_state']}, ";
			$sql .= "bp_dt_update = sysdate() ";
			$sql .= "where idx = {$_REQUEST['bp_idx']}";
			$DB->Execute($sql);

			$result->result = "1";
		}
	}	
	echo json_encode($result);
?>
