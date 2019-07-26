<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, search_name
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['search_name'])){

		$sql = "select * from user_data where idx='{$_REQUEST['ur_idx']}'";
		$userRes = $DB->GetOne($sql);

		$result = (object)null;
		
		$result->search = (object)null;

		$sql = "select * from user_data where ";
		$sql .= "ur_hidden = 0 ";
		if(strlen($_REQUEST['search_name']) > 0)$sql .= "AND ur_name like '%{$_REQUEST['search_name']}%' ";
		else $sql .= "AND ur_team = '{$userRes['ur_team']}' ";
		if((int)$userRes['ur_unit'] == 7)$sql .= "AND ur_unit = 7 ";
		else $sql .= "AND ur_unit != 7 ";
		$sql .= "order by ur_name asc ";
		$searchRes = $DB->GetAll($sql);
		
		$tempArr = array();
		for($i=0;$i<count($searchRes);$i++) {
			$temp = (object)null;
			$temp->ur_idx = $searchRes[$i]['idx'];
			$temp->ur_id = urlencode($searchRes[$i]['ur_id']);
			$temp->ur_name = urlencode($searchRes[$i]['ur_name']);
			$temp->ur_team = urlencode($searchRes[$i]['ur_team']);

			$tempArr[] = $temp;
		}
		$result->search = $tempArr;
		
	}
	
	echo json_encode($result);
?>