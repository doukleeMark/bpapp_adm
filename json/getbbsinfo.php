<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, bbs_idx
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['bbs_idx'])){

		$sql = "select bur.*, l.lk_cnt from ";
		$sql .= "(select b.*, u.ur_name, u.ur_id, u.ur_comp, r.cnt from user_data u, bbs_data b left outer join ";
		$sql .= "(select count(bbr_parent) as cnt, bbr_parent from bbs_reply group by bbr_parent) r ";
		$sql .= "on r.bbr_parent = b.idx ";
		$sql .= "where b.bbs_user = u.idx and b.idx = {$_REQUEST['bbs_idx']} ";
		$sql .= ") bur left outer join "; 
		$sql .= "(select count(lk_bbs) as lk_cnt, lk_bbs from like_data group by lk_bbs) l on l.lk_bbs = bur.idx ";
		$bbsRes = $DB->GetOne($sql);

		$result = (object)null;
		
		$result->bbs = (object)null;
		$result->bbs->bbs_idx = $bbsRes['idx'];
		$result->bbs->bbs_mode = $bbsRes['bbs_mode'];
		$result->bbs->bbs_user_idx = $bbsRes['bbs_user'];
		$result->bbs->bbs_user_mail = urlencode($bbsRes['ur_id']);
		$result->bbs->bbs_user_name = urlencode($bbsRes['ur_name']);
		$result->bbs->bbs_title = urlencode($bbsRes['bbs_title']);

		if(isset($bbsRes['idx'])){
			$sql = "select idx from like_data where lk_bbs = {$bbsRes['idx']} AND lk_user = {$_REQUEST['ur_idx']} ";
			$likeMy = $DB->GetOne($sql);
		}

		if($bbsRes['lk_cnt'] == null)$bbsRes['lk_cnt'] = '0';
		$result->bbs->bbs_like_cnt = $bbsRes['lk_cnt'];
		$result->bbs->bbs_like_my = (isset($likeMy['idx'])?"1":"0");
		$result->bbs->bbs_hit = $bbsRes['bbs_hit'];
		if($bbsRes['cnt'] == null)$bbsRes['cnt'] = '0';
		$result->bbs->bbs_reply_cnt = $bbsRes['cnt'];
		$result->bbs->bbs_dt_create = $bbsRes['bbs_dt_create'];
			
	}
	
	echo json_encode($result);
?>