<?php
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	include_once(CLASS_PATH . "/el.class.lib");

    $elClass = new ELClass();
    $co_info = $elClass->getCourseInfo($_GET['c']);

    if(!isset($co_info['idx'])){
		echo "<script>location.href = '/?page=el_courseClosedList';</script>";
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
	
    $sheet->setCellValue("A1", "Result Report");
    $sheet->mergeCells('A1:F1');
    $sheet->setCellValue("A2", "과정명");
    $sheet->setCellValue("B2", $co_info['co_title']);
    $sheet->mergeCells('B2:F2');

    $sheet->setCellValue("A3", "작성일");
    $sheet->setCellValue("B3", $co_info['co_dt_create']);
	$sheet->mergeCells('B3:F3');

	$sql = "SELECT * FROM course_result WHERE cor_co_id = {$co_info['idx']} ";
    $row = $DB->GetAll($sql);

    $totalCount = count($row);
    $completeCount = 0;
    $tot_score = 0;
	
    $sheet->setCellValue("A4", "총인원");
    $sheet->setCellValue("B4", $totalCount);
	$sheet->mergeCells('B4:C4');
	
	// 컨텐츠 리스트 정보
	$sql = "SELECT 
                cc.cc_ct_id as ct_id, 
				ct.ct_title 
            FROM 
                course_contents_close cc,
				contents ct 
			WHERE cc.cc_co_id = {$co_info['idx']} AND cc.cc_ct_id = ct.idx 
			ORDER BY cc_order ASC ";
	$contents_list = $DB->GetAll($sql);

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

	$rowcell = 6;
	for ($i=0; $i < count($contents_list) ; $i++) { 
		$sheet->setCellValue(num2alpha($rowcell+$i)."7", $contents_list[$i]['ct_title']);
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
        $sheet->setCellValue("A".$cell, $row[$i]['cor_group_name']);
        $sheet->setCellValue("B".$cell, $row[$i]['cor_team_name']);
        $sheet->setCellValue("C".$cell, $row[$i]['cor_ur_email']);
		$sheet->setCellValue("D".$cell, $row[$i]['cor_ur_name']);
		
		if($row[$i]['cor_complete']){
			$completeCount++;
			$tot_score += $row[$i]['cor_score'];
		}

        $sheet->setCellValue("E".$cell, ($row[$i]['cor_complete'])?'O':'X');
		$sheet->setCellValue("F".$cell, $row[$i]['cor_score']);
		
		// 각 컨텐츠 점수
		$ct_list = explode(',', $row[$i]['cor_ct_res']);

		for ($j=0; $j < count($ct_list) ; $j++) {
			$sheet->setCellValue(num2alpha($rowcell+$j).$cell, $ct_list[$j]);
		}

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
	header('Content-Disposition:attachment;filename='.$now.'_closed_result.xlsx');
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