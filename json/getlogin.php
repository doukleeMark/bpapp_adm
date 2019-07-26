<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : id, pw

	include_once(CLASS_PATH . "/rank.class.lib");
	$rankClass = new RankClass();
	
	if(isset($_REQUEST['id']) && !(isset($_REQUEST['pw']))){

		$sql = "select * from user_data where ur_id='{$_REQUEST['id']}'";
		$res = $DB->GetOne($sql);

		$result = (object)null;
		$result->user = (object)null;
		
		if(isset($res['idx'])){
			$result->user->ur_state = $res['ur_state'];

			// 신규유저인지 디바이스 초기화한 유저인지 확인하기 위한 PW 유무 확인
			if(strlen($res['ur_pw']) > 0)$is_pw = "1";
			else $is_pw = "0";

			$result->user->is_pw = $is_pw;

			$sql = "select * from device_data where dvi_user='{$res['idx']}'";
			$dvi_res = $DB->GetAll($sql);

			if(count($dvi_res) == 0){
				// 디바이스 로우 생성
				$sql = "insert into device_data(dvi_user) values(";
				$sql .= $res['idx'] . ")";
				$DB->Execute($sql);

				$sql = "select * from device_data where dvi_user='{$res['idx']}'";
				$dvi_res = $DB->GetAll($sql);
			}

			$result->device = (object)null;
			$result->device->total_space = count($dvi_res);

			$empty = 0;
			for($i=0;$i<count($dvi_res);$i++) {
				if($dvi_res[$i]['dvi_uuid']==NULL)$empty++;
			}
			$result->device->empty_space = $empty;

		}else{
			$result->user->ur_state = "0";
		}
	}else if(isset($_REQUEST['id']) && isset($_REQUEST['pw'])){

		// 유저 정보
		$sql = "select * from user_data where ur_id='{$_REQUEST['id']}' and ur_pw=PASSWORD('".addslashes($_REQUEST['pw'])."')";
		$res = $DB->GetOne($sql);

		$result = (object)null;
		$result->user = (object)null;

		if(count($res)!= 0){
			
			$result->user->ur_state = $res['ur_state'];
			$result->user->ur_idx = $res['idx'];
			$result->user->ur_name = urlencode($res['ur_name']);
			$result->user->ur_level = $res['ur_level'];
			$result->user->ur_unit = $res['ur_unit'];
			$result->user->ur_position = $res['ur_position'];
			$result->user->ur_grade = $rankClass->getMyGrade($res['idx']);
			$result->user->ur_point = $res['ur_point_bp'];
			$result->user->ur_team = urlencode($res['ur_team']);
			$result->user->ur_teamlevel = $res['ur_teamlevel'];
			$result->user->ur_number = $res['ur_number'];
			$result->user->ur_month = $res['ur_month'];

			$result->user->ur_group_low = urlencode($res['ur_group_low']);
			$result->user->ur_group_high = urlencode($res['ur_group_high']);
			$result->user->ur_group_level = $res['ur_group_level'];
			$result->user->ur_choice_level = $res['ur_choice_level'];

			$sql = "select * from device_data where dvi_user='{$res['idx']}'";
			$dvi_res = $DB->GetAll($sql);

			$result->device = (object)null;
			$result->device->total_space = count($dvi_res);

			$empty = 0;
			$uuidArr = array();
			for($i=0;$i<count($dvi_res);$i++) {
				if($dvi_res[$i]['dvi_uuid']==NULL)
					$empty++;
				else
					$uuidArr[] = $dvi_res[$i]['dvi_uuid'];
			}	
			$result->device->empty_space = $empty;

			$result->uuid = (object)null;
			$result->uuid = $uuidArr;

			// 버전업 유도
			if(!isset($_REQUEST['ver'])) {
				$result->user->ur_state = "0";
			}

		}else{
			$result->user->ur_state = "0";
		}
	}

	echo json_encode($result);
?>