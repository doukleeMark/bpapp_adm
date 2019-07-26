<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, pw_old, pw_new
	
	$result->result = '0';
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['pw_old']) && isset($_REQUEST['pw_new'])){

		$sql = "select * from user_data ";
		$sql .= "where idx={$_REQUEST['ur_idx']} AND ur_pw = password('{$_REQUEST['pw_old']}') ";
		$userRes = $DB->GetOne($sql);

		if(isset($userRes['idx'])){

			$sql = "update user_data set ";
			$sql .= "ur_pw = password('{$_REQUEST['pw_new']}') ";
			$sql .= "where idx = {$_REQUEST['ur_idx']}";
			$DB->Execute($sql);
			
			$result->result = '1';
		}
	}
	
	echo json_encode($result);
?>