<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// IN : ur_idx, bp_idx, choice_type, choice_value, unit
	
	$result->result = '0';
	$result->msg = '';

	if(isset($_REQUEST['ur_idx']) && isset($_REQUEST['bp_idx']) && isset($_REQUEST['choice_type']) && isset($_REQUEST['choice_value'])){

		if((int)$_REQUEST['choice_type'] >= 7){
		
			if((int)$_REQUEST['choice_value'] == 1){ // 초이스 하기
				
				// 본부장, 월간베스트가 이미 초이스한 정보가 있는지 확인
				$sql = "select idx from bp_choice ";
				$sql .= "where bc_parent = {$_REQUEST['bp_idx']} ";
				$sql .= "AND bc_type = {$_REQUEST['choice_type']} limit 1 ";
				$choiceRes = $DB->GetOne($sql);

				if(isset($choiceRes['idx'])){
					
					$result->msg = "이미 Choice 상태 입니다.";
					echo json_encode($result);
					return;
				}

				// 본부장
				if((int)$_REQUEST['choice_type'] == 7){

					// bp 정보
					$sql = "select b.*, u.ur_group_high from user_data u, bp_data b ";
					$sql .= "where u.idx = b.bp_user AND b.idx = {$_REQUEST['bp_idx']} ";
					$bpRes = $DB->GetOne($sql);

					// 초이스 요청자 초이스 그룹확인 
					$sql = "select ur_group_high from user_data ";
					$sql .= "where idx = {$_REQUEST['ur_idx']} ";
					$userRes = $DB->GetOne($sql);

					if(!(strlen($bpRes['ur_group_high']) > 0 && $bpRes['ur_group_high'] == $userRes['ur_group_high'])){
						$result->msg = "Choice가 가능하지 않습니다.";
						echo json_encode($result);
						return;
					}
				}

				// 이번주 주차 기준일 때 넣었던 주차
				$weekNo = date("W");
				// 이번달
				$monthNo = (int)date("m");

				// 초이스 정보 저장하기
				$sql = "insert into bp_choice(bc_user, bc_parent, bc_week, bc_month, bc_type, bc_date) values (";
				$sql .= "{$_REQUEST['ur_idx']}, ";
				$sql .= "{$_REQUEST['bp_idx']}, ";
				$sql .= "{$weekNo}, ";
				$sql .= "{$monthNo}, ";
				$sql .= "{$_REQUEST['choice_type']}, ";
				$sql .= "sysdate()) ";
				$DB->Execute($sql);
				
			}else{ // 초이스 취소하기

				if((int)$_REQUEST['choice_type'] == 9){
					// 월간베스트 취소 가능 여부 확인
					$sql = "Select ur_month from user_data where idx={$_REQUEST['ur_idx']} AND ur_month=1 ";
					$monthRes = $DB->GetOne($sql);
				}

				// 초이스 취소 가능 여부 확인
				$sql = "select idx from bp_choice ";
				$sql .= "where bc_parent = {$_REQUEST['bp_idx']} ";
				if(!isset($monthRes['ur_month']))$sql .= "AND bc_user = {$_REQUEST['ur_idx']} ";
				$sql .= "AND bc_type = {$_REQUEST['choice_type']} limit 1 ";
				$choiceRes = $DB->GetOne($sql);

				if(!isset($choiceRes['idx']) && !isset($monthRes['ur_month'])){
						
					$result->msg = "Choice한 본인만 취소 가능합니다.";
					echo json_encode($result);
					return;
				}else{

					$sql = "delete from bp_choice where idx={$choiceRes['idx']}";
					$DB->Execute($sql);

				}

			}

			$sql = "update bp_data set ";
			if((int)$_REQUEST['choice_type'] == 9)$sql .= "bp_choice_month = '{$_REQUEST['choice_value']}' ";
			else if((int)$_REQUEST['choice_type'] == 8)$sql .= "bp_choice_ceo = '{$_REQUEST['choice_value']}' ";
			else if((int)$_REQUEST['choice_type'] == 7)$sql .= "bp_choice_bon = '{$_REQUEST['choice_value']}' ";
			$sql .= "where idx = '{$_REQUEST['bp_idx']}'";
			$DB->Execute($sql);

			// 초이스 로그
			$sql = "insert into bp_choice_log(bcl_user, bcl_bp, bcl_type, bcl_choice, bcl_date) values(";
			$sql .= "'" . $_REQUEST['ur_idx'] . "', ";
			$sql .= "'". $_REQUEST['bp_idx'] . "', ";
			$sql .= "'". $_REQUEST['choice_type'] . "', ";
			$sql .= "'". $_REQUEST['choice_value'] . "', ";
			$sql .= "sysdate()) ";
			$DB->Execute($sql);

			$result->result = '1';

			/* Best BP Push Send : start*/
            $obj = (object)[
                'choice_type'   => $_REQUEST['choice_type'],
                'choice_value'  => $_REQUEST['choice_value'],
                'bp_idx'        => $_REQUEST['bp_idx']
            ];

            $url = PUSH_SERVER;
            httpPost($url, $obj);
            /* Best BP Push Send : end*/
		}
	}

	function httpPost($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
	
	echo json_encode($result);
?>