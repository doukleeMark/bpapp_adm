<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, bp_idx
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['bp_idx'])){

		$sql = "select bur.*, l.bl_cnt from ";
		$sql .= "(select b.*, u.ur_name, u.ur_id, u.ur_team, u.ur_group_high, r.cnt from user_data u, bp_data b left outer join ";
		$sql .= "(select count(bpr_parent) as cnt, bpr_parent from bp_reply group by bpr_parent) r ";
		$sql .= "on r.bpr_parent = b.idx ";
		$sql .= "where b.bp_user = u.idx and b.idx = {$_REQUEST['bp_idx']} ";
		$sql .= ") bur left outer join "; 
		$sql .= "(select count(bl_bp) as bl_cnt, bl_bp from bp_like group by bl_bp) l on l.bl_bp = bur.idx ";
		$bpRes = $DB->GetOne($sql);

		$result = (object)null;
		
		$result->bp = (object)null;
		$result->bp->bp_idx = $bpRes['idx'];
		$result->bp->bp_brand = $bpRes['bp_brand'];
		$result->bp->bp_user_idx = $bpRes['bp_user'];
		$result->bp->bp_user_mail = urlencode($bpRes['ur_id']);
		$result->bp->bp_user_name = urlencode($bpRes['ur_name']);
		$result->bp->bp_user_team = urlencode($bpRes['ur_team']);
		$result->bp->bp_user_group_high = urlencode($bpRes['ur_group_high']);
		$result->bp->bp_follow = $bpRes['bp_follow'];
		$result->bp->bp_teamfu = $bpRes['bp_teamfu'];
		$result->bp->bp_title = urlencode($bpRes['bp_title']);
		$result->bp->bp_choice_ceo = $bpRes['bp_choice_ceo'];
		$result->bp->bp_choice_bon = $bpRes['bp_choice_bon'];
		$result->bp->bp_choice_unit = $bpRes['bp_choice_unit'];
		$result->bp->bp_choice_mkt = $bpRes['bp_choice_mkt'];
		$result->bp->bp_choice_mrt = $bpRes['bp_choice_mrt'];
		$result->bp->bp_choice_pm = $bpRes['bp_choice_pm'];
		$result->bp->bp_choice_month = $bpRes['bp_choice_month'];

		if($bpRes['bl_cnt'] == null)$bpRes['bl_cnt'] = '0';
		$result->bp->bp_like_cnt = $bpRes['bl_cnt'];

		$sql = "select bl_bp from bp_like where bl_bp = {$bpRes['idx']} AND bl_user = {$_REQUEST['ur_idx']} ";
		$likeMy = $DB->GetOne($sql);
		$result->bp->bp_like_my = (isset($likeMy['bl_bp'])?"1":"0");

		$result->bp->bp_hit = $bpRes['bp_hit'];

		if($bpRes['cnt'] == null)$bpRes['cnt'] = '0';
		$result->bp->bp_reply_cnt = $bpRes['cnt'];

		$result->bp->bp_state = $bpRes['bp_state'];
		$result->bp->bp_dt_create = $bpRes['bp_date'];
			
	}
	
	echo json_encode($result);
?>