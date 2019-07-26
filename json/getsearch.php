<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, stype, schar, unit
	
	$result = (object)null;

	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['stype']) && isset($_REQUEST['schar']) && isset($_REQUEST['unit'])){

		if((int)$_REQUEST['stype'] == 1){ // 데이터일 경우
			
			$result->file = (object)null;

			if(strlen($_REQUEST['schar']) > 0){

				$sql = "select di.*, up.tmp_name from data_info di, upload_data up where ";
				$sql .= "di.dt_file = up.idx AND di.dt_units like '%{$_REQUEST['unit']}%' ";
				$sql .= "AND di.dt_title like '%{$_REQUEST['schar']}%' ";
				$sql .= "order by idx desc ";
				$fileRes = $DB->GetAll($sql);

				$dataArr = array();
				for($i=0;$i<count($fileRes);$i++) {
					$temp = (object)null;
					$temp->dt_idx = $fileRes[$i]['idx'];
					$temp->dt_type = $fileRes[$i]['dt_type'];
					$temp->dt_folder = $fileRes[$i]['dt_folder'];
					$temp->dt_title = urlencode($fileRes[$i]['dt_title']);
					$temp->dt_file = urlencode(SHOST.$fileRes[$i]['tmp_name']);

					// 데이터 업로드시간 기준 하루까지 상태표시
					$diff = strtotime(date("Y-m-d")) - strtotime(substr($fileRes[$i]['dt_date'], 0, 10));
					$years = floor($diff / (365*60*60*24)); 
					$months = floor(($diff - $years*365*60*624)/(30*60*60*24));
					$days = floor(($diff - $years*365*60*60*24-$months*30*60*60*24)/(60*60*24)); 
					$daycount = $years*365+$months*30+$days;
					$temp->dt_new = ($daycount > 1 )?'0':'1';

					// 즐겨찾기 여부 확인
					$sql = "select idx from favor_data where ";
					$sql .= "fav_user = {$_REQUEST['ur_idx']} and fav_data = {$fileRes[$i]['idx']}";
					$favFile = $DB->GetOne($sql);
					$temp->dt_favor = (isset($favFile['idx']))?'1':'0';

					$temp->dt_date = $fileRes[$i]['dt_date'];
					$dataArr[] = $temp;
				}
				$result->file = $dataArr;
			}

		}else if((int)$_REQUEST['stype'] == 2){ // 스케줄일 경우

			if(strlen($_REQUEST['schar']) > 0){

				$sql = "select c.*, u.ur_id, u.ur_name from cal_data c, user_data u ";
				$sql .= "where c.cal_user = u.idx AND c.cal_unit = '{$_REQUEST['unit']}' ";
				$sql .= "AND (c.cal_title like '%{$_REQUEST['schar']}%' or u.ur_name like '%{$_REQUEST['schar']}%') ";
				$calRes = $DB->GetAll($sql);
				
				$result->event = (object)null;

				$eventArr = array();
				for($i=0;$i<count($calRes);$i++) {
					$temp = (object)null;
					$temp->cal_idx = $calRes[$i]['idx'];
					$temp->cal_user = $calRes[$i]['cal_user'];
					$temp->cal_user_name = urlencode($calRes[$i]['ur_name']);
					$temp->cal_user_mail = urlencode($calRes[$i]['ur_id']);
					$temp->cal_title = urlencode($calRes[$i]['cal_title']);
					$temp->cal_mail = $calRes[$i]['cal_mail'];
					$temp->cal_date = str_replace('-', '.',$calRes[$i]['cal_date']);
					$temp->cal_time = $calRes[$i]['cal_time'];
					$temp->cal_dt_create = str_replace('-', '.',$calRes[$i]['cal_dt_create']);
					
					$eventArr[] = $temp;
				}
				$result->event = $eventArr;
			}

		}else if((int)$_REQUEST['stype'] == 3){ // 성공사례일 경우

			if(strlen($_REQUEST['schar']) > 0){

				$sql = "select bur.*, l.bl_cnt from ";
				$sql .= "(select b.*, u.ur_name, u.ur_id, u.ur_team, r.bpr_cnt from user_data u, bp_data b left outer join ";
				$sql .= "(select count(bpr_parent) as bpr_cnt, bpr_parent from bp_reply group by bpr_parent) r ";
				$sql .= "on r.bpr_parent = b.idx ";
				$sql .= "where b.bp_hidden = 0 AND b.bp_user = u.idx AND b.bp_unit = {$_REQUEST['unit']} AND b.bp_state = 5 ";
				$sql .= "AND b.bp_title like '%{$_REQUEST['schar']}%' ";
				$sql .= ") bur left outer join "; 
				$sql .= "(select count(bl_bp) as bl_cnt, bl_bp from bp_like group by bl_bp) l on l.bl_bp = bur.idx ";
				$sql .= "order by bur.idx desc";
				$bpRes = $DB->GetAll($sql);
				
				$result->bp = (object)null;

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
				$result->bp = $bpArr;
			}
		}
	}
	
	echo json_encode($result);
?>