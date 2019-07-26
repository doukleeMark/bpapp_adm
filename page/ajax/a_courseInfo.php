<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

	include_once(CLASS_PATH . "/el.class.lib");
	include_once(CLASS_PATH . "/s3.class.lib");

	if($_POST['actionType']=="insert") {

        $elClass = new ELClass();
        $s3Class = new S3Class();
         
        // 과정 정보 추가
        $obj = array(
            'co_writer' => $_SESSION['USER_NO'],
            'co_title' => $_POST['co_title'],
            'co_desc' => $_POST['co_desc'],
            'co_status' => (int)$_POST['co_status'],
            'co_s3_thumb' => (int)$_POST['co_s3_thumb'],
            'co_dt_start' => $_POST['co_dt_start'],
            'co_dt_end' => $_POST['co_dt_end']
        );

        $course_idx = $elClass->courseInsert($obj);

        // s3파일 전송 및 파일정보 업데이트
       $s3Class->s3Upload($_POST['co_s3_thumb'], 'course' . "/" . $course_idx);
       
        echo json_encode($course_idx);

	}else if($_POST['actionType']=="update") {

        $elClass = new ELClass();
	    $s3Class = new S3Class();

        // 필수로 마지막 배열은 idx 
        $obj = array(
            'co_title' => $_POST['co_title'],
            'co_desc' => $_POST['co_desc'],
            'co_status' => (int)$_POST['co_status'],
            'co_s3_thumb' => (int)$_POST['co_s3_thumb'],
            'co_dt_start' => $_POST['co_dt_start'],
            'co_dt_end' => $_POST['co_dt_end'],
            'idx' => (int)$_POST['idx']
        );

        $elClass->courseUpdate($obj);

        // s3파일 전송 및 파일정보 업데이트
        $s3Class->s3Upload($_POST['co_s3_thumb'], 'course' . "/" . $_POST['idx']);
		
	}else if($_POST['actionType']=="closedDeletes"){

        $elClass = new ELClass();
        $s3Class = new S3Class();

        $arr = explode(',', $_POST['idxs']);

        foreach($arr as $i){
			
			$course = $elClass->getCourseInfo($i);

            if($course['co_s3_thumb'] > 0){
                $s3Class->s3Delete($course['co_s3_thumb']);
			}
			
			// close 백업 과정 컨텐츠 연결정보 삭제 
			$sql = "DELETE FROM course_contents_close WHERE cc_co_id = {$i} ";
			$DB->Execute($sql);

            // 과정에 포함된 유저정보 삭제
			$elClass->courseAttenderDeleteWithCourseID($i);
			
			// close 백업 컨텐츠 데이터 삭제 
			$sql = "DELETE FROM course_closed_data WHERE co_id = {$i} ";
			$DB->Execute($sql);

			// close 백업 과정 결과 데이터 삭제
			$sql = "DELETE FROM course_result WHERE cor_co_id = {$i} ";
			$DB->Execute($sql);

            // 과정 삭제 
			$elClass->courseDelete($i);
			
        }
        
        echo json_encode(true);
        
	}else if($_POST['actionType']=="deletes"){

        $elClass = new ELClass();
        $s3Class = new S3Class();

        $arr = explode(',', $_POST['idxs']);

        foreach($arr as $i){
			
			$course = $elClass->getCourseInfo($i);

            if($course['co_s3_thumb'] > 0){
                $s3Class->s3Delete($course['co_s3_thumb']);
			}
			
			// 과정 컨텐츠 연결정보 삭제
			$elClass->courseContentDeleteWithCourseID($i);
			
			// 과정에 포함된 유저정보 삭제
			$elClass->courseAttenderDeleteWithCourseID($i);
			
            // 과정 삭제 
			$elClass->courseDelete($i);
			
        }
        
        echo json_encode(true);
        
	}else if($_POST['actionType']=='deleteFile'){
        
        // POST : idx, file_idx, target(input name)
        
        if((int)$_POST['idx'] > 0){
            $obj = array(
                $_POST['target'] => 0,
                'idx' => (int)$_POST['idx']
            );
    
            $elClass = new ELClass();
            $elClass->contentUpdate($obj);
        }

		$s3Class = new S3Class();
		$s3Class->s3Delete($_POST['file_idx']);
		
	}else if($_POST['actionType']=='close'){
		
		// POST : idx
		$co_idx = (int)$_POST['idx'];

		/* 과정 결과 저장 */
		$sql = "INSERT INTO course_closed_data 
			SELECT 
				null,
				{$co_idx} as co_id, 
				cp.cp_ur_id as ur_id, 
				cp.cp_ct_id as ct_id, 
				s3.s3_play_sec as play_duration, 
				cp.cp_play_sec as play_sec, 
				if(cp.cp_play_sec+5 >= s3.s3_play_sec, 1, 0) as play_complete, 
				cp.cp_dt_update as play_date, 
				ct.ct_test_count as test_count, 
				ifnull(ctr.ctr_complete, 0) as test_complete, 
				ctr.ctr_score as test_score,
				ctr.ctr_right_answer_cnt as test_right_answer_cnt,
				ctr.ctr_right_answer as test_right_answer,
				ctr.ctr_ur_input as test_input,
				ctr.ctr_cq_idxs as test_cq_idxs,
				ctr.ctr_dt_update as test_date 
			FROM
				contents ct, 
				s3_data s3,
				contents_played cp 
				LEFT JOIN contents_test_result ctr 
					ON cp.cp_ct_id = ctr.ctr_ct_id AND cp.cp_ur_id = ctr.ctr_ur_id, 
				course_user cu 
			WHERE 
				cp.cp_ct_id IN (
					SELECT ct.idx 
					FROM contents ct, course_contents cc 
					WHERE cc.cc_co_id = {$co_idx} AND ct.idx = cc.cc_ct_id )
				AND ct.idx = cp.cp_ct_id 
				AND ct.ct_s3_file = s3.idx 
				AND cu.cu_co_id = {$co_idx} 
				AND cu.cu_ur_id = cp.cp_ur_id  
			ORDER BY cp.cp_ur_id ASC";
		$DB->Execute($sql);
		
		// 과정에 포함된 유저 리스트 가져오기
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
            WHERE cu.cu_co_id = {$co_idx} 
                AND cu.cu_ur_id = ur.idx ";
		$row = $DB->GetAll($sql);
		
		// 컨텐츠 리스트 가져오기
		$sql = "SELECT 
				cc_ct_id as ct_id
			FROM 
				course_contents
			WHERE cc_co_id = {$co_idx} 
			ORDER BY cc_order ASC ";
		$ct_list = $DB->GetAll($sql);

		// 컨텐츠 결과 가져오기 
		$sql = "SELECT * FROM course_closed_data WHERE co_id = {$co_idx} ";
		$ccd_list = $DB->GetAll($sql);

		for($i=0; $i<count($row);$i++){

			// $row[$i]['ur_idx']

			// $complete = 0;
			// if($row[$i]['cu_complete']>0 || ($nonTestCnt['non_t_cnt'] == $nonTestCnt['total'] && $nonTestCnt['total'] > 0)){
			// 	if($nonTestCnt['non_t_cnt'] == 0){
			// 		$complete = 1;
			// 	}else if($nonTestCnt['non_t_cnt'] == $nonTestCnt['cnt']){
			// 		$complete = 1;
			// 	}
			// }
			
			$complete_cnt = 0;

			// 각 컨텐츠 점수
			$ct_res = '';
			for ($j=0; $j < count($ct_list) ; $j++) { 
				
				$sub = '-';
				for ($k=0; $k < count($ccd_list) ; $k++) {

					if($row[$i]['ur_idx'] == $ccd_list[$k]['ur_id'] && $ct_list[$j]['ct_id'] == $ccd_list[$k]['ct_id']) {
						
						// 이수 체크
						if((int)$ccd_list[$k]['test_count'] > 0 && (int)$ccd_list[$k]['test_complete'] == 1){
							$complete_cnt++;
							$sub = $ccd_list[$k]['test_score'];
						} else if((int)$ccd_list[$k]['test_count'] == 0 && (int)$ccd_list[$k]['play_complete'] == 1) {
							$complete_cnt++;
							$sub = '*';
						}
						break;
					}
				}
				$ct_res .= $sub . ',';
			}
			if(strlen($ct_res) > 0) $ct_res = substr($ct_res, 0, -1);

			$complete = ($complete_cnt == count($ct_list))?1:0;

			// 결과 추가 
			$sql = "INSERT INTO course_result(
						cor_co_id,
						cor_ur_id,
						cor_group_name,
						cor_team_name,
						cor_ur_email,
						cor_ur_name,
						cor_complete,
						cor_score,
						cor_ct_res,
						cor_dt_create)
					VALUES(
						{$co_idx},
						{$row[$i]['ur_idx']},
						'{$row[$i]['unit']}',
						'{$row[$i]['cu_ur_team']}',
						'{$row[$i]['ur_id']}',
						'{$row[$i]['cu_ur_name']}',
						{$complete},
						{$row[$i]['cu_score']},
						'{$ct_res}',
						sysdate()
					)";
			$DB->Execute($sql);
		}
        
		$sql = "update course set co_closed = 1, co_dt_closed = sysdate() where idx = {$co_idx} ";
		$DB->Execute($sql);

		// 과정과 컨텐츠 연결 정보 삭제 
		$sql = "INSERT INTO course_contents_close  
			SELECT null, cc_co_id, cc_ct_id, cc_order 
			FROM course_contents 
			WHERE cc_co_id = {$co_idx} ORDER BY cc_order ASC ";
		$DB->Execute($sql);

		$sql = "DELETE FROM course_contents WHERE cc_co_id = {$co_idx} ";
		$DB->Execute($sql);

	}
	
?>