<?php
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');

	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	include_once(CLASS_PATH . "/survey.class.lib");
	$surveyClass = new SurveyClass();
	
	$surveyInfo = $surveyClass->getSurveyInfo($_GET['idx']);
	$surveySub = $surveyClass->getSurveySubInfo($_GET['idx']);


	$sql = "select sd.*, u.ur_id, u.ur_name from survey_data sd, user_data u ";
	$sql .= "where sd.svd_user = u.idx AND svd_idx={$_GET['idx']} order by svd_user, svd_page ";
	$surveyRes = $DB->GetAll($sql);

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

	$sheet->getStyle('1')->applyFromArray(
		array(
			'font' => array(
				'bold' => true,
				'size' => 11
			),
			'alignment' => array(
				'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			)
		)
	);

	$sheet->getStyle('3:4')->applyFromArray(
		array(
			'font' => array(
				'bold' => true,
				'size' => 11
			),
			'alignment' => array(
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			)
		)
	);

	$sheet->getStyle('A:B')->applyFromArray(
		array(
			'font' => array(
				'bold' => true,
				'size' => 11
			),
			'alignment' => array(
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			)
		)
	);

	// Quick Poll 제목
	$sheet->setCellValue("A1", "Quick Poll Title");
	$sheet->mergeCells("A1:B1");
	$sheet->setCellValue("C1", $surveyInfo['svf_title']);
	
	$cell = 3;

	// ID
	$sheet->setCellValue("A3", "ID");
	$sheet->mergeCells("A3:A4");
	$sheet->getColumnDimension("A")->setWidth(17);

	// NAME
	$sheet->setCellValue("B3", "NAME");
	$sheet->mergeCells("B3:B4");
	$sheet->getColumnDimension("B")->setWidth(17);

	$rowcell = 2;

	// 퀴즈 제목
	for($i=0;$i<count($surveySub);$i++){

		// Q1 ~
		$sheet->setCellValue(num2alpha($rowcell+($i*5))."3", "Q".($i+1));
		$sheet->getComment(num2alpha($rowcell+($i*5))."3")->getText()->createTextRun($surveySub[$i]['svs_question']);
		$sheet->mergeCells(num2alpha($rowcell+($i*5))."3:".num2alpha($rowcell+($i*5)+4)."3");

		// Item1 ~
		for ($j=0;$j<5;$j++) { 
			$sheet->setCellValue(num2alpha($rowcell+($i*5)+$j)."4", "Item".($j+1));
			$sheet->getComment(num2alpha($rowcell+($i*5)+$j)."4")->getText()->createTextRun($surveySub[$i]['svs_item_'.($j+1)]);
		}
	}

	// ID,NAME 값 채우기
	$cell = 5;
	$idList = array();
	$nameList = array();
	for($i=0; $i<count($surveyRes);$i++){
		if(!in_array($surveyRes[$i]['ur_id'], $idList)){
			array_push($idList, $surveyRes[$i]['ur_id']);
			array_push($nameList, $surveyRes[$i]['ur_name']);
			$sheet->setCellValue("A{$cell}", $surveyRes[$i]['ur_id']);
			$sheet->setCellValue("B{$cell}", $surveyRes[$i]['ur_name']);
			$sheet->getRowDimension($cell)->setRowHeight(25);
			$sheet->getStyle($cell)->applyFromArray(
				array(
					'alignment' => array(
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
						'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
					)
				)
			);
			$cell++;
		}
	}

	// item 값 채우기
	$cell = 5;
	$rowcell = 2;
	// 누적 카운트
	$result = array();
	// 개인 카운트
	$count = array();
	for($i=0; $i<count($surveyRes);$i++){
		
		for($j=0; $j<count($idList);$j++){
			if($surveyRes[$i]['ur_id'] == $idList[$j]){

				// $row 위치
				$rowPos = $cell + $j;

				// item 값
				if(isset($count[$j][($surveyRes[$i]['svd_page']-1)][($surveyRes[$i]['svd_select']-1)]))
					$count[$j][($surveyRes[$i]['svd_page']-1)][($surveyRes[$i]['svd_select']-1)]++;
				else
					$count[$j][($surveyRes[$i]['svd_page']-1)][($surveyRes[$i]['svd_select']-1)] = 1;

				$sheet->setCellValue(num2alpha($rowcell+(($surveyRes[$i]['svd_page']-1)*5)+($surveyRes[$i]['svd_select']-1)).$rowPos, $count[$j][($surveyRes[$i]['svd_page']-1)][($surveyRes[$i]['svd_select']-1)]);

				break;
			}
		}
		if(isset($result[($surveyRes[$i]['svd_page']-1)][($surveyRes[$i]['svd_select']-1)]))
			$result[($surveyRes[$i]['svd_page']-1)][($surveyRes[$i]['svd_select']-1)]++;
		else
			$result[($surveyRes[$i]['svd_page']-1)][($surveyRes[$i]['svd_select']-1)] = 1;
	}	

	$cell = 5 + count($idList) + 1;
	
	// count
	$sheet->setCellValue("A".$cell, "COUNT");
	$sheet->mergeCells("A".$cell.":B".$cell);
	$sheet->getRowDimension($cell)->setRowHeight(25);
	$sheet->getStyle($cell)->applyFromArray(
		array(
			'alignment' => array(
				'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			)
		)
	);

	for($i=0; $i<count($surveySub);$i++){

		for($j=0; $j<5;$j++){
			// count 값 채우기
			if(isset($result[$i][$j])) $val = $result[$i][$j];
			else $val = 0;
			$sheet->setCellValue(num2alpha($rowcell+($i*5)+$j).$cell, $val);
		}
	}

	// 타이틀 
	$sheet->mergeCells('C1:'.num2alpha($rowcell+(count($surveySub)*5)-1).'1');

	// 배경색 적용
	$sheet->getStyle('A3:'.num2alpha($rowcell+(count($surveySub)*5)-1).'4')->applyFromArray(
		array(
			'fill' => array(
				'type'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'color' => array('rgb'=>'F3F3F3')
			)
		)
	);
	
	$sheet->getStyle('A'.(count($idList)+6).':'.num2alpha($rowcell+(count($surveySub)*5)-1).(count($idList)+6))->applyFromArray(
		array(
			'fill' => array(
				'type'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'color' => array('rgb'=>'F3F3F3')
			)
		)
	);

	// 보더 스타일 지정
	$defaultBorder = array(
		'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		'color' => array('rgb'=>'888888')
	);
	$headBorder = array(
		'borders' => array(
			'bottom' => $defaultBorder,
			'left'   => $defaultBorder,
			'top'    => $defaultBorder,
			'right'  => $defaultBorder
		)
	);

	for($i=0; $i < ($rowcell+(count($surveySub)*5)); $i++) { 
		for ($j=0; $j < (count($idList)+2); $j++) { 
			$sheet->getStyle(num2alpha($i).($j+3))->applyFromArray( $headBorder );
		}
		$sheet->getStyle(num2alpha($i).(count($idList)+6))->applyFromArray( $headBorder );
	}

	$now = date("ymd");
	
	// Rename sheet
	$sheet->setTitle("Market");

	// 문서 열어볼시 미리 선택되어지는 셀 설정
	$sheet->setSelectedCellByColumnAndRow(0, 1);

	// 엑셀 파일 오픈시 활성화될 시트
	$spreadsheet->setActiveSheetIndex(0);
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition:attachment;filename='.$now.'_quickPoll.xlsx');
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