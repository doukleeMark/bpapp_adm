<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, unit
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['unit'])){
		$sql = "select * from user_data where idx='{$_REQUEST['ur_idx']}'";
		$resUser = $DB->GetOne($sql);

		$sql = "select * from board_data where ";
		$sql .= "bod_units like '%X{$_REQUEST['unit']},%' ";
		$sql .= "order by idx desc";
		
		$noticeRes = $DB->GetAll($sql);

		$result = (object)null;
		
		$result->notice = (object)null;
		
		$noticeArr = array();
		for($i=0;$i<count($noticeRes);$i++) {
			$temp = (object)null;
			$temp->bod_idx = $noticeRes[$i]['idx'];
			$temp->bod_title = urlencode($noticeRes[$i]['bod_title']);

			// 업로드시간 기준 하루까지 상태표시
			$diff = strtotime(date("Y-m-d")) - strtotime(substr($noticeRes[$i]['bod_date'], 0, 10));
			$years = floor($diff / (365*60*60*24)); 
			$months = floor(($diff - $years*365*60*624)/(30*60*60*24));
			$days = floor(($diff - $years*365*60*60*24-$months*30*60*60*24)/(60*60*24)); 
			$daycount = $years*365+$months*30+$days;
			$temp->bod_new = ($daycount > 1 )?'0':'1';
			$temp->bod_hit = $noticeRes[$i]['bod_hit'];
			$temp->bod_date = $noticeRes[$i]['bod_date'];
			
			$noticeArr[] = $temp;
		}
		$result->notice = $noticeArr;
	}
	
	echo json_encode($result);
?>