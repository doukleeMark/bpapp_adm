<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, bbs_mode=1, unit
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['bbs_mode']) && isset($_REQUEST['unit'])){

		$sql = "select bur.*, l.lk_cnt from ";
		$sql .= "(select b.*, u.ur_name, u.ur_id, u.ur_comp, r.cnt from user_data u, bbs_data b left outer join ";
		$sql .= "(select count(bbr_parent) as cnt, bbr_parent from bbs_reply group by bbr_parent) r ";
		$sql .= "on r.bbr_parent = b.idx ";
		$sql .= "where b.bbs_user = u.idx and b.bbs_mode = {$_REQUEST['bbs_mode']} and b.bbs_unit = {$_REQUEST['unit']} ";
		$sql .= ") bur left outer join "; 
		$sql .= "(select count(lk_bbs) as lk_cnt, lk_bbs from like_data group by lk_bbs) l on l.lk_bbs = bur.idx ";
		$sql .= "order by bur.idx desc";
		$bbsRes = $DB->GetAll($sql);

		$result = (object)null;
		$result->bbs_list = (object)null;
		$bbsArr = array();
		for($i=0;$i<count($bbsRes);$i++) {
			$temp = (object)null;
			$temp->bbs_idx = $bbsRes[$i]['idx'];
			$temp->bbs_mode = $bbsRes[$i]['bbs_mode'];
			$temp->bbs_user_idx = $bbsRes[$i]['bbs_user'];
			$temp->bbs_user_mail = urlencode($bbsRes[$i]['ur_id']);
			$temp->bbs_user_name = urlencode($bbsRes[$i]['ur_name']);
			$temp->bbs_title = urlencode($bbsRes[$i]['bbs_title']);

			$sql = "select idx from like_data where lk_bbs = {$bbsRes[$i]['idx']} AND lk_user = {$_REQUEST['ur_idx']} ";
			$likeMy = $DB->GetOne($sql);

			if($bbsRes[$i]['lk_cnt'] == null)$bbsRes[$i]['lk_cnt'] = '0';
			$temp->bbs_like_cnt = $bbsRes[$i]['lk_cnt'];
			$temp->bbs_like_my = (isset($likeMy['idx'])?"1":"0");
			$temp->bbs_hit = $bbsRes[$i]['bbs_hit'];
			if($bbsRes[$i]['cnt'] == null)$bbsRes[$i]['cnt'] = '0';
			$temp->bbs_reply_cnt = $bbsRes[$i]['cnt'];
			$temp->bbs_dt_create = $bbsRes[$i]['bbs_dt_create'];
			
			$bbsArr[] = $temp;

		}
		$result->bbs_list = $bbsArr;
	}
	
	echo json_encode($result);
?>