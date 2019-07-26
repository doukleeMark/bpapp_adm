<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx
	// CP 나 그룹장 승인권한있는 경우 승인 가능한 BP 개수를 보내준다. 
	
	if(isset($_REQUEST['ur_idx'])){

		$sql = "select ur_group_level, ur_group_low, ur_position from user_data where idx={$_REQUEST['ur_idx']}";
		$userRes = $DB->GetOne($sql);

		if($userRes == null){
			echo "error";
			return;
		}

		// CP
		if((int)$userRes['ur_position'] == 2){

			$sql = "select count(idx) as cnt from bp_data where bp_hidden=0 AND bp_state=4 ";
			$confirm = $DB->GetOne($sql);
			$confirm_cnt = $confirm['cnt'];
		
		// 그룹장
		}else if((int)$userRes['ur_group_level'] == 1 && strlen($userRes['ur_group_low'])){ 

			$sql = "select count(b.idx) as cnt from bp_data b, user_data u where b.bp_hidden=0 ";
			$sql .= "AND b.bp_user = u.idx ";
			$sql .= "AND b.bp_state IN(2,3,4) ";
			$sql .= "AND u.ur_group_low='{$userRes['ur_group_low']}' ";
			$sql .= "AND b.bp_user!={$_REQUEST['ur_idx']} ";
			$confirm = $DB->GetOne($sql);
			$confirm_cnt = $confirm['cnt'];

		}else{
			$confirm_cnt = -1;
		}

		$result = (object)null;
		$result->confirm_cnt = $confirm_cnt;
	}
	
	echo json_encode($result);
?>