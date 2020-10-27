<?php
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	include_once(CLASS_PATH . "/el.class.lib");

    $elClass = new ELClass();
    $co_info = $elClass->getCourseInfo($_GET['c']);

    if(!isset($co_info['idx'])){
		echo "<script>location.href = '/?page=el_courseList';</script>";
		return;
    }
	
	use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;

    require_once __DIR__ . '/../vendor/phpoffice/phpspreadsheet/src/Bootstrap.php';

	// Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set document properties
    $spreadsheet->getProperties()->setCreator('BP')
        ->setLastModifiedBy('BP')
        ->setTitle('')
        ->setSubject('')
        ->setDescription('')
        ->setKeywords('')
        ->setCategory('');

    $style_center = array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
    );
    $style_left = array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
    );

    $sheet->getStyle('A1:A5')->applyFromArray(
		array(
			'font' => array(
				'bold' => true,
				'size' => 11
			),
			'alignment' => $style_center
		)
    );
    $sheet->getStyle('D3:D5')->applyFromArray(
		array(
			'font' => array(
				'bold' => true,
				'size' => 11
            ),
			'alignment' => $style_center
		)
    );
    
    $sheet->getStyle('A7:F7')->applyFromArray(
		array(
			'font' => array(
				'bold' => true,
				'size' => 11
			),
			'alignment' => $style_center
		)
    );
    $sheet->getStyle('B3:B5')->applyFromArray(
		array(
			'alignment' => $style_left
		)
    );
    $sheet->getStyle('E3:E5')->applyFromArray(
		array(
			'alignment' => $style_left
		)
    );
	
	$sql = "SELECT 
                cu.* ,
				ur.idx as ur_idx,
                ur.ur_id,
                ur.ur_team,
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

    $sheet->setCellValue("A1", "Result Report");
    $sheet->mergeCells('A1:F1');
    $sheet->setCellValue("A2", "과정명");
    $sheet->setCellValue("B2", $co_info['co_title']);
    $sheet->mergeCells('B2:F2');

    $sheet->setCellValue("A3", "작성일");
    $sheet->setCellValue("B3", $co_info['co_dt_create']);
	$sheet->mergeCells('B3:F3');
	
    $sheet->setCellValue("A4", "총인원");
    $sheet->setCellValue("B4", $totalCount);
    $sheet->mergeCells('B4:C4');

    // 상태
    if($co_info['co_status'] == 1){
        $status = '무제한';
    }else if($co_info['co_status'] == 2){
        $status = $co_info['co_dt_start'] . " ~ " . $co_info['co_dt_end'];
    }
    $sheet->setCellValue("D4", "상태");
    $sheet->setCellValue("E4", $status);
    $sheet->mergeCells('E4:F4');

    $sheet->setCellValue("A7", "그룹명");
    $sheet->setCellValue("B7", "팀명");
    $sheet->setCellValue("C7", "ID");
    $sheet->setCellValue("D7", "이름");
    $sheet->setCellValue("E7", "이수여부");
	$sheet->setCellValue("F7", "점수");

	// 문제 가져오기 //$co_info['idx']
	$sql = "SELECT 
                cc.cc_ct_id as ct_id, 
				ct.ct_title 
            FROM 
                course_contents cc,
				contents ct 
			WHERE cc.cc_co_id = {$_GET['c']} AND cc.cc_ct_id = ct.idx 
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

	$rowcell = 6;
	for ($i=0; $i < count($ct_list) ; $i++) { 
		$sheet->setCellValue(num2alpha($rowcell+$i)."7", $ct_list[$i]['ct_title']);
		$sheet->getColumnDimension(num2alpha($rowcell+$i))->setWidth(13);
		$sheet->getStyle(num2alpha($rowcell+$i))->applyFromArray(array('alignment' => $style_center));
	}

	$sheet->mergeCells('A6:F6');
    $sheet->getColumnDimension("A")->setWidth(17);
    $sheet->getColumnDimension("B")->setWidth(17);
    $sheet->getColumnDimension("C")->setWidth(17);
    $sheet->getColumnDimension("D")->setWidth(17);
    $sheet->getColumnDimension("E")->setWidth(17);
    $sheet->getColumnDimension("F")->setWidth(17);
    
    $cell = 8;
    $rowcell = 6;
    for($i=0; $i<count($row);$i++){
        $sheet->setCellValue("A".$cell, $row[$i]['unit']);
        $sheet->setCellValue("B".$cell, $row[$i]['ur_team']);
        $sheet->setCellValue("C".$cell, $row[$i]['ur_id']);
		$sheet->setCellValue("D".$cell, $row[$i]['cu_ur_name']);

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

		// 각 컨텐츠 점수
		for ($j=0; $j < count($ct_list) ; $j++) { 
			$sheet->setCellValue(num2alpha($rowcell+$j).$cell, '-');
			
			// 재생완료 확인 
			for ($k=0; $k < count($played_list) ; $k++) { 
				if($row[$i]['ur_idx'] == $played_list[$k]['cp_ur_id'] && $ct_list[$j]['ct_id'] == $played_list[$k]['cp_ct_id']) {
					$sheet->setCellValue(num2alpha($rowcell+$j).$cell, '*');
					break;
				}
			}
			
			for ($k=0; $k < count($ctr_list) ; $k++) {
				if($ctr_list[$k]['ctr_ur_id'] == $row[$i]['ur_idx'] && $ctr_list[$k]['ctr_ct_id'] == $ct_list[$j]['ct_id']){
					$sheet->setCellValue(num2alpha($rowcell+$j).$cell, $ctr_list[$k]['ctr_score']);
					
					// 각 컨텐츠 점수 중 80미만일 경우 미이수처리 
					if((int)$ctr_list[$k]['ctr_score'] < 80){
						$complete = false;
					}
					break;
				}
			}
		}
		$sheet->setCellValue("E".$cell, ($complete)?'O':'X');
		$sheet->setCellValue("F".$cell, $row[$i]['cu_score']);

        $cell++;
	}
	

	$sheet->setCellValue("A5", "이수완료");
    $avg = 0;
    if($totalCount > 0){
        $avg = round($completeCount / $totalCount * 100);
    }
    $sheet->setCellValue("B5", $completeCount . "(" . $avg . "%)");
    $sheet->mergeCells('B5:C5');

    $avg_score = 0;
    if($totalCount > 0 && $tot_score && $completeCount){
        $avg_score = round($tot_score / $completeCount);
    }
    $sheet->setCellValue("D5", "평균점수");
    $sheet->setCellValue("E5", $avg_score);
	$sheet->mergeCells('E5:F5');
    
	$now = date("ymd");
	
	// Rename sheet
	$sheet->setTitle("Result");

	// 문서 열어볼시 미리 선택되어지는 셀 설정
	$sheet->setSelectedCellByColumnAndRow(0, 1);

	// 엑셀 파일 오픈시 활성화될 시트
	$spreadsheet->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition:attachment;filename='.$now.'_result.xlsx');
	header('Cache-Control: max-age=0');

	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');
	
	exit;
	
	function num2alpha($n)
	{
		for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
		$r = chr($n%26 + 0x41) . $r;
		return $r;
	}
?>