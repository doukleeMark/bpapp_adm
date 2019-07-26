<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, bbs_idx
	
	$result->result = '-1';
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['bbs_idx'])){

		$sql = "select idx from like_data ";
		$sql .= "where lk_bbs = {$_REQUEST['bbs_idx']} AND lk_user = {$_REQUEST['ur_idx']} ";
		$likeRes = $DB->GetOne($sql);

		if(!isset($likeRes['idx'])){
			$sql = "insert into like_data(lk_bbs, lk_user) values (";
			$sql .= "{$_REQUEST['bbs_idx']}, ";
			$sql .= "{$_REQUEST['ur_idx']}) ";
			$DB->Execute($sql);

			$result->result = '1';
		}else{
			$sql = "delete from like_data ";
			$sql .= "where lk_bbs = {$_REQUEST['bbs_idx']} AND lk_user = {$_REQUEST['ur_idx']} ";
			$DB->Execute($sql);

			$result->result = '0';
		}
	}
	
	echo json_encode($result);
?>