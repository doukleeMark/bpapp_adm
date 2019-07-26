<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : team

	if(isset($_REQUEST['team'])){

		$sql = "select * from user_data where ur_team='".addslashes($_REQUEST['team'])."' AND ur_teamlevel=2 limit 1";
		$t_LeaderRes = $DB->GetOne($sql);

		$sql = "select * from user_data where ur_team='".addslashes($_REQUEST['team'])."' AND ur_teamlevel=1 order by ur_name";
		$t_ListRes = $DB->GetAll($sql);

		// 팀정보
		if(isset($t_LeaderRes['ur_comp'])){
			$company = $t_LeaderRes['ur_comp'];
		}else if(isset($t_ListRes[0]['ur_comp'])){
			$company = $t_ListRes[0]['ur_comp'];
		}else{
			$company = '';
		}

		// 팀장
		$t_LeaderArr = array();
		$temp = urlencode($t_LeaderRes['ur_name']);
		if(isset($t_LeaderRes['ur_comp']))
			$t_LeaderArr[] = $temp;

		// 팀원
		$t_ListArr = array();
		for($i=0;$i<count($t_ListRes);$i++) {
			$temp = urlencode($t_ListRes[$i]['ur_name']);
			$t_ListArr[] = $temp;
		}

		$result = (object)null;

		// 팀정보
		$result->team_info = (object)null;
		$result->team_info->company = $company;

		$result->manager = (object)null;
		$result->manager = $t_LeaderArr;

		$result->member = (object)null;
		$result->member = $t_ListArr;

	}
	
	echo json_encode($result);
?>