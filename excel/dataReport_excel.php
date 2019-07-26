<?php
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	$sql = "select tr.idx, u.ur_name, tr.tr_unit, tr.tr_team, tr.tr_date, dt.dt_title from data_info dt, data_tracking tr, user_data u ";
	$sql .= "where tr.tr_user = u.idx ";
	$sql .= "AND dt.idx = tr.tr_data ";
	$sql .= "AND u.ur_level!=10 AND u.ur_hidden=0 ";
	$sql .= "order by tr.idx desc ";
	$row = $DB->GetAll($sql);

	$sql = "select * from unit_data";
	$units = $DB->GetAll($sql);

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
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			)
		)
	);

	$sheet->setCellValue("A1", "No.");
	$sheet->getColumnDimension("A")->setWidth(6);
	$sheet->setCellValue("B1", "Name");
	$sheet->getColumnDimension("B")->setWidth(10);
	$sheet->setCellValue("C1", "Group");
	$sheet->getColumnDimension("C")->setWidth(10);
	$sheet->setCellValue("D1", "Team");
	$sheet->getColumnDimension("D")->setWidth(15);
	$sheet->setCellValue("E1", "Title");
	$sheet->getColumnDimension("E")->setWidth(60);
	$sheet->setCellValue("F1", "Date");
	$sheet->getColumnDimension("F")->setWidth(18);
		
	$cell = 2;
	for($i=0; $i<count($row);$i++){
		$no = $i+1;

		// 유닛명
		$unitName = "-";
		for ( $j = 0 ; $j < count($units) ; $j++ ) { 
			if($row[$i]['tr_unit'] == $units[$j]['idx']){
				$unitName = $units[$j]['unit_name'];
				break;
			}
		}
		
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue("A$cell", $row[$i]['idx'])
			->setCellValue("B$cell", $row[$i]['ur_name']) 
			->setCellValue("C$cell", $unitName)
			->setCellValue("D$cell", $row[$i]['tr_team'])
			->setCellValue("E$cell", $row[$i]['dt_title'])
			->setCellValue("F$cell", $row[$i]['tr_date']);

		$cell++;
	}

	$now = date("ymd");

	// Rename sheet
	$sheet->setTitle("Data Report");

	// 문서 열어볼시 미리 선택되어지는 셀 설정
	$sheet->setSelectedCellByColumnAndRow(0, 1);

	// 엑셀 파일 오픈시 활성화될 시트
	$spreadsheet->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition:attachment;filename='.$now.'_DataReport.xlsx');
	header('Cache-Control: max-age=0');

	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');
	
	exit;
?>