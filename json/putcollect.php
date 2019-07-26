<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// IN : ur_idx, col_value1, col_value2, col_value3, col_value4, col_value5

	$result = (object)null;

	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['col_value1']) && isset($_REQUEST['col_value2']) && isset($_REQUEST['col_value3']) && isset($_REQUEST['col_value4']) && isset($_REQUEST['col_value5'])){

		$year = date("Y");
		$month = (int)date("m");
		$week = toWeekNum(time());

		$sql = "select * from collect_data ";
		$sql .= "where col_user={$_REQUEST['ur_idx']} AND col_year = {$year} AND col_month={$month} limit 1 ";
		$collectRes = $DB->GetOne($sql);

		$sql = "select ur_unit from user_data where idx={$_REQUEST['ur_idx']} ";
		$userRes = $DB->GetOne($sql);

		if(!isset($collectRes['idx'])){
			$sql = "insert into collect_data(col_user, col_unit, col_value1, col_value2, col_value3, col_value4, col_value5, col_year, col_month, col_week, col_dt_last) values(";
			$sql .= "{$_REQUEST['ur_idx']}, ";
			$sql .= "{$userRes['ur_unit']}, ";
			$sql .= "{$_REQUEST['col_value1']}, ";
			$sql .= "{$_REQUEST['col_value2']}, ";
			$sql .= "{$_REQUEST['col_value3']}, ";
			$sql .= "{$_REQUEST['col_value4']}, ";
			$sql .= "{$_REQUEST['col_value5']}, ";
			$sql .= "{$year}, ";
			$sql .= "{$month}, ";
			$sql .= "{$week}, ";
			$sql .= "sysdate()) ";
			$DB->Execute($sql);
		}else{
			$sql = "update collect_data set ";
			$sql .= "col_value1 = {$_REQUEST['col_value1']}, ";
			$sql .= "col_value2 = {$_REQUEST['col_value2']}, ";
			$sql .= "col_value3 = {$_REQUEST['col_value3']}, ";
			$sql .= "col_value4 = {$_REQUEST['col_value4']}, ";
			$sql .= "col_value5 = {$_REQUEST['col_value5']}, ";
			$sql .= "col_dt_last = sysdate() ";
			$sql .= "where col_user={$_REQUEST['ur_idx']} AND col_year = {$year} AND col_month={$month} ";

			$DB->Execute($sql);
		}

		$sql = "select col_dt_last from collect_data ";
		$sql .= "where col_user={$_REQUEST['ur_idx']} AND col_year = {$year} AND col_month={$month} limit 1 ";
		$collectRes = $DB->GetOne($sql);

		$result->result = "1";
		$result->col_dt_last = $collectRes['col_dt_last'];

	}	

	echo json_encode($result);
?>