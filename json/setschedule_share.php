<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// IN : ur_idx, cal_idx, share_idx

	$result = (object)null;

	$result->result = "0";

	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['cal_idx']) && isset($_REQUEST['share_idx'])){
		
		$sql = "select * from cal_data where idx='{$_REQUEST['cal_idx']}'";
		$calRes = $DB->GetOne($sql);

		$sql = "select * from user_data where idx='{$_REQUEST['ur_idx']}'";
		$userRes = $DB->GetOne($sql);
		
		if(isset($userRes['idx'])) {

			$shareList = explode(",", $_REQUEST['share_idx']);

			for($i = 0 ; $i < count($shareList) ; $i++){
				
				$sql = "select idx, ur_id from user_data where idx='{$shareList[$i]}'";
				$targetRes = $DB->GetOne($sql);

				if(isset($targetRes['ur_id']) && filter_var($targetRes['ur_id'], FILTER_VALIDATE_EMAIL)){

					// 푸시
					exec("/usr/bin/php -q /home/bpapp/public_html/app/fcm/push_start.php share,{$calRes['idx']},{$targetRes['idx']} {$userRes['idx']} > /dev/null &");
				}
			}

			$result->result = "1";
		} else {
			$result->result = "0";
		}
	}

	echo json_encode($result);

?>
