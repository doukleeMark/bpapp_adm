<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, filter, type, brand, unit

	/* 
		type

		1 : 성공사례
		2 : 주간베스트
		3 : 월간베스트
		4 : 공개 (MY BP의 경우)
		5 : 비공개 (MY BP의 경우)
		6 : TEAM F/U

		filter

		0 : brand
		1 : unit
		2 : All
		3 : Team bp
		4 : my bp
    */
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['filter']) && isset($_REQUEST['type']) && isset($_REQUEST['brand']) && isset($_REQUEST['unit'])){

		$sql = "select ur_unit, ur_team, ur_teamlevel, ur_position from user_data where idx={$_REQUEST['ur_idx']}";
		$userRes = $DB->GetOne($sql);

		if($userRes == null){
			echo "error";
			return;
		}

		$result = (object)null;
		
		$result->items = (object)null;

		if((int)$_REQUEST['filter'] == 0){ // Brand 선택
			$AddBrand = "AND b.bp_brand={$_REQUEST['brand']} ";
			if((int)$_REQUEST['unit'] > 0)$AddUnit = "AND b.bp_unit={$_REQUEST['unit']} ";
		}else if((int)$_REQUEST['filter'] == 1){ // Unit 선택
			$AddUnit = "AND b.bp_unit={$_REQUEST['unit']} ";
		}else if((int)$_REQUEST['filter'] == 2){ // All

			if((int)$_REQUEST['unit'] == 7){
				$AddUnit = "AND b.bp_unit=7 ";
			}else{
				$AddUnit = "AND b.bp_unit!=7 ";
			}

		}else if((int)$_REQUEST['filter'] == 3){ // team bp
			$AddTeam = "AND u.ur_team = '{$userRes['ur_team']}' ";
		}else if((int)$_REQUEST['filter'] == 4){ // my bp
			$AddMy = "AND b.bp_user={$_REQUEST['ur_idx']} ";
		}

		if((int)$_REQUEST['type'] == 1){ // 성공사례
			if((int)$_REQUEST['filter'] == 3) $AddType = "AND b.bp_state!=0 ";
			else $AddType = "AND b.bp_state=5 ";
			$AddType .= "AND b.bp_choice_month=0 AND b.bp_teamfu=0 AND b.bp_new_fu=0 ";
			// 주간베스트 삭제로 해당 위치에 모두 노출
			// $AddType .= "AND b.bp_choice_ceo=0 AND b.bp_choice_bon=0 AND b.bp_choice_unit=0 AND b.bp_choice_mkt=0 AND b.bp_choice_month=0 AND b.bp_teamfu=0 ";
		}else if((int)$_REQUEST['type'] == 2){ // 주간베스트

			echo json_encode($result);
			return;

			if((int)$_REQUEST['filter'] == 3) $AddType = "AND b.bp_state!=0 ";
			else $AddType = "AND b.bp_state=5 ";
			$AddType .= "AND (b.bp_choice_ceo=1 OR b.bp_choice_bon=1 OR b.bp_choice_unit=1 OR b.bp_choice_mkt=1) AND b.bp_choice_month=0 AND b.bp_teamfu=0 AND b.bp_new_fu=0 ";
		}else if((int)$_REQUEST['type'] == 3){ // 월간베스트
			if((int)$_REQUEST['filter'] == 3) $AddType = "AND b.bp_state!=0 ";
			else {
				$AddType = "AND b.bp_state=5 ";
				if((int)$_REQUEST['unit'] == 7){
					$AddUnit = "AND b.bp_unit=7 ";
				}else{
					$AddUnit = "AND b.bp_unit!=7 ";
				}
			}
			$AddType .= "AND b.bp_choice_month=1 AND b.bp_teamfu=0 ";
		}else if((int)$_REQUEST['type'] == 4){ // 공개 (MY BP의 경우)
			$AddType = "AND b.bp_state!=0 ";
		}else if((int)$_REQUEST['type'] == 5){ // 비공개 (MY BP의 경우)
			$AddType = "AND b.bp_state=0 ";
		}else if((int)$_REQUEST['type'] == 6){ // new follow up
			$AddType = "AND b.bp_state=5 AND b.bp_teamfu=0 AND b.bp_new_fu=1 ";
		}

		$sql = "select bur.*, l.bl_cnt from ";
		$sql .= "(select b.*, u.ur_name, u.ur_id, u.ur_team, u.ur_group_high, r.bpr_cnt from user_data u, bp_data b left outer join ";
		$sql .= "(select count(bpr_parent) as bpr_cnt, bpr_parent from bp_reply group by bpr_parent) r ";
		$sql .= "on r.bpr_parent = b.idx ";
		$sql .= "where b.bp_hidden = 0 AND b.bp_user = u.idx ".$AddBrand.$AddUnit.$AddTeam.$AddMy.$AddType;
		$sql .= ") bur left outer join "; 
		$sql .= "(select count(bl_bp) as bl_cnt, bl_bp from bp_like group by bl_bp) l on l.bl_bp = bur.idx ";
		$sql .= "order by bur.idx desc";
		$bpRes = $DB->GetAll($sql);
		

		$bpArr = array();
		for($i=0;$i<count($bpRes);$i++) {
			$temp = (object)null;
			$temp->bp_idx = $bpRes[$i]['idx'];
			$temp->bp_brand = $bpRes[$i]['bp_brand'];
			$temp->bp_user_idx = $bpRes[$i]['bp_user'];
			$temp->bp_user_name = urlencode($bpRes[$i]['ur_name']);
			$temp->bp_user_team = urlencode($bpRes[$i]['ur_team']);
			$temp->bp_user_group_high = urlencode($bpRes[$i]['ur_group_high']);
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