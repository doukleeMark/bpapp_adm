<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, dt_idx, favor
	
	$result->result = '0';
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['dt_idx']) && isset($_REQUEST['favor'])){

		if($_REQUEST['favor'] == "1"){
			// 추가
			$sql = "select * from favor_data ";
			$sql .= "where fav_user={$_REQUEST['ur_idx']} AND fav_data={$_REQUEST['dt_idx']} ";
			$favRes = $DB->GetOne($sql);

			if(!isset($favRes['idx'])){
				// 없으면 추가
				$sql = "insert into favor_data(fav_user, fav_data) values(";
				$sql .= "'{$_REQUEST['ur_idx']}', ";
				$sql .= "'{$_REQUEST['dt_idx']}') ";
				$DB->Execute($sql);
			}


		}else if($_REQUEST['favor'] == "0"){
			// 삭제
			$sql = "delete from favor_data ";
			$sql .= "where fav_user={$_REQUEST['ur_idx']} AND fav_data={$_REQUEST['dt_idx']} ";
			$DB->Execute($sql);
		}
		$result->result = '1';

	}
	
	echo json_encode($result);
?>