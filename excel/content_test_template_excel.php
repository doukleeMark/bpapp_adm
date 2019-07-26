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

	$sheet->setCellValue("A1", "문제");
	$sheet->getColumnDimension("A")->setWidth(30);
	$sheet->setCellValue("B1", "보기1");
	$sheet->getColumnDimension("B")->setWidth(16);
	$sheet->setCellValue("C1", "보기2");
	$sheet->getColumnDimension("C")->setWidth(16);
	$sheet->setCellValue("D1", "보기3");
	$sheet->getColumnDimension("D")->setWidth(16);
	$sheet->setCellValue("E1", "보기4");
	$sheet->getColumnDimension("E")->setWidth(16);
	$sheet->setCellValue("F1", "정답");
	$sheet->getColumnDimension("F")->setWidth(10);
		
	$now = date("ymd");

	// Rename sheet
	$sheet->setTitle("Test");

	// 문서 열어볼시 미리 선택되어지는 셀 설정
	$sheet->setSelectedCellByColumnAndRow(0, 1);

	// 엑셀 파일 오픈시 활성화될 시트
	$spreadsheet->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition:attachment;filename=test_upload_template.xlsx');
	header('Cache-Control: max-age=0');

	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');
	
	exit;
?>