<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, bp_idx
	
	$result->result = '-1';
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['bp_idx'])){

		$sql = "select bl_bp from bp_like ";
		$sql .= "where bl_bp = {$_REQUEST['bp_idx']} AND bl_user = {$_REQUEST['ur_idx']} ";
		$likeRes = $DB->GetOne($sql);

		if(!isset($likeRes['bl_bp'])){

			// 좋아요
			$sql = "insert into bp_like(bl_bp, bl_user) values (";
			$sql .= "{$_REQUEST['bp_idx']}, ";
			$sql .= "{$_REQUEST['ur_idx']}) ";
			$DB->Execute($sql);

			$result->result = '1';
		}else{

			// 좋아요 취소
			$sql = "delete from bp_like ";
			$sql .= "where bl_bp = {$_REQUEST['bp_idx']} AND bl_user = {$_REQUEST['ur_idx']} ";
			$DB->Execute($sql);

			$result->result = '0';
		}
	}
	
	echo json_encode($result);
?>