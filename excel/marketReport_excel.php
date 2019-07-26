<?php
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	if(!isset($_GET['unit']) || $_GET['unit'] == 0){
		echo "<script>location.href = '/?page=report_market';</script>";
		return;
	}

	$sql = "select co.col_year, co.col_month, co.col_week, u.ur_team, u.ur_unit, CONCAT(co.col_year, '년', co.col_month, '월') as month, sum(co.col_value1) as val1, sum(co.col_value2) as val2, sum(co.col_value3) as val3, sum(co.col_value4) as val4, sum(co.col_value5) as val5 from collect_data co, user_data u where co.col_user = u.idx AND u.ur_team != '' AND u.ur_level < 10 AND u.ur_hidden = 0 AND u.ur_unit = {$_GET['unit']} group by month, u.ur_team ";
	$row = $DB->GetAll($sql);

	if(count($row) == 0){
		echo "<script>alert('해당 데이터가 없습니다.');</script>";
		echo "<script>location.href = '/?page=report_market';</script>";
		return;
	}

	$unit_data = array(
		array('시나롱 세미나', 'Sante Family'),
		array('스토가 신규과 단일기관 세미나', '맥스핌(호흡기내과, 혈종내과, 감염내과) 세미나', '메이액트 단일 및 일대일 세미나', '뮤코미스트 단일기관 세미나'),
		array('세미나'),
		array('[하베DAY] Mission 수행처', '스토가 세미나(단일, 다기관)', '메이액트 세미나(단일, 다기관)', 'Sante Family 신규처/증액처 수'),
		array('시나롱 세미나', 'Sante Family 랜딩처 수', '스토가 세미나(단일, 다기관)', '메이액트 세미나(단일, 다기관)'),
		array('HD Dialyser 신규처/Machine 신규처', '헤모시스 직납전환처', 'PD 신환자 수', 'EPO 신규처', '칼세파라 신규처'),
		array('Pharm st. 가입처', 'Pharm st. 매출 발생처'),
		array('부스파 & 푸로작 세미나(단일, 다기관)', '스트라테라 세미나(단일, 다기관)')
	);
	
	$sql = "select unit_name from unit_data where idx={$_GET['unit']} ";
	$unitRes = $DB->GetOne($sql);
	
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

	$sheet->getStyle('2:4')->applyFromArray(
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

	$sheet->getStyle('A')->applyFromArray(
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

	$sheet->getRowDimension(1)->setRowHeight(30);
	$sheet->getRowDimension(2)->setRowHeight(30);
	$sheet->getRowDimension(3)->setRowHeight(30);
	$sheet->getRowDimension(4)->setRowHeight(30);

	$sheet->setCellValue("A1", $unitRes['unit_name']);
	$sheet->setCellValue("A2", "팀명");
	$sheet->mergeCells('A2:A4');
	$sheet->getColumnDimension("A")->setWidth(17);

	$cell = 5;

	$teamList = array();
	for($i=0; $i<count($row);$i++){
		if(!in_array($row[$i]['ur_team'], $teamList)){
			array_push($teamList, $row[$i]['ur_team']);
			$sheet->setCellValue("A{$cell}", $row[$i]['ur_team']);
			$sheet->getRowDimension($cell)->setRowHeight(25);
			$sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
			$cell++;
		}
	}

	$itemCnt = count($unit_data[(int)$_GET['unit']-1]);
	$cell = 5;
	$rowcell = 1;
	$prev = '';
	for($i=0; $i<count($row);$i++){
		if($i == 0){
			// 첫 주차
			$sheet->setCellValue(num2alpha($rowcell)."2", $row[$i]['month']);
			$sheet->mergeCells(num2alpha($rowcell).'2:'.num2alpha($rowcell+$itemCnt-1).'2');
			for ($j=0; $j < $itemCnt; $j++) { 
				$sheet->setCellValue(num2alpha($rowcell+$j)."3", $unit_data[(int)$_GET['unit']-1][$j]);
				$sheet->setCellValue(num2alpha($rowcell+$j)."4", "DATA_".($j+1));
				$sheet->getColumnDimension(num2alpha($rowcell+$j))->setWidth(17);
			}
			
			for($j=0; $j<count($teamList);$j++){
				if($teamList[$j]==$row[$i]['ur_team']){
					for($k=0; $k < $itemCnt; $k++) { 
						$sheet->setCellValue(num2alpha($rowcell+$k).($j+5), $row[$i][('val'.($k+1))]);
					}
				}else{
					for($k=0; $k < $itemCnt; $k++) { 
						$sheet->setCellValue(num2alpha($rowcell+$k).($j+5), 0);
					}
				}
			}

		}else{
			// 같은 주차면..
			if($prev == $row[$i]['month']){
				for($j=0; $j<count($teamList);$j++){
					if($teamList[$j]==$row[$i]['ur_team']){
						for($k=0; $k < $itemCnt; $k++) { 
							$sheet->setCellValue(num2alpha($rowcell+$k).($j+5), $row[$i][('val'.($k+1))]);
						}
					}
				}
			}else{
				// 새로운 주차
				$rowcell = $rowcell + $itemCnt;
				$sheet->setCellValue(num2alpha($rowcell)."2", $row[$i]['month']);
				$sheet->mergeCells(num2alpha($rowcell).'2:'.num2alpha($rowcell+$itemCnt-1).'2');
				for ($j=0; $j < $itemCnt; $j++) { 
					$sheet->setCellValue(num2alpha($rowcell+$j)."3", $unit_data[(int)$_GET['unit']-1][$j]);
					$sheet->setCellValue(num2alpha($rowcell+$j)."4", "DATA_".($j+1));
					$sheet->getColumnDimension(num2alpha($rowcell+$j))->setWidth(17);
				}

				for($j=0; $j<count($teamList);$j++){
					if($teamList[$j]==$row[$i]['ur_team']){
						for($k=0; $k < $itemCnt; $k++) { 
							$sheet->setCellValue(num2alpha($rowcell+$k).($j+5), $row[$i][('val'.($k+1))]);
						}
					}else{
						for($k=0; $k < $itemCnt; $k++) { 
							$sheet->setCellValue(num2alpha($rowcell+$k).($j+5), 0);
						}
					}
				}
			}
		}
		$prev = $row[$i]['month'];
	}

	// 배경색 적용
	$sheet->getStyle('A2:'.num2alpha($rowcell+$itemCnt-1).'4')->applyFromArray(
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

	$sheet->mergeCells('A1:'.num2alpha($rowcell+$itemCnt-1).'1');

	// 다중 셀 보더 스타일 적용
	foreach(range('A',num2alpha($rowcell+$itemCnt-1)) as $i => $cell){
		for($i=0;$i<count($teamList)+4;$i++){
			$sheet->getStyle($cell.($i+1))->applyFromArray( $headBorder );
		}
	}
	
	$now = date("ymd");
	
	// Rename sheet
	$sheet->setTitle("Market");

	// 문서 열어볼시 미리 선택되어지는 셀 설정
	$sheet->setSelectedCellByColumnAndRow(0, 1);

	// 엑셀 파일 오픈시 활성화될 시트
	$spreadsheet->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition:attachment;filename='.$now.'_market.xlsx');
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