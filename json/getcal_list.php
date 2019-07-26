<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : date, idx, ur_idx, unit
	
	$day = array("SUNDAY","MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY");
	
	$result = (object)null;

	if(isset($_REQUEST['date']) && isset($_REQUEST['unit'])){
			
		$sql = "select * from user_data where idx='{$_REQUEST['ur_idx']}'";
		$userRes = $DB->GetOne($sql);

		$result->cal_list = (object)null;
		
		$sql = "select c.*, u.ur_id, u.ur_name from cal_data c, user_data u ";
		$sql .= "where c.cal_user = u.idx AND c.cal_date = '{$_REQUEST['date']}' AND c.cal_unit = '{$_REQUEST['unit']}' ";
		$sql .= "order by c.cal_time asc ";
		$calRes = $DB->GetAll($sql);

		$eventArr = array();
		for($i=0;$i<count($calRes);$i++) {
			$temp = (object)null;
			$temp->cal_idx = $calRes[$i]['idx'];
			$temp->cal_user = $calRes[$i]['cal_user'];
			$temp->cal_user_name = urlencode($calRes[$i]['ur_name']);
			$temp->cal_user_mail = urlencode($calRes[$i]['ur_id']);
			$temp->cal_title = urlencode($calRes[$i]['cal_title']);
			$temp->cal_mail = $calRes[$i]['cal_mail'];
			$temp->cal_date = str_replace('-', '.',$calRes[$i]['cal_date']);
			$d = date_create($calRes[$i]['cal_date']);
			$temp->cal_day = $day[date_format($d, "w")];
			$temp->cal_time = $calRes[$i]['cal_time'];
			$temp->cal_dt_create = str_replace('-', '.',$calRes[$i]['cal_dt_create']);
			
			$eventArr[] = $temp;
		}	
		$result->cal_list = $eventArr;

	}else if(isset($_REQUEST['idx'])){

		$sql = "select c.*, u.ur_id, u.ur_name, br.brand_name from cal_data c, user_data u, brand_data br ";
		$sql .= "where c.cal_user = u.idx AND c.idx = '{$_REQUEST['idx']}' AND br.idx = c.cal_brand ";
		$calRes = $DB->GetOne($sql);

		$result->cal = (object)null;
		$result->cal->cal_idx = $calRes['idx'];
		$result->cal->cal_user = $calRes['cal_user'];
		$result->cal->cal_user_name = urlencode($calRes['ur_name']);
		$result->cal->cal_user_mail = urlencode($calRes['ur_id']);
		$result->cal->cal_brand = urlencode($calRes['brand_name']);
		$result->cal->cal_title = urlencode($calRes['cal_title']);
		$result->cal->cal_content = urlencode($calRes['cal_content']);
		$result->cal->cal_mail = $calRes['cal_mail'];
		$result->cal->cal_date = str_replace('-', '.',$calRes['cal_date']);
		$d = date_create($calRes['cal_date']);
		$result->cal->cal_day = $day[date_format($d, "w")];
		$result->cal->cal_time = $calRes['cal_time'];
		$result->cal->cal_dt_create = str_replace('-', '.',$calRes['cal_dt_create']);
			
	}
	
	echo json_encode($result);
?>