<?php
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');

	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	$sql = "select u.ur_id, u.ur_name, u.ur_team, u.ur_tag, ud.unit_name ";
	$sql .= "from user_data u, unit_data ud ";
	$sql .= "where u.ur_unit = ud.idx AND u.ur_level!=10 AND u.ur_hidden=0 order by u.idx asc ";
	$row = $DB->GetAll($sql);

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
	$sheet->setCellValue("B1", "Unit");
	$sheet->getColumnDimension("B")->setWidth(16);
	$sheet->setCellValue("C1", "Team");
	$sheet->getColumnDimension("C")->setWidth(16);
	$sheet->setCellValue("D1", "Email");
	$sheet->getColumnDimension("D")->setWidth(23);
	$sheet->setCellValue("E1", "Name");
	$sheet->getColumnDimension("E")->setWidth(16);
	$sheet->setCellValue("F1", "Tag");
	$sheet->getColumnDimension("F")->setWidth(30);
		
	$cell = 2;
	for($i=0; $i<count($row);$i++){
		$no = count($row) - $i;

		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue("A$cell", $i+1)
			->setCellValue("B$cell", $row[$i]['unit_name']) 
			->setCellValue("C$cell", $row[$i]['ur_team'])
			->setCellValue("D$cell", $row[$i]['ur_id'])
			->setCellValue("E$cell", $row[$i]['ur_name'])
			->setCellValue("F$cell", $row[$i]['ur_tag']);

		$cell++;
	}

	$now = date("ymd");

	// Rename sheet
	$sheet->setTitle("userTag");

	// 문서 열어볼시 미리 선택되어지는 셀 설정
	$sheet->setSelectedCellByColumnAndRow(0, 1);

	// 엑셀 파일 오픈시 활성화될 시트
	$spreadsheet->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition:attachment;filename='.$now.'_userTagUpload.xlsx');
	header('Cache-Control: max-age=0');

	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');
	
	exit;
?>