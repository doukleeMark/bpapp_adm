<?php
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');

    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	$sql = "select p.*, u.ur_id, u.ur_name, u.ur_team, u.ur_level ";
	$sql .= "from user_data u, point_data p ";
	$sql .= "where u.idx = p.ptd_user AND u.ur_level!=10 AND u.ur_hidden=0 order by idx desc limit 5000 ";
    $row = $DB->GetAll($sql);
    
    $cells = array(
        'A' => array(6, 'idx', 'No.'),
        'B' => array(23, 'email',  'Email'),
        'C' => array(16, 'name', 'Name'),
        'D' => array(13, 'team', 'Team'),
        'E' => array(9, 'level', 'Level'),
        'F' => array(27, 'event', 'Event'),
        'G' => array(12, 'point', 'Point'),
        'H' => array(13, 'total', 'Total Point'),
        'I' => array(20, 'date', 'Date')
    );

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

    // header
    foreach ($cells as $key => $val) {
        $cellName = $key.'1';
        $sheet->getColumnDimension($key)->setWidth($val[0]);
        $sheet->getRowDimension('1')->setRowHeight(20);
        $sheet->setCellValue($cellName, $val[2]);
        $sheet->getStyle($cellName)->getFont()->setBold(true);
        $sheet->getStyle($cellName)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cellName)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }

    $cell = 2;
    for($i=0; $i<count($row);$i++){
		
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

		$sheet->setCellValue("A$cell", $row[$i]['idx']) // NO
			->setCellValue("B$cell", $row[$i]['ur_id']) 
			->setCellValue("C$cell", $row[$i]['ur_name'])
			->setCellValue("D$cell", $row[$i]['ur_team'])
			->setCellValue("E$cell", $levelText)
			->setCellValue("F$cell", $row[$i]['ptd_event'])
			->setCellValue("G$cell", $row[$i]['ptd_point'])
			->setCellValue("H$cell", $row[$i]['ptd_total'])
			->setCellValue("I$cell", $row[$i]['ptd_date']);

		$cell++;
	}
    
    $filename = date("ymd").'_pointReport';

    // Rename worksheet
    $spreadsheet->getActiveSheet()->setTitle('Point');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Xlsx)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');

    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;

?>