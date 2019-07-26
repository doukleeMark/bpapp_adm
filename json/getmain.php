<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, unit
	
	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['unit'])){
		$sql = "select * from user_data where idx='{$_REQUEST['ur_idx']}'";
		$resUser = $DB->GetOne($sql);

		$result = (object)null;
		$result->new = (object)null;
		$result->new->data = '0';
		$result->new->event = '0';
		$result->new->bp = '0';
		$result->new->community = '0';
		$result->new->question = '0';
		$result->new->quickpoll = '0';

		// data
		$sql = "select dt_date from data_info where dt_units like '%{$_REQUEST['unit']}%' ";
		$sql .= "order by idx desc limit 1";
		$lastRes = $DB->GetOne($sql);
		
		if(isset($lastRes['dt_date'])){
			$result->new->data = oneDayCheck($lastRes['dt_date']);
		}

		// event
		$sql = "select cal_dt_create from cal_data where cal_unit = {$_REQUEST['unit']} ";
		$sql .= "order by idx desc limit 1";
		$lastRes = $DB->GetOne($sql);
		
		if(isset($lastRes['cal_dt_create'])){
			$result->new->event = oneDayCheck($lastRes['cal_dt_create']);
		}

		if((int)$_REQUEST['unit'] == 7){
			// 유닛이 CHC인 경우

			// community
			$sql = "select bbs_dt_create from bbs_data where bbs_unit = {$_REQUEST['unit']} ";
			$sql .= "order by idx desc limit 1";
			$lastRes = $DB->GetOne($sql);
			
			if(isset($lastRes['bbs_dt_create'])){
				$result->new->community = oneDayCheck($lastRes['bbs_dt_create']);
			}

			// quickpoll
			$sql = "select svf_date from survey_info where svf_unit = {$_REQUEST['unit']} AND svf_visible = 1 ";
			$sql .= "order by idx desc limit 1";
			$lastRes = $DB->GetOne($sql);
			
			if(isset($lastRes['svf_date'])){
				$result->new->quickpoll = oneDayCheck($lastRes['svf_date']);
			}

		}else{
			// 유닛이 CHC가 아닌 경우

			// community
			$sql = "select bbs_dt_create from bbs_data where bbs_mode IN(1,2,3) and bbs_unit = {$_REQUEST['unit']} ";
			$sql .= "order by idx desc limit 1";
			$lastRes = $DB->GetOne($sql);
			
			if(isset($lastRes['bbs_dt_create'])){
				$result->new->community = oneDayCheck($lastRes['bbs_dt_create']);
			}

			// question
			$sql = "select bbs_dt_create from bbs_data where bbs_mode IN(4,5) and bbs_unit = {$_REQUEST['unit']} ";
			$sql .= "order by idx desc limit 1";
			$lastRes = $DB->GetOne($sql);
			
			if(isset($lastRes['bbs_dt_create'])){
				$result->new->question = oneDayCheck($lastRes['bbs_dt_create']);
			}
		}

		// notice
		$result->notice = (object)null;

		$sql = "select * from board_data where ";
		$sql .= "bod_type = 1 ";
		$sql .= "AND bod_units like '%X{$_REQUEST['unit']},%' ";
		$sql .= "order by idx desc LIMIT 3";
		$resNotice = $DB->GetAll($sql);
		$noticeArr = array();
		for($i=0;$i<count($resNotice);$i++) {
			$temp = (object)null;
			$temp->bod_idx = $resNotice[$i]['idx'];
			$temp->bod_title = urlencode($resNotice[$i]['bod_title']);
			$temp->bod_dt_day = substr($resNotice[$i]['bod_date'], 8, 2);
			$temp->bod_dt_month = strtoupper(substr(date("F",strtotime($resNotice[$i]['bod_date'])), 0, 3)) ;

			// 업로드시간 기준 하루까지 상태표시
			$diff = strtotime(date("Y-m-d")) - strtotime(substr($resNotice[$i]['bod_date'], 0, 10));
			$years = floor($diff / (365*60*60*24)); 
			$months = floor(($diff - $years*365*60*624)/(30*60*60*24));
			$days = floor(($diff - $years*365*60*60*24-$months*30*60*60*24)/(60*60*24)); 
			$daycount = $years*365+$months*30+$days;
			$temp->bod_new = ($daycount > 1 )?'0':'1';
			$temp->bod_date = $resNotice[$i]['bod_date'];
			$temp->bod_hit = $resNotice[$i]['bod_hit'];
			$noticeArr[] = $temp;
		}
		$result->notice = $noticeArr;

		// mission
		$result->mission = (object)null;

		$result->mission_active = '0';
		
		$sql = "select * from mission_data where md_unit = {$_REQUEST['unit']} order by md_group";
		$resMission = $DB->GetAll($sql);
		$missionArr = array();
		for($i=0;$i<count($resMission);$i++) {
			$temp = (object)null;
			$temp->md_group = $resMission[$i]['md_group'];
			$temp->md_target = $resMission[$i]['md_target'];
			$temp->md_arrival = $resMission[$i]['md_arrival'];
			$missionArr[] = $temp;
		}
		$result->mission = $missionArr;

		// mission desc
		$result->mission_desc = (object)null;
		
		$sql = "select * from mission_str limit 1";
		$resMissionDesc = $DB->GetOne($sql);
		$result->mission_desc = urlencode($resMissionDesc['descTxt']);

	}

	function oneDayCheck($_date){
		// 데이터 업로드시간 기준 하루까지 상태표시
		$diff = strtotime(date("Y-m-d")) - strtotime(substr($_date, 0, 10));
		$years = floor($diff / (365*60*60*24)); 
		$months = floor(($diff - $years*365*60*624)/(30*60*60*24));
		$days = floor(($diff - $years*365*60*60*24-$months*30*60*60*24)/(60*60*24)); 
		$daycount = $years*365+$months*30+$days;
		
		return ($daycount > 1 )?'0':'1';
	}
	
	echo json_encode($result);
?>