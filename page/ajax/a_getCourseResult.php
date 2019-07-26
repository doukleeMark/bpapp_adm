<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	include_once(CLASS_PATH . "/el.class.lib");

	$sql = "select * from course where idx={$_POST['idx']} and co_closed=0 limit 1 ";
	$co_info = $DB->GetOne($sql);

    if(!isset($co_info['idx']))return;

	$sql = "SELECT 
                cu.* ,
				ur.idx as ur_idx,
                ur.ur_id, 
                ifnull(ui.unit_name, '-') as unit
            FROM 
                user_data ur,
                course_user cu 
                LEFT JOIN unit_data ui 
                ON cu.cu_ur_unit = ui.idx 
            WHERE cu.cu_co_id = {$co_info['idx']} 
                AND cu.cu_ur_id = ur.idx ";
    $row = $DB->GetAll($sql);

    $totalCount = count($row);
    $completeCount = 0;
    $tot_score = 0;

	// 문제 가져오기
	$sql = "SELECT 
                cc_ct_id as ct_id
            FROM 
                course_contents
			WHERE cc_co_id = {$_POST['idx']} 
			ORDER BY cc_order ASC ";
	$ct_list = $DB->GetAll($sql);

	$sql = "SELECT * FROM contents_test_result WHERE ctr_complete = 1 ";
	$ctr_list = $DB->GetAll($sql);
	
	// 테스트가 없는 컨텐츠 재생완료 확인하기 
	$sql = "SELECT 
			cp.cp_ur_id, cp.cp_ct_id
		FROM
			contents ct, 	
			s3_data s3,
			contents_played cp
		WHERE 
			cp.cp_ct_id IN (
				SELECT ct.idx 
				FROM contents ct, course_contents cc 
				WHERE cc.cc_co_id = {$co_info['idx']} AND ct.idx = cc.cc_ct_id AND ct.ct_test_count = 0)
			AND ct.idx = cp.cp_ct_id 
			AND ct.ct_s3_file = s3.idx 
			AND cp.cp_play_sec+5 >= s3.s3_play_sec  
		ORDER BY cp.cp_ct_id ASC";
	$played_list = $DB->GetAll($sql);



	$output = array(		
		"data" => array()
	);

	$count = 0;

	for($i=0; $i<count($row);$i++){
		
		$count++;

		// 과정을 완료 했는지 확인 후 완료했다면 평균 구하고 완료처리
		$sql = "SELECT
				cc.cc_co_id as co_idx,
				count(cc.cc_ct_id) as tot_cnt,
				count(ctr_ct_id) as current_cnt,
				Round(Avg(ctr.ctr_score), 0) AS score_avg
			FROM
				test_cnt_sub tc, 
				course_contents cc 
				LEFT JOIN contents_test_result ctr 
				ON cc.cc_ct_id = ctr.ctr_ct_id
					AND ctr.ctr_ur_id = {$row[$i]['ur_idx']}
			WHERE
				cc_co_id = {$co_info['idx']} 
				AND tc.idx = cc.cc_ct_id
			GROUP BY cc.cc_co_id 
			LIMIT 1 ";
		$course_completeRes = $DB->GetOne($sql);

		if($course_completeRes['tot_cnt'] == $course_completeRes['current_cnt']){
			$sql = "UPDATE course_user 
					SET cu_complete = 1, 
						cu_score = {$course_completeRes['score_avg']}, 
						cu_dt_update = sysdate() 
					WHERE cu_co_id = {$course_completeRes['co_idx']} AND cu_ur_id = {$row[$i]['ur_idx']}";
			$DB->Execute($sql);
			$row[$i]['cu_complete'] = 1;
			$row[$i]['cu_score'] = $course_completeRes['score_avg'];
		}

		/*
		$row[$i]['cu_complete'] : 테스트을 풀고 테스트있는 컨텐츠들을 모두 이수 했을 경우 
		테스트가 없는 컨텐츠들도 이수 했는지 확인
		*/
		// total : 컨텐츠 총 개수 
		// non_t_cnt : 테스트가 없는 컨텐츠 개수 
		// cnt : 테스트가 없는 컨텐츠 중에 재생을 완료한 개수
		$sql = "SELECT 
					COUNT(cp.idx) AS cnt, 
					(SELECT COUNT(ct.idx) 
                    FROM contents ct, course_contents cc 
                    WHERE cc.cc_co_id = {$co_info['idx']} AND ct.idx = cc.cc_ct_id AND ct.ct_test_count = 0) AS non_t_cnt, 
					(SELECT COUNT(cc.idx) 
                    FROM course_contents cc 
                    WHERE cc.cc_co_id = {$co_info['idx']}) AS total
	   			FROM
					contents ct, 	
					s3_data s3,
					contents_played cp
	   			WHERE 
					cp.cp_ct_id IN (
						SELECT ct.idx 
						FROM contents ct, course_contents cc 
						WHERE cc.cc_co_id = {$co_info['idx']} AND ct.idx = cc.cc_ct_id AND ct.ct_test_count = 0)
					AND ct.idx = cp.cp_ct_id 
					AND ct.ct_s3_file = s3.idx 
					AND cp.cp_play_sec+5 >= s3.s3_play_sec
					AND cp.cp_ur_id = {$row[$i]['ur_idx']} ";
		$nonTestCnt = $DB->GetOne($sql);

		$complete = false;
		if($row[$i]['cu_complete']>0 || ($nonTestCnt['non_t_cnt'] == $nonTestCnt['total'] && $nonTestCnt['total'] > 0)){
			if($nonTestCnt['non_t_cnt'] == 0){
				$complete = true;
			}else if($nonTestCnt['non_t_cnt'] == $nonTestCnt['cnt']){
				$complete = true;
			}
		}

		if($complete){
			$completeCount++;
			$tot_score += $row[$i]['cu_score'];
		}

		$arr = array();
		$arr['no'] = $count;
		$arr['ur_idx'] = $row[$i]['ur_idx'];
		$arr['group'] = $row[$i]['unit'];
		$arr['team'] = $row[$i]['cu_ur_team'];
		$arr['id'] = $row[$i]['ur_id'];
		$arr['name'] = $row[$i]['cu_ur_name'];
		$arr['score'] = $row[$i]['cu_score'];
		
		// 각 컨텐츠 점수
		$temp = '';
		for ($j=0; $j < count($ct_list) ; $j++) { 
			$t = '-';

			// 재생완료 확인 
			for ($k=0; $k < count($played_list) ; $k++) { 
				if($row[$i]['ur_idx'] == $played_list[$k]['cp_ur_id'] && $ct_list[$j]['ct_id'] == $played_list[$k]['cp_ct_id']) {
					$t = '*';
					break;
				}
			}

			for ($k=0; $k < count($ctr_list) ; $k++) {
				if($ctr_list[$k]['ctr_ur_id'] == $row[$i]['ur_idx'] && $ctr_list[$k]['ctr_ct_id'] == $ct_list[$j]['ct_id']){
					$t = $ctr_list[$k]['ctr_score'];

					// 각 컨텐츠 점수 중 80미만일 경우 미이수처리 
					if((int)$ctr_list[$k]['ctr_score'] < 80){
						$complete = false;
					}
					break;
				}
			}
			$temp .= $t . ',';
		}
		$arr['complete'] = ($complete)?'O':'X';
		$arr['detail'] = substr($temp, 0, -1);
		$output['data'][] = $arr;
	}

	echo json_encode($output);	
?>