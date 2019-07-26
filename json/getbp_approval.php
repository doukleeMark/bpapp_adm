<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx
	// CP 나 그룹장 승인권한있는 경우 승인 가능한 BP 리스트를 보내준다. 
	
	if(isset($_REQUEST['ur_idx'])){

		$sql = "select ur_group_level, ur_group_low, ur_position from user_data where idx={$_REQUEST['ur_idx']}";
		$userRes = $DB->GetOne($sql);

		if($userRes == null){
			echo "error";
			return;
		}

		if((int)$userRes['ur_position'] == 2){ // CP

			$sql = "select bur.*, l.bl_cnt from ";
			$sql .= "(select b.*, u.ur_name, u.ur_id, u.ur_team, r.bpr_cnt from user_data u, bp_data b left outer join ";
			$sql .= "(select count(bpr_parent) as bpr_cnt, bpr_parent from bp_reply group by bpr_parent) r ";
			$sql .= "on r.bpr_parent = b.idx ";
			$sql .= "where b.bp_user = u.idx ";
			$sql .= "AND b.bp_hidden=0 ";
			$sql .= "AND b.bp_state=4 ";
			$sql .= ") bur left outer join "; 
			$sql .= "(select count(bl_bp) as bl_cnt, bl_bp from bp_like group by bl_bp) l on l.bl_bp = bur.idx ";
			$sql .= "order by bur.idx desc";
			$bpRes = $DB->GetAll($sql);

		// 그룹장
		}else if((int)$userRes['ur_group_level'] == 1 && strlen($userRes['ur_group_low'])){ 
	
			$sql = "select bur.*, l.bl_cnt from ";
			$sql .= "(select b.*, u.ur_name, u.ur_id, u.ur_group_low, r.bpr_cnt from user_data u, bp_data b left outer join ";
			$sql .= "(select count(bpr_parent) as bpr_cnt, bpr_parent from bp_reply group by bpr_parent) r ";
			$sql .= "on r.bpr_parent = b.idx ";
			$sql .= "where b.bp_user = u.idx ";
			$sql .= "AND b.bp_hidden=0 ";
			$sql .= "AND b.bp_state IN(2,3,4) ";
			$sql .= "AND u.ur_group_low='{$userRes['ur_group_low']}' ";
			$sql .= ") bur left outer join "; 
			$sql .= "(select count(bl_bp) as bl_cnt, bl_bp from bp_like group by bl_bp) l on l.bl_bp = bur.idx ";
			$sql .= "order by bur.idx desc";
			$bpRes = $DB->GetAll($sql);
		}

		$result = (object)null;
		
		$result->items = (object)null;

		$bpArr = array();
		for($i=0;$i<count($bpRes);$i++) {
			$temp = (object)null;
			$temp->bp_idx = $bpRes[$i]['idx'];
			$temp->bp_brand = $bpRes[$i]['bp_brand'];
			$temp->bp_user_idx = $bpRes[$i]['bp_user'];
			$temp->bp_user_name = urlencode($bpRes[$i]['ur_name']);
			$temp->bp_user_team = urlencode($bpRes[$i]['ur_team']);
			$temp->bp_follow = $bpRes[$i]['bp_follow'];
			$temp->bp_teamfu = $bpRes[$i]['bp_teamfu'];
			$temp->bp_title = urlencode($bpRes[$i]['bp_title']);
			$temp->bp_choice_ceo = $bpRes[$i]['bp_choice_ceo'];
			$temp->bp_choice_bon = $bpRes[$i]['bp_choice_bon'];
			$temp->bp_choice_unit = $bpRes[$i]['bp_choice_unit'];
			$temp->bp_choice_mkt = $bpRes[$i]['bp_choice_mkt'];
			$temp->bp_choice_mrt = $bpRes[$i]['bp_choice_mrt'];
			$temp->bp_choice_pm = $bpRes[$i]['bp_choice_pm'];
			
			if($bpRes[$i]['bl_cnt'] == null)$bpRes[$i]['bl_cnt'] = '0';
			$temp->bp_like_cnt = $bpRes[$i]['bl_cnt'];
			
			$sql = "select bl_bp from bp_like where bl_bp = {$bpRes[$i]['idx']} AND bl_user = {$_REQUEST['ur_idx']} ";
			$likeMy = $DB->GetOne($sql);
			$temp->bp_like_my = (isset($likeMy['bl_bp'])?"1":"0");

			$temp->bp_hit = $bpRes[$i]['bp_hit'];

			if($bpRes[$i]['bpr_cnt'] == null)$bpRes[$i]['bpr_cnt'] = '0';
			$temp->bp_reply_cnt = $bpRes[$i]['bpr_cnt'];

			$temp->bp_state = $bpRes[$i]['bp_state'];
			$temp->bp_dt_create = $bpRes[$i]['bp_date'];
			
			$bpArr[] = $temp;
		}
		$result->items = $bpArr;
	}
	
	echo json_encode($result);
?>