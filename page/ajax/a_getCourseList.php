<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    // Paging
    $sLimit = "";
    if (isset($_POST['start']) && $_POST['length'] != '-1') {
        $sLimit = "LIMIT " . $DB->RealEscapeString($_POST['start']) . ", " .
        $DB->RealEscapeString($_POST['length']);
    }

    // Ordering
    $orderColumns = array('co.idx', 'co.idx', 'co.co_title', 'co.co_status', 'cu.com_cnt', 'cu.cnt', 'co.co_dt_update');

    if (isset($_POST['order'])) {
        $sOrder = "ORDER BY ";
        $sOrder .= $orderColumns[intval($_POST['order'][0]['column'])] . " " . $DB->RealEscapeString($_POST['order'][0]['dir']) . " ";
    }

    $filterColumns = array('co.co_title');
    $sWhere = "";
    if ($_POST['search']['value'] != "") {
        $sWhere = "AND (";

        if (strpos($_POST['search']['value'], " ")) {
            $words = explode(" ", $_POST['search']['value']);

            for ($i = 0; $i < count($words); $i++) {
                $sWhere .= " (";
                for ($j = 0; $j < count($filterColumns); $j++) {
                    $sWhere .= $filterColumns[$j] . " LIKE '%" . $DB->RealEscapeString($words[$i]) . "%' OR ";
                }
                $sWhere = substr_replace($sWhere, "", -3);
                $sWhere .= ") AND";
            }
        } else {

            for ($i = 0; $i < count($filterColumns); $i++) {
                $sWhere .= $filterColumns[$i] . " LIKE '%" . $DB->RealEscapeString($_POST['search']['value']) . "%' OR ";
            }
        }

        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ') ';
    }

    $add_sql = '';
    
    $sql = "select 
                co.idx, co.co_title, co.co_status, 
                co.co_dt_start, co.co_dt_end, co.co_dt_update, ifnull(cu.cnt, 0) as cnt, 
                ifnull(cu.com_cnt, 0) as com_cnt 
            from course co ";
            // 이도욱 21/01/11일 수정 -- 퇴사자 관련
    $sql .= "left join (    
                select
                    cu_co_id, count(cu_co_id) as cnt, count(if(cu_complete=1,1,null)) as com_cnt 
                from course_user WHERE cu_ur_id in (select idx from user_data where ur_hidden = 0) group by cu_co_id
            ) cu 
            on co.idx = cu.cu_co_id ";
    $sql .= "where co.co_closed = 0 ";
    $sql .= $add_sql . $sWhere . $sOrder . $sLimit;
    $res = $DB->Execute($sql);
    
    // 총 갯수
    $sql = "select count(co.idx) as cnt from course co ";
    $sql .= "where co.co_closed = 0 ";
    $sql .= $add_sql . $sWhere;
    $countRes = $DB->GetOne($sql);
    $cnt = $countRes['cnt'];

    $aColumns = array('idx', 'no', 'co_title', 'co_status', 'com_cnt', 'cnt', 'co_dt_update');

    $output = array(
        "draw" => $draw,
        "recordsTotal" => $cnt,
        "recordsFiltered" => $cnt,
        "data" => array(),
    );

    $count = 0;
    while ($aRow = mysqli_fetch_array($res)) {
        $count++;
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            if ($aColumns[$i] == "no") {
				$row['no'] = $count;
            } else if ($aColumns[$i] == "com_cnt") {
				// 테스트가 없는 컨텐츠 이수 개수 더하기
				// 이도욱 21/01/11일 수정 -- 퇴사자 관련
				$sql = "SELECT cu.cu_ur_id, cu.cu_complete
						FROM course_user cu
						WHERE cu.cu_co_id = {$aRow['idx']}
						AND cu_ur_id in (select idx from user_data where ur_hidden = 0)";
				$cu_row = $DB->GetAll($sql);

				$complete_cnt = 0;

				for ($j=0; $j < count($cu_row); $j++) { 
					// total : 컨텐츠 총 개수 
					// non_t_cnt : 테스트가 없는 컨텐츠 개수 
					// cnt : 테스트가 없는 컨텐츠 중에 재생을 완료한 개수
					$sql = "SELECT 
							COUNT(cp.idx) AS cnt, 
							(SELECT COUNT(ct.idx) 
							FROM contents ct, course_contents cc 
							WHERE cc.cc_co_id = {$aRow['idx']} AND ct.idx = cc.cc_ct_id AND ct.ct_test_count = 0) AS non_t_cnt, 
							(SELECT COUNT(cc.idx) 
							FROM course_contents cc 
							WHERE cc.cc_co_id = {$aRow['idx']}) AS total
						FROM
							contents ct, 	
							s3_data s3,
							contents_played cp
						WHERE 
							cp.cp_ct_id IN (
								SELECT ct.idx 
								FROM contents ct, course_contents cc 
								WHERE cc.cc_co_id = {$aRow['idx']} AND ct.idx = cc.cc_ct_id AND ct.ct_test_count = 0)
							AND ct.idx = cp.cp_ct_id 
							AND ct.ct_s3_file = s3.idx 
							AND cp.cp_play_sec+5 >= s3.s3_play_sec
							AND cp.cp_ur_id = {$cu_row[$j]['cu_ur_id']} ";
					$nonTestCnt = $DB->GetOne($sql);
					
					if($cu_row[$j]['cu_complete']>0 || ($nonTestCnt['non_t_cnt'] == $nonTestCnt['total'] && $nonTestCnt['total'] > 0)){
						if($nonTestCnt['non_t_cnt'] == 0){
							$complete_cnt++;
						}else if($nonTestCnt['non_t_cnt'] == $nonTestCnt['cnt']){
							$complete_cnt++;
						}
					}
				}

                $row['com_cnt'] = $complete_cnt;
            } else if ($aColumns[$i] == "co_status") {
                if($aRow[$aColumns[$i]] == 1){
                    $status = -1;
                }else if($aRow[$aColumns[$i]] == 2){
                    $now = new DateTime(date("Y-m-d"));
                    $datetime = new DateTime($aRow['co_dt_end']);

                    if($now > $datetime){
                        $status = -2;
                    }else{
                        $difference = $now->diff($datetime);
                        $status = $difference->days;
                    }
                    
                }
                $row[$aColumns[$i]] = $status;
            } else if ($aColumns[$i] != ' ') {
                $row[$aColumns[$i]] = $aRow[$aColumns[$i]];
            }
        }
        $output['data'][] = $row;
    }
    
    echo json_encode($output);
?>