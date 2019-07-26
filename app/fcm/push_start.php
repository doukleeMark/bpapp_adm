#!/usr/bin/php
<?php
	return;
	error_reporting(E_ALL ^ E_NOTICE);
	
	define('NotificationPushNone', 0);
	define('NotificationPushNotice', 1 << 0);
	define('NotificationPushNewData', 1 << 1);
	define('NotificationPushSchedule', 1 << 2);
	define('NotificationPushBP', 1 << 3);
	define('NotificationPushReply', 1 << 4);
	
	require_once("db.class.lib");
	require_once("/home/bpapp/public_html/include/lib/common.lib");
	include_once("/home/bpapp/public_html/include/class/push.class.lib");

	$DB = new DBclass();
	$pushClass = new PushClass();

	if(isset($argv[1])){

		$tokens_ios = array();
		$tokens_android = array();

		if(strpos($argv[1], ",") > 0){
			$arr = explode(",", $argv[1]);
		}

		if($arr[0] == "schedule"){
			
			$sql = "select cal_user, cal_title, cal_date, cal_unit from cal_data where idx={$arr[1]}";
			$calRes = $DB->GetOne($sql);

			if(isset($calRes['cal_date'])){

				$sql = "select dvi_token, dvi_os, dvi_push from device_data d, user_data u ";
				$sql .= "where d.dvi_user=u.idx AND u.ur_unit={$calRes['cal_unit']} AND u.ur_position IN(3,4,5,6) ";
				$sql .= "AND d.dvi_token is not null ";
				$deviceRes = $DB->GetAll($sql);
				
				for($i=0;$i<count($deviceRes);$i++){
					if($deviceRes[$i][dvi_push] & NotificationPushSchedule){
						if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
							if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
								$tokens_android[] = $deviceRes[$i]['dvi_token'];
							}else{
								$tokens_ios[] = $deviceRes[$i]['dvi_token'];
							}
						}
					}
				}

				$msg = "스케줄이 등록되었습니다.\n[{$calRes['cal_date']}]\n{$calRes['cal_title']}";

				if(count($tokens_ios) > 0)
					$pushClass->send_notification_ios($tokens_ios, "schedule", $msg, $arr[1]);
				if(count($tokens_android) > 0)
					$pushClass->send_notification_android($tokens_android, "schedule", $msg, $arr[1]);

			}
		}else if($arr[0] == "share"){
			
			$sql = "select cal_user, cal_title, cal_date, cal_unit from cal_data where idx={$arr[1]}";
			$calRes = $DB->GetOne($sql);

			$sql = "select ur_name from user_data where idx={$argv[2]}";
			$userRes = $DB->GetOne($sql);
			
			if(isset($calRes['cal_date']) && isset($userRes['ur_name'])){

				$sql = "select dvi_token, dvi_os, dvi_push from device_data where dvi_user={$arr[2]} AND dvi_token is not null ";
				$deviceRes = $DB->GetAll($sql);

				for($i=0;$i<count($deviceRes);$i++){
					if($deviceRes[$i][dvi_push] & NotificationPushSchedule){
						if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
							if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
								$tokens_android[] = $deviceRes[$i]['dvi_token'];
							}else{
								$tokens_ios[] = $deviceRes[$i]['dvi_token'];
							}
						}
					}
				}

				$msg = "스케줄이 공유되었습니다.\n[{$calRes['cal_date']}]\n{$calRes['cal_title']}\nFrom. {$userRes['ur_name']}";

				if(count($tokens_ios) > 0)
					$pushClass->send_notification_ios($tokens_ios, "share", $msg, $arr[1], $calRes['cal_unit']);
				if(count($tokens_android) > 0)
					$pushClass->send_notification_android($tokens_android, "share", $msg, $arr[1], $calRes['cal_unit']);
			}
			
		}else if($arr[0] == "choice8" || $arr[0] == "choice7"){

			$sql = "select bp_title, bp_unit from bp_data where idx={$arr[1]} ";
			$bpRes = $DB->GetOne($sql);

			if(isset($bpRes['bp_unit'])){

				if($arr[0] == "choice8")$msg = "부문장 초이스로 채택되었습니다.\n{$bpRes['bp_title']}";
				else if($arr[0] == "choice7")$msg = "본부장 초이스로 채택되었습니다.\n{$bpRes['bp_title']}";
				
				// Nephro, CHC 유닛 본부장 초이스시 각 그룹 사람에게만 푸시전송
				if((int)$argv[2] == 6 && $arr[0] == "choice7" || (int)$argv[2] == 7 && $arr[0] == "choice7"){

					$sql = "select dvi_token, dvi_os, dvi_push from device_data d, user_data u ";
					$sql .= "where d.dvi_user=u.idx AND u.ur_unit={$argv[2]} ";
					$sql .= "AND d.dvi_token is not null AND d.dvi_token != ''";
					$deviceRes = $DB->GetAll($sql);
					
				}else if($arr[0] == "choice7"){
					
					$sql = "select dvi_token, dvi_os, dvi_push from device_data d, user_data u ";
					$sql .= "where d.dvi_user = u.idx AND u.ur_unit != 6 AND u.ur_unit != 7 ";
					$sql .= "AND d.dvi_token is not null AND d.dvi_token != ''";
					$deviceRes = $DB->GetAll($sql);

				}else if($arr[0] == "choice8"){
					
					$sql = "select dvi_token, dvi_os, dvi_push from device_data d, user_data u ";
					$sql .= "where d.dvi_user = u.idx ";
					$sql .= "AND d.dvi_token is not null AND d.dvi_token != ''";
					$deviceRes = $DB->GetAll($sql);

				}
				
				for($i=0;$i<count($deviceRes);$i++){
					if($deviceRes[$i][dvi_push] & NotificationPushBP){
						if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
							if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
								$tokens_android[] = $deviceRes[$i]['dvi_token'];
							}else{
								$tokens_ios[] = $deviceRes[$i]['dvi_token'];
							}
						}
					}
				}

				if(count($tokens_ios) > 0)
					$pushClass->send_notification_ios($tokens_ios, $arr[0], $msg, $arr[1], $bpRes['bp_unit']);
				if(count($tokens_android) > 0)
					$pushClass->send_notification_android($tokens_android, $arr[0], $msg, $arr[1], $bpRes['bp_unit']);
				
			}

		}else if($arr[0] == "choice"){

			$sql = "select bp_title, bp_unit, bp_user from bp_data where idx={$arr[1]} ";
			$bpRes = $DB->GetOne($sql);

			if(isset($bpRes['bp_unit'])){

				$sql = "select dvi_token, dvi_os, dvi_push from device_data where dvi_user={$bpRes['bp_user']} AND dvi_token is not null AND dvi_token != ''";
				$deviceRes = $DB->GetAll($sql);

				for($i=0;$i<count($deviceRes);$i++){
					if($deviceRes[$i][dvi_push] & NotificationPushBP){
						if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
							if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
								$tokens_android[] = $deviceRes[$i]['dvi_token'];
							}else{
								$tokens_ios[] = $deviceRes[$i]['dvi_token'];
							}
						}
					}
				}

				$msg = "월간베스트로 채택되었습니다.\n{$bpRes['bp_title']}";

				if(count($tokens_ios) > 0)
					$pushClass->send_notification_ios($tokens_ios, $arr[0], $msg, $arr[1], $bpRes['bp_unit']);
				if(count($tokens_android) > 0)
					$pushClass->send_notification_android($tokens_android, $arr[0], $msg, $arr[1], $bpRes['bp_unit']);
			}

		}else if($arr[0] == "notice"){

			$sql = "select bod_title, bod_units from board_data where idx={$arr[1]} ";
			$boardRes = $DB->GetOne($sql);

			if(isset($boardRes['bod_units'])){

				$msg = "공지사항이 등록되었습니다.\n{$boardRes['bod_title']}";

				$units = substr(str_replace("X", "", $boardRes['bod_units']), 0, -1);
				if(strlen($units) > 0){
					$sql = "select dvi_token, dvi_os, dvi_push from device_data d, user_data u ";
					$sql .= "where d.dvi_user=u.idx AND u.ur_unit IN({$units}) ";
					$sql .= "AND d.dvi_token is not null AND d.dvi_token != ''";
					$deviceRes = $DB->GetAll($sql);

					for($i=0;$i<count($deviceRes);$i++){
						if($deviceRes[$i][dvi_push] & NotificationPushNotice){
							if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
								if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
									$tokens_android[] = $deviceRes[$i]['dvi_token'];
								}else{
									$tokens_ios[] = $deviceRes[$i]['dvi_token'];
								}
							}
						}
					}

					if(count($tokens_ios) > 0)
						$pushClass->send_notification_ios($tokens_ios, $arr[0], $msg, $arr[1]);
					if(count($tokens_android) > 0)
						$pushClass->send_notification_android($tokens_android, $arr[0], $msg, $arr[1]);
				}
			}

		}else if($arr[0] == "data"){

			$sql = "select dt_title, dt_units from data_info where idx={$arr[1]} ";
			$dataRes = $DB->GetOne($sql);

			if(isset($dataRes['dt_units'])){

				$msg = "데이터가 등록되었습니다.\n{$dataRes['dt_title']}";

				$units = substr(str_replace("X", "", $dataRes['dt_units']), 0, -1);
				if(strlen($units) > 0){
					$sql = "select dvi_token, dvi_os, dvi_push from device_data d, user_data u ";
					$sql .= "where d.dvi_user=u.idx AND u.ur_unit IN({$units}) ";
					$sql .= "AND d.dvi_token is not null AND d.dvi_token != ''";
					$deviceRes = $DB->GetAll($sql);

					for($i=0;$i<count($deviceRes);$i++){
						if($deviceRes[$i][dvi_push] & NotificationPushNewData){
							if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
								if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
									$tokens_android[] = $deviceRes[$i]['dvi_token'];
								}else{
									$tokens_ios[] = $deviceRes[$i]['dvi_token'];
								}
							}
						}
					}

					if(count($tokens_ios) > 0)
						$pushClass->send_notification_ios($tokens_ios, $arr[0], $msg);
					if(count($tokens_android) > 0)
						$pushClass->send_notification_android($tokens_android, $arr[0], $msg);
				}
			}

		}else if($arr[0] == "approval"){

			$sql = "select bp_title, bp_unit, bp_user, bp_state from bp_data where idx={$arr[1]} ";
			$bpRes = $DB->GetOne($sql);

			if((int)$bpRes['bp_state'] == 2 || (int)$bpRes['bp_state'] == 4){

				if((int)$bpRes['bp_state'] == 2){

					$sql = "select ur_name, ur_group_low from user_data where idx={$bpRes['bp_user']}";
					$userRes = $DB->GetOne($sql);

					$sql = "select dvi_token, dvi_os, dvi_push from device_data d, user_data u ";
					$sql .= "where d.dvi_user=u.idx ";
					$sql .= "AND u.ur_group_low = '{$userRes['ur_group_low']}' ";
					$sql .= "AND u.ur_group_level = 1 ";
					$sql .= "AND d.dvi_token is not null ";
					$deviceRes = $DB->GetAll($sql);

				}else{

					$sql = "select dvi_token, dvi_os, dvi_push from device_data d, user_data u ";
					$sql .= "where d.dvi_user=u.idx ";
					$sql .= "AND u.ur_position = 2 ";
					$sql .= "AND d.dvi_token is not null ";
					$deviceRes = $DB->GetAll($sql);

				}

				for($i=0;$i<count($deviceRes);$i++){
					if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
						if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
							$tokens_android[] = $deviceRes[$i]['dvi_token'];
						}else{
							$tokens_ios[] = $deviceRes[$i]['dvi_token'];
						}
					}
				}

				$msg = "성공사례 승인요청이 필요합니다.\n{$bpRes['bp_title']}";

				if(count($tokens_ios) > 0)
					$pushClass->send_notification_ios($tokens_ios, "approval", $msg, $arr[1], $bpRes['bp_unit']);
				if(count($tokens_android) > 0)
					$pushClass->send_notification_android($tokens_android, "approval", $msg, $arr[1], $bpRes['bp_unit']);

			}

		}else if($arr[0] == "bp_reply"){

			$sql = "select bp_title, bp_unit, bp_user from bp_data where idx={$arr[1]} ";
			$bpRes = $DB->GetOne($sql);

			if(isset($bpRes['bp_unit'])){

				$sql = "select dvi_token, dvi_os, dvi_push from device_data where dvi_user={$bpRes['bp_user']} AND dvi_token is not null ";
				$deviceRes = $DB->GetAll($sql);

				for($i=0;$i<count($deviceRes);$i++){
					if($deviceRes[$i][dvi_push] & NotificationPushReply){
						if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
							if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
								$tokens_android[] = $deviceRes[$i]['dvi_token'];
							}else{
								$tokens_ios[] = $deviceRes[$i]['dvi_token'];
							}
						}
					}
				}

				$msg = "댓글이 등록되었습니다.\n{$bpRes['bp_title']}";

				if(count($tokens_ios) > 0)
					$pushClass->send_notification_ios($tokens_ios, "bp_reply", $msg, $arr[1], $bpRes['bp_unit']);
				if(count($tokens_android) > 0)
					$pushClass->send_notification_android($tokens_android, "bp_reply", $msg, $arr[1], $bpRes['bp_unit']);
			}

		}else if($arr[0] == "reply"){

			$sql = "select bbs_title, bbs_unit, bbs_user from bbs_data where idx={$arr[1]} ";
			$bbsRes = $DB->GetOne($sql);

			if(isset($bbsRes['bbs_unit'])){

				$sql = "select dvi_token, dvi_os, dvi_push from device_data where dvi_user={$bbsRes['bbs_user']} AND dvi_token is not null ";
				$deviceRes = $DB->GetAll($sql);

				for($i=0;$i<count($deviceRes);$i++){
					if($deviceRes[$i][dvi_push] & NotificationPushReply){
						if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
							if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
								$tokens_android[] = $deviceRes[$i]['dvi_token'];
							}else{
								$tokens_ios[] = $deviceRes[$i]['dvi_token'];
							}
						}
					}
				}

				$msg = "댓글이 등록되었습니다.\n{$bbsRes['bbs_title']}";

				if(count($tokens_ios) > 0)
					$pushClass->send_notification_ios($tokens_ios, "reply", $msg, $arr[1], $bbsRes['bbs_unit']);
				if(count($tokens_android) > 0)
					$pushClass->send_notification_android($tokens_android, "reply", $msg, $arr[1], $bbsRes['bbs_unit']);
			}

		}else if($arr[0] == "bbs1" || $arr[0] == "bbs2"){

			$sql = "select bbs_title, bbs_unit, bbs_user from bbs_data where idx={$arr[1]} ";
			$bbsRes = $DB->GetOne($sql);

			if(isset($bbsRes['bbs_unit'])){

				$sql = "select dvi_token, dvi_os, dvi_push from device_data d, user_data u ";
				$sql .= "where d.dvi_user=u.idx AND u.ur_unit={$bbsRes['bbs_unit']} AND u.ur_position = 3 ";
				$sql .= "AND d.dvi_token is not null ";
				$deviceRes = $DB->GetAll($sql);

				for($i=0;$i<count($deviceRes);$i++){
					if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
						if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
							$tokens_android[] = $deviceRes[$i]['dvi_token'];
						}else{
							$tokens_ios[] = $deviceRes[$i]['dvi_token'];
						}
					}
				}

				if($arr[0] == "bbs1")$str = "요청";
				else $str = "질문";

				$msg = $str."이 등록되었습니다.\n{$bbsRes['bbs_title']}";

				if(count($tokens_ios) > 0)
					$pushClass->send_notification_ios($tokens_ios, "bbs", $msg, $arr[1], $bbsRes['bbs_unit']);
				if(count($tokens_android) > 0)
					$pushClass->send_notification_android($tokens_android, "bbs", $msg, $arr[1], $bbsRes['bbs_unit']);
			}

		}else if($arr[0] == "bp_open"){

			$sql = "select bp_title, bp_unit from bp_data where idx={$arr[1]} ";
			$bpRes = $DB->GetOne($sql);

			if(isset($bpRes['bp_unit'])){

				$sql = "select dvi_token, dvi_os, dvi_push from device_data d, user_data u ";
				$sql .= "where d.dvi_user=u.idx AND u.ur_unit={$bpRes['bp_unit']} AND u.ur_position = 3 ";
				$sql .= "AND d.dvi_token is not null ";
				$deviceRes = $DB->GetAll($sql);

				for($i=0;$i<count($deviceRes);$i++){
					if(!($deviceRes[$i]['dvi_token'] == "" || $deviceRes[$i]['dvi_token'] == NULL)){
						if(strpos($deviceRes[$i]['dvi_os'], "iOS") === false){
							$tokens_android[] = $deviceRes[$i]['dvi_token'];
						}else{
							$tokens_ios[] = $deviceRes[$i]['dvi_token'];
						}
					}
				}

				$msg = "성공사례가 전체공개되었습니다.\n{$bpRes['bp_title']}";

				if(count($tokens_ios) > 0)
					$pushClass->send_notification_ios($tokens_ios, "bp_open", $msg, $arr[1], $bpRes['bp_unit']);
				if(count($tokens_android) > 0)
					$pushClass->send_notification_android($tokens_android, "bp_open", $msg, $arr[1], $bpRes['bp_unit']);
			}

		}		
	}
?>