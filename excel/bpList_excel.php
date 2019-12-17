<?php
    ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');

	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	$sql = "select bu.*, l.bl_cnt from ";
	$sql .= "(select b.*, u.ur_name, u.ur_team from bp_data b, user_data u where b.bp_hidden = 0 AND b.bp_state != 0 AND b.bp_user = u.idx ) bu left outer join ";
	$sql .= "(select count(bl_bp) as bl_cnt, bl_bp from bp_like group by bl_bp) l on l.bl_bp = bu.idx ";
	$sql .= "order by bu.idx desc";
	$row = $DB->GetAll($sql);

	$sql = "select * from unit_data";
	$units = $DB->GetAll($sql);

	$sql = "select * from brand_data";
	$brands = $DB->GetAll($sql);
	
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
	$sheet->setCellValue("B1", "Title");
	$sheet->getColumnDimension("B")->setWidth(60);
	$sheet->setCellValue("C1", "Writer");
	$sheet->getColumnDimension("C")->setWidth(7);
	$sheet->setCellValue("D1", "Brand");
	$sheet->getColumnDimension("D")->setWidth(10);
	$sheet->setCellValue("E1", "Unit");
	$sheet->getColumnDimension("E")->setWidth(8);
	$sheet->setCellValue("F1", "Team");
	$sheet->getColumnDimension("F")->setWidth(16);
	$sheet->setCellValue("G1", "Choice");
	$sheet->getColumnDimension("G")->setWidth(8);
	$sheet->setCellValue("H1", "Like");
	$sheet->getColumnDimension("H")->setWidth(5);
	$sheet->setCellValue("I1", "Hit");
	$sheet->getColumnDimension("I")->setWidth(5);
    $sheet->setCellValue("J1", "Follow");
    $sheet->getColumnDimension("J")->setWidth(8);
	$sheet->setCellValue("K1", "Share");
	$sheet->getColumnDimension("K")->setWidth(9);
	$sheet->setCellValue("L1", "State");
	$sheet->getColumnDimension("L")->setWidth(15);
	$sheet->setCellValue("M1", "Date");
	$sheet->getColumnDimension("M")->setWidth(20);
		
	$cell = 2;
	for($i=0; $i<count($row);$i++){
		$no = $i+1;

		// 브랜드
		$brandName = "-";
		for ( $j = 0 ; $j < count($brands) ; $j++ ) { 
			if($row[$i]['bp_brand'] == $brands[$j]['idx']){
				$brandName = $brands[$j]['brand_name'];
				break;
			}
		}

		// 유닛명
		$unitName = "-";
		for ( $j = 0 ; $j < count($units) ; $j++ ) { 
			if($row[$i]['bp_unit'] == $units[$j]['idx']){
				$unitName = $units[$j]['unit_name'];
				break;
			}
		}
		
		// 초이스
		$choice = "";
		if((int)$row[$i]['bp_choice_ceo'] == 1)$choice .= "C,"; 
		if((int)$row[$i]['bp_choice_bon'] == 1)$choice .= "G,"; 
		if((int)$row[$i]['bp_choice_unit'] == 1)$choice .= "U,"; 
		if((int)$row[$i]['bp_choice_mkt'] == 1)$choice .= "M,"; 
		if((int)$row[$i]['bp_choice_mrt'] == 1)$choice .= "T,"; 
		if((int)$row[$i]['bp_choice_pm'] == 1)$choice .= "P,"; 
		if((int)$row[$i]['bp_choice_month'] == 1)$choice .= "MB,";

		if(strlen($choice) > 0)$choice = substr($choice, 0, -1);

		// Share
		if((int)$row[$i]['bp_state'] == 5)$share = "전체공개";
		else $share = "팀내공개";

		// 상태
		switch($row[$i]['bp_state']){
			case '1':
				$stateText = "팀내공개";
				break;
			case '2':
				$stateText = "승인대기";
				break;
			case '3':
				$stateText = "CP승인반려";
				break;
			case '4':
				$stateText = "CP승인대기";
				break;
			case '5':
				$stateText = "전체공개";
				break;
			default:
				$stateText = "";
		}

		// 타이틀
		$addTitle = "";
		if((int)$row[$i]['bp_teamfu'] == 1) $addTitle = "ⓣ ". $row[$i]['bp_title'];
		else if((int)$row[$i]['bp_new_fu'] == 1) $addTitle = "ⓕ ". $row[$i]['bp_title'];

		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue("A$cell", $no)
			->setCellValue("B$cell", ($addTitle.$row[$i]['bp_title'])) 
			->setCellValue("C$cell", $row[$i]['ur_name'])
			->setCellValue("D$cell", $brandName)
			->setCellValue("E$cell", $unitName)
			->setCellValue("F$cell", $row[$i]['ur_team'])
			->setCellValue("G$cell", $choice)
			->setCellValue("H$cell", $row[$i]['bl_cnt'])
			->setCellValue("I$cell", $row[$i]['bp_hit'])
            ->setCellValue("J$cell", $row[$i]['bp_new_fu'])
			->setCellValue("K$cell", $share)
			->setCellValue("L$cell", $stateText)
			->setCellValue("M$cell", $row[$i]['bp_date']);

		$cell++;
	}

	$now = date("ymd");

	// Rename sheet
	$sheet->setTitle("bpList");

	// 문서 열어볼시 미리 선택되어지는 셀 설정
	$sheet->setSelectedCellByColumnAndRow(0, 1);

	// 엑셀 파일 오픈시 활성화될 시트
	$spreadsheet->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition:attachment;filename='.$now.'_bpList.xlsx');
	header('Cache-Control: max-age=0');

	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');
	
	exit;
?>