<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, bp_idx
	
	include_once(CLASS_PATH . "/data.class.lib");
	$dataClass = new DataClass();

	$result->result = '0';
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['bp_idx'])){

		$sql = "select * from bp_data ";
		$sql .= "where idx={$_REQUEST['bp_idx']} ";
		$bpRes = $DB->GetOne($sql);

		$sql = "select ur_unit from user_data ";
		$sql .= "where idx={$_REQUEST['ur_idx']} ";
		$userRes = $DB->GetOne($sql);

		if($bpRes['bp_user']==$_REQUEST['ur_idx'] && (int)$bpRes['bp_state'] <= 1){

			// 좋아요 정보 삭제
			$sql = "delete from bp_like where bl_bp={$bpRes['idx']}";
			$DB->Execute($sql);

			// 관련 리플 삭제
			$sql = "delete from bp_reply where bpr_parent={$bpRes['idx']}";
			$DB->Execute($sql);
			
			if($bpRes['bp_file'] > 0){
				// 첨부이미지 삭제
				$dataClass->deleteFile($bpRes['bp_file']);
			}

			$sql = "delete from bp_data where idx={$_REQUEST['bp_idx']}";
			$DB->Execute($sql);

			$result->result = '1';
		}
	}
	
	echo json_encode($result);
?>