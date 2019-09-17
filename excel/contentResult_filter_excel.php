<?php
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');

	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

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

	$sheet->setCellValue("A1", "최상위조직");
	$sheet->setCellValue("B1", "그룹명");
	$sheet->setCellValue("C1", "하위조직");
	$sheet->setCellValue("D1", "팀명");
	$sheet->setCellValue("E1", "성명");
	$sheet->setCellValue("F1", "컨텐츠명");
	$sheet->setCellValue("G1", "Test 점수");
	$sheet->setCellValue("H1", "수강 완료 날짜");
	$sheet->setCellValue("I1", "Test 완료 날짜");

	// 수강자 
    $sql = "SELECT 
			cp.idx, cp.cp_ur_id, cp.cp_play_sec, cp.cp_dt_update,
			ifnull(ctr.ctr_score, '-') AS score, ifnull(ctr.ctr_dt_update, '-') AS ctr_dt_update, 
			ur.ur_name, ur.ur_group_high, ur.ur_group_low, ur.ur_team, 
			ifnull(ud.unit_name, '-') AS unit, 
			ct.ct_title, ct.ct_test_count
		FROM
			contents ct, 	
			s3_data s3,
			contents_played cp
			LEFT JOIN contents_test_result ctr
				ON cp.cp_ct_id = ctr.ctr_ct_id
				AND cp.cp_ur_id = ctr.ctr_ur_id,
			user_data ur
			LEFT JOIN unit_data ud
				ON ur.ur_unit = ud.idx 
		WHERE 
			ct.idx = cp.cp_ct_id 
			AND ct.ct_delete = 0 
			AND ct.ct_open = 1 
			AND ct.ct_s3_file = s3.idx 
			AND cp.cp_play_sec+5 >= s3.s3_play_sec 
			AND cp.cp_ur_id = ur.idx 
			AND ur.ur_state = 1 
			AND ur.ur_hidden = 0 ";
		if(isset($_GET['unit']) && (int)$_GET['unit'] > 0) $sql .= "AND ur.ur_unit = '{$_GET['unit']}' ";
		if(isset($_GET['team']) && strlen($_GET['team'])) $sql .= "AND ur.ur_team = '{$_GET['team']}' ";
		if(isset($_GET['name']) && strlen(trim($_GET['name']))) $sql .= "AND ur.ur_name = '" . trim($_GET['name']) . "' ";
	$sql .=	"ORDER BY ur.ur_name ASC";
	$contentRes = $DB->GetAll($sql);

	$cell = 2;

    for ($i=0; $i < count($contentRes) ; $i++) {
        if ($contentRes[$i]['ct_test_count'] == "0")
            $contentRes[$i]['score'] = "0";
    }

    for ($i=0; $i < count($contentRes) ; $i++) {
        $sheet->setCellValue("A".$cell, $contentRes[$i]['ur_group_high']);
        $sheet->setCellValue("B".$cell, $contentRes[$i]['unit']);
        $sheet->setCellValue("C".$cell, $contentRes[$i]['ur_group_low']);
        $sheet->setCellValue("D".$cell, $contentRes[$i]['ur_team']);
        $sheet->setCellValue("E".$cell, $contentRes[$i]['ur_name']);
        $sheet->setCellValue("F".$cell, $contentRes[$i]['ct_title']);
        $sheet->setCellValue("G".$cell, $contentRes[$i]['score']);
        $sheet->setCellValue("H".$cell, $contentRes[$i]['cp_dt_update']);
        $sheet->setCellValue("I".$cell, $contentRes[$i]['ctr_dt_update']);
        
        $cell++;
	}

	// 너비
	$sheet->getColumnDimension("A")->setWidth(14);
    $sheet->getColumnDimension("B")->setWidth(9);
    $sheet->getColumnDimension("C")->setWidth(16);
    $sheet->getColumnDimension("D")->setWidth(16);
    $sheet->getColumnDimension("E")->setWidth(9);
    $sheet->getColumnDimension("F")->setWidth(16);
	$sheet->getColumnDimension("G")->setWidth(9);
	$sheet->getColumnDimension("H")->setWidth(18);
	$sheet->getColumnDimension("I")->setWidth(18);
	
	$sheet->getStyle('A1:I1')->applyFromArray(
		array(
			'font' => array(
				'bold' => true,
				'size' => 11
			),
			'alignment' => $style_center
		)
	);
	
	$sheet->setTitle("Result");

	// 문서 열어볼시 미리 선택되어지는 셀 설정
	$sheet->setSelectedCellByColumnAndRow(0, 0);
	$sheet->setSelectedCellByColumnAndRow(0, 0);

	// 엑셀 파일 오픈시 활성화될 시트
	$spreadsheet->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition:attachment;filename='.date("ymd").'_content_result.xlsx');
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