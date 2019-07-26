<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, bod_idx
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['bod_idx'])){
		$sql = "select * from board_data where idx = {$_REQUEST['bod_idx']}";
		
		$noticeRes = $DB->GetOne($sql);

		$result = (object)null;

		$result->notice = (object)null;
		$result->notice->bod_idx = $noticeRes['idx'];
		$result->notice->bod_title = urlencode($noticeRes['bod_title']);

		// 업로드시간 기준 하루까지 상태표시
		$diff = strtotime(date("Y-m-d")) - strtotime(substr($noticeRes['bod_date'], 0, 10));
		$years = floor($diff / (365*60*60*24)); 
		$months = floor(($diff - $years*365*60*624)/(30*60*60*24));
		$days = floor(($diff - $years*365*60*60*24-$months*30*60*60*24)/(60*60*24)); 
		$daycount = $years*365+$months*30+$days;
		$result->notice->bod_new = ($daycount > 1 )?'0':'1';
		$result->notice->bod_hit = $noticeRes['bod_hit'];
		$result->notice->bod_date = $noticeRes['bod_date'];
	}
	
	echo json_encode($result);
?>