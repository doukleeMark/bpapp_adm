<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, bbs_idx
	
	include_once(CLASS_PATH . "/data.class.lib");
	$dataClass = new DataClass();

	$result->result = '0';
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['bbs_idx'])){

		$sql = "select * from bbs_data ";
		$sql .= "where idx={$_REQUEST['bbs_idx']} ";
		$bbsRes = $DB->GetOne($sql);

		if(isset($bbsRes['idx'])){
			// 관련 리플 삭제
			$sql = "delete from bbs_reply where bbr_parent={$bbsRes['idx']}";
			$DB->Execute($sql);
		}
		if($bbsRes['bbs_file'] > 0){
			// 첨부이미지 삭제
			$dataClass->deleteFile($bbsRes['bbs_file']);
		}

		$sql = "delete from bbs_data where idx={$_REQUEST['bbs_idx']}";
		$DB->Execute($sql);

		$result->result = '1';
	}
	
	echo json_encode($result);
?>