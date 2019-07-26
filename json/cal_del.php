<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, cal_idx
	
	include_once(CLASS_PATH . "/data.class.lib");
	$dataClass = new DataClass();

	$result->result = '0';
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['cal_idx'])){

		$sql = "select * from cal_data ";
		$sql .= "where idx={$_REQUEST['cal_idx']} ";
		$calRes = $DB->GetOne($sql);

		if($calRes['cal_img'] > 0){
			// 첨부이미지 삭제
			$dataClass->deleteFile($calRes['cal_img']);
		}

		$sql = "delete from cal_data where idx={$_REQUEST['cal_idx']}";
		$DB->Execute($sql);

		$result->result = '1';
	}
	
	echo json_encode($result);
?>