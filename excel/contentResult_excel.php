<?php
	ini_set('memory_limit','-1');
	ini_set('max_execution_time', '0');
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	include_once(CLASS_PATH . "/el.class.lib");

    $elClass = new ELClass();
    $ct_info = $elClass->getContentInfo($_GET['ct']);

    if(!isset($ct_info['idx'])){
		echo "<script>location.href = '/?page=el_contentList';</script>";
		return;
    }

    // 평균
    $sql = "SELECT 
                ROUND(AVG(ctr_score),1) as score 
            FROM 
                contents_test_result
            WHERE ctr_ct_id = {$_GET['ct']} 
            GROUP BY ctr_ct_id ";
    $ct_avg = $DB->GetOne($sql);

    // 코드정보
    $sql = "SELECT * FROM code_type ORDER BY code_name ASC ";
    $code_list = $DB->GetAll($sql);
	
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
    
    $sheet->getStyle('A1:A6')->applyFromArray(
		array(
			'font' => array(
				'bold' => true,
				'size' => 11
			),
			'alignment' => $style_center
		)
    );
    $sheet->getStyle('C4:C6')->applyFromArray(
		array(
			'font' => array(
				'bold' => true,
				'size' => 11
            ),
			'alignment' => $style_center
		)
    );
    $sheet->getStyle('B2:B6')->applyFromArray(
		array(
			'alignment' => $style_left
		)
    );
    $sheet->getStyle('D4:D6')->applyFromArray(
		array(
			'alignment' => $style_left
		)
    );
    
    $sheet->setCellValue("A1", "Result Report");
    $sheet->mergeCells('A1:D1');
    $sheet->setCellValue("A2", "제목");
    $sheet->setCellValue("B2", $ct_info['ct_title']);
    $sheet->mergeCells('B2:D2');

    $sheet->setCellValue("A3", "강의자");
    $sheet->setCellValue("B3", $ct_info['ct_speaker']);
    $sheet->mergeCells('B3:D3');
    
    $sheet->setCellValue("A4", "질환");
    $sheet->setCellValue("B4", codeToString($ct_info['ct_code_di'], $code_list));

    $sheet->setCellValue("A5", "제품");
    $sheet->setCellValue("B5", codeToString($ct_info['ct_code_pd'], $code_list));

    $sheet->setCellValue("A6", "평균 점수");
    $sheet->setCellValue("B6", $ct_avg['score']);

    $sheet->setCellValue("C4", "신입/경력");
    $sheet->setCellValue("D4", codeToString($ct_info['ct_code_gd'], $code_list));

    $sheet->setCellValue("C5", "직책");
    $sheet->setCellValue("D5", codeToString($ct_info['ct_code_lv'], $code_list));

    $sheet->setCellValue("C6", "작성일");
    $sheet->setCellValue("D6", $ct_info['ct_dt_create']);

	// 댓글 가져오기
	$sql = "SELECT 
                cm_comment,
                cm_dt_create 
            FROM 
                contents_msg 
			WHERE cm_ct_id = {$_GET['ct']} 
			ORDER BY cm_dt_create DESC ";
	$cm_list = $DB->GetAll($sql);
	
	$sheet->setCellValue("A8", "댓글");
	$sheet->mergeCells('A8:D8');
	$sheet->getStyle("A8")->applyFromArray(
		array(
			'alignment' => $style_center
		)
	);

	$cell = 9;
	for ($i=0; $i < count($cm_list) ; $i++) { 
        $sheet->setCellValue("A".$cell, $cm_list[$i]['cm_dt_create']);
        $sheet->setCellValue("B".$cell, $cm_list[$i]['cm_comment']);
        $sheet->mergeCells("B".$cell.":"."D".$cell);
        $sheet->getStyle("B".$cell)->applyFromArray(
            array(
                'alignment' => $style_center
            )
        );
        $cell++;
	}

	// 너비
	$sheet->getColumnDimension("A")->setWidth(20);
    $sheet->getColumnDimension("B")->setWidth(20);
    $sheet->getColumnDimension("C")->setWidth(20);
    $sheet->getColumnDimension("D")->setWidth(20);
	
	// Rename sheet
	$sheet->setTitle("Result");

	// 다음 시트 
	$sheet2 = $spreadsheet->createSheet(1);

	$sheet2->setCellValue("A1", "수강자 리스트");
	$sheet2->mergeCells('A1:I1');
	$sheet2->setCellValue("A2", "최상위조직");
	$sheet2->setCellValue("B2", "그룹명");
	$sheet2->setCellValue("C2", "하위조직");
	$sheet2->setCellValue("D2", "팀명");
	$sheet2->setCellValue("E2", "성명");
	$sheet2->setCellValue("F2", "수강유무");
	$sheet2->setCellValue("G2", "Test 점수");
	$sheet2->setCellValue("H2", "수강 완료 날짜");
	$sheet2->setCellValue("I2", "Test 완료 날짜");

	// 수강자 
    $sql = "SELECT 
			cp.idx, cp.cp_ur_id, cp.cp_play_sec, cp.cp_dt_update,
			ifnull(ctr.ctr_score, '-') AS score, ifnull(ctr.ctr_dt_update, '-') AS ctr_dt_update, 
			ur.ur_name, ur.ur_group_high, ur.ur_group_low, ur.ur_team, 
			ifnull(ud.unit_name, '-') AS unit
		FROM
			contents ct, 	
			s3_data s3,
			contents_played cp
			LEFT JOIN contents_test_result ctr
				ON cp.cp_ct_id = {$_GET['ct']}
				AND cp.cp_ct_id = ctr.ctr_ct_id
				AND cp.cp_ur_id = ctr.ctr_ur_id,
			user_data ur
			LEFT JOIN unit_data ud
				ON ur.ur_unit = ud.idx 
		WHERE 
			cp.cp_ct_id = {$_GET['ct']} 
			AND ct.idx = cp.cp_ct_id 
			AND ct.ct_s3_file = s3.idx 
			AND cp.cp_play_sec+5 >= s3.s3_play_sec 
			AND cp.cp_ur_id = ur.idx 
			AND ur.ur_state = 1 
			AND ur.ur_hidden = 0
		ORDER BY ur.ur_name ASC";
	$contentRes = $DB->GetAll($sql);

	$cell = 3;
	for ($i=0; $i < count($contentRes) ; $i++) { 
        $sheet2->setCellValue("A".$cell, $contentRes[$i]['ur_group_high']);
        $sheet2->setCellValue("B".$cell, $contentRes[$i]['unit']);
        $sheet2->setCellValue("C".$cell, $contentRes[$i]['ur_group_low']);
        $sheet2->setCellValue("D".$cell, $contentRes[$i]['ur_team']);
        $sheet2->setCellValue("E".$cell, $contentRes[$i]['ur_name']);
        $sheet2->setCellValue("F".$cell, $contentRes[$i]['score']!='-'?'O':'-');
        $sheet2->setCellValue("G".$cell, $contentRes[$i]['score']);
        $sheet2->setCellValue("H".$cell, $contentRes[$i]['cp_dt_update']);
        $sheet2->setCellValue("I".$cell, $contentRes[$i]['ctr_dt_update']);
        
        $cell++;
	}

	// 너비
	$sheet2->getColumnDimension("A")->setWidth(14);
    $sheet2->getColumnDimension("B")->setWidth(9);
    $sheet2->getColumnDimension("C")->setWidth(16);
    $sheet2->getColumnDimension("D")->setWidth(16);
    $sheet2->getColumnDimension("E")->setWidth(9);
    $sheet2->getColumnDimension("F")->setWidth(9);
	$sheet2->getColumnDimension("G")->setWidth(9);
	$sheet2->getColumnDimension("H")->setWidth(18);
	$sheet2->getColumnDimension("I")->setWidth(18);
	
	$sheet2->getStyle('A1:I2')->applyFromArray(
		array(
			'font' => array(
				'bold' => true,
				'size' => 11
			),
			'alignment' => $style_center
		)
	);
	
	$sheet2->setTitle("User");

	// 문서 열어볼시 미리 선택되어지는 셀 설정
	$sheet->setSelectedCellByColumnAndRow(0, 0);
	$sheet2->setSelectedCellByColumnAndRow(0, 0);

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
    
    function codeToString($_s, $_codes){
        
        $temp = $_s;
        $str = '';
        if(strlen($temp)){
            $temp = str_replace('X', '', $temp);
            $temp = substr($temp, 0, -1);
            $arr = explode(',', $temp);
            
            for ($i=0; $i < count($arr); $i++) { 
                for ($j=0; $j < count($_codes); $j++) { 
                    if($_codes[$j]['idx'] == $arr[$i]){
                        $str .= $_codes[$j]['code_name'] . ",";
                        break;
                    }
                }
            }
            $str = substr($str, 0, -1);
        }
        
        return $str;
    }
?>