<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// IN : ur_idx

	$result = (object)null;

	if(isset($_REQUEST['ur_idx'])){

		$year = date("Y");
		$month = (int)date("m");
		$week = toWeekNum(time());

		$sql = "select * from collect_data ";
		$sql .= "where col_user={$_REQUEST['ur_idx']} AND col_year = {$year} AND col_month={$month} AND col_week = {$week} limit 1 ";
		$collectRes = $DB->GetOne($sql);

		if(!isset($collectRes['idx'])){
			$sql = "insert into collect_data(col_user, col_year, col_month, col_week, col_dt_last) values(";
			$sql .= "{$_REQUEST['ur_idx']}, ";
			$sql .= "{$year}, ";
			$sql .= "{$month}, ";
			$sql .= "{$week}, ";
			$sql .= "sysdate()) ";
			$DB->Execute($sql);

			$sql = "select * from collect_data ";
			$sql .= "where col_user={$_REQUEST['ur_idx']} AND col_year = {$year} AND col_month={$month} AND col_week = {$week} limit 1 ";
			$collectRes = $DB->GetOne($sql);
		}

		$result->collect = (object)null;

		$result->collect = (object)null;
		$result->collect->col_value1 = $collectRes['col_value1'];
		$result->collect->col_value2 = $collectRes['col_value2'];
		$result->collect->col_value3 = $collectRes['col_value3'];
		$result->collect->col_value4 = $collectRes['col_value4'];
		$result->collect->col_value5 = $collectRes['col_value5'];
		$result->collect->col_year = $collectRes['col_year'];
		$result->collect->col_month = $collectRes['col_month'];
		$result->collect->col_week = $collectRes['col_week'];
		$result->collect->col_dt_last = $collectRes['col_dt_last'];
			
	}	

	echo json_encode($result);

?>