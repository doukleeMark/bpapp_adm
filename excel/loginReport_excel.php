<?php
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	$add_sql = "AND u.ur_level!=10 AND u.ur_hidden=0 ";
	$sql = "select l.idx, l.log_date, l.log_os, l.log_model, l.log_appver, u.ur_name, u.ur_id, ud.unit_name, u.ur_team, u.ur_level from system_log l, user_data u, unit_data ud where l.log_type = 'login' AND l.log_user = u.idx AND ud.idx = u.ur_unit ";
	$sql .= $add_sql;
	$sql .= "order by l.idx desc limit 5000 ";
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
	$sheet->setCellValue("B1", "Email");
	$sheet->getColumnDimension("B")->setWidth(23);
	$sheet->setCellValue("C1", "Name");
	$sheet->getColumnDimension("C")->setWidth(16);
	$sheet->setCellValue("D1", "Group");
	$sheet->getColumnDimension("D")->setWidth(16);
	$sheet->setCellValue("E1", "Team");
	$sheet->getColumnDimension("E")->setWidth(16);
	$sheet->setCellValue("F1", "Level");
	$sheet->getColumnDimension("F")->setWidth(13);
	$sheet->setCellValue("G1", "Device");
	$sheet->getColumnDimension("G")->setWidth(27);
	$sheet->setCellValue("H1", "OS");
	$sheet->getColumnDimension("H")->setWidth(12);
	$sheet->setCellValue("I1", "APP");
	$sheet->getColumnDimension("I")->setWidth(12);
	$sheet->setCellValue("J1", "Date");
	$sheet->getColumnDimension("J")->setWidth(20);
		
	$cell = 2;
	for($i=0; $i<count($row);$i++){
		$no = $i+1;
		
		switch($row[$i]['ur_level']){
			case '10':
				$levelText = "MASTER";
				break;
			case '9':
				$levelText = "ADMIN";
				break;
			case '3':
				$levelText = "PM";
				break;
			case '2':
				$levelText = "MR";
				break;
			default:
				$levelText = "";
		}

		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue("A$cell", $no)
			->setCellValue("B$cell", $row[$i]['ur_id']) 
			->setCellValue("C$cell", $row[$i]['ur_name'])
			->setCellValue("D$cell", $row[$i]['unit_name'])
			->setCellValue("E$cell", $row[$i]['ur_team'])
			->setCellValue("F$cell", $levelText)
			->setCellValue("G$cell", $row[$i]['log_model'])
			->setCellValue("H$cell", $row[$i]['log_os'])
			->setCellValue("I$cell", $row[$i]['log_appver'])
			->setCellValue("J$cell", $row[$i]['log_date']);

		$cell++;
	}

	$now = date("ymd");

	// Rename sheet
	$sheet->setTitle("loginReport");

	// 문서 열어볼시 미리 선택되어지는 셀 설정
	$sheet->setSelectedCellByColumnAndRow(0, 1);

	// 엑셀 파일 오픈시 활성화될 시트
	$spreadsheet->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition:attachment;filename='.$now.'_loginReport.xlsx');
	header('Cache-Control: max-age=0');

	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');
	
	exit;
?>