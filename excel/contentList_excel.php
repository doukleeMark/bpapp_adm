<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');

require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

$sqlCode = "select * from code_type";
$rowCode = $DB->GetAll($sqlCode);
$code = array();

for($i = 0; $i < count($rowCode); $i++){
    $code[$rowCode[$i]['idx']] = $rowCode[$i]['code_name'];
}
$sql  = "SELECT";
$sql .= " c.idx, c.ct_code_pd, c.ct_code_di, c.ct_code_ss, c.ct_title, c.ct_speaker,  c.ct_hit, c.ct_dt_update, ";
$sql .= " if(c.ct_type='V',    'VIDEO', 'AUDIO')  ct_type,";
$sql .= " if(c.ct_open_type=0, '내부', '외부')      ct_open_type, ";
$sql .= " if(c.ct_open=0,      '공개하기', '공개')   ct_open,";
$sql .= " ifnull(sec_to_time(s3.s3_play_sec), 0) as play_time,";
$sql .= " ifnull(round(cr.rating, 1), '-') as rating";
$sql .= "      FROM contents c";
$sql .= " left join (select cr_ct_id, avg(cr_rating) as rating from contents_rating group by cr_ct_id) cr on c.idx = cr.cr_ct_id";
$sql .= " left join s3_data s3 on c.ct_s3_file = s3.idx";
$sql .= " where ct_delete = 0";
$sql .= " order by idx desc limit 5000 ";
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
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
        )
    )
);

$sheet->setCellValue("A1", "No.");
$sheet->getColumnDimension("A")->setWidth(6);
$sheet->setCellValue("B1", "제품");
$sheet->getColumnDimension("B")->setWidth(16);
$sheet->setCellValue("C1", "질환");
$sheet->getColumnDimension("C")->setWidth(16);
$sheet->setCellValue("D1", "Sales Skill");
$sheet->getColumnDimension("D")->setWidth(16);
$sheet->setCellValue("E1", "Title");
$sheet->getColumnDimension("E")->setWidth(23);
$sheet->setCellValue("F1", "Speaker");
$sheet->getColumnDimension("F")->setWidth(16);
$sheet->setCellValue("G1", "Play Time");
$sheet->getColumnDimension("G")->setWidth(16);
$sheet->setCellValue("H1", "Type");
$sheet->getColumnDimension("H")->setWidth(16);
$sheet->setCellValue("I1", "Rating");
$sheet->getColumnDimension("I")->setWidth(13);
$sheet->setCellValue("J1", "Hit");
$sheet->getColumnDimension("J")->setWidth(27);
$sheet->setCellValue("K1", "Date");
$sheet->getColumnDimension("K")->setWidth(12);
$sheet->setCellValue("L1", "소속");
$sheet->getColumnDimension("L")->setWidth(12);
$sheet->setCellValue("M1", "View");
$sheet->getColumnDimension("M")->setWidth(20);
$cell = 2;
for ($i = 0; $i < count($row); $i++) {
    $no = $i + 1;


    switch ($row[$i]['ur_level']) {
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
    $row[$i]['ct_code_pd'] = explode(',', str_replace('X', '', $row[$i]['ct_code_pd'] ));
    $endItem = array_pop($row[$i]['ct_code_pd']);
    $row[$i]['ct_code_di'] = explode(',', str_replace('X', '', $row[$i]['ct_code_di'] ));
    $endItem = array_pop($row[$i]['ct_code_di']);
    $row[$i]['ct_code_ss'] = explode(',', str_replace('X', '', $row[$i]['ct_code_ss'] ));
    $endItem = array_pop($row[$i]['ct_code_ss']);
    $pdList = $row[$i]['ct_code_pd'];
    $diList = $row[$i]['ct_code_di'];
    $ssList = $row[$i]['ct_code_ss'];

    for($j=0; $j<count($pdList); $j++){
        $row[$i]['ct_code_pd_str'] .= $code[$pdList[$j]];
        if($j+1 != count($pdList)) $row[$i]['ct_code_pd_str'] .= ', ';
    }
    for($j=0; $j<count($diList); $j++){
        $row[$i]['ct_code_di_str'] .= $code[$diList[$j]];
        if($j+1 != count($diList)) $row[$i]['ct_code_di_str'] .= ', ';
    }
    for($j=0; $j<count($ssList); $j++){
        $row[$i]['ct_code_ss_str'] .= $code[$ssList[$j]];
        if($j+1 != count($ssList)) $row[$i]['ct_code_ss_str'] .= ', ';
    }

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A$cell", $no)
        ->setCellValue("B$cell", $row[$i]['ct_code_pd_str'])
        ->setCellValue("C$cell", $row[$i]['ct_code_di_str'])
        ->setCellValue("D$cell", $row[$i]['ct_code_ss_str'])
        ->setCellValue("E$cell", $row[$i]['ct_title'])
        ->setCellValue("F$cell", $row[$i]['ct_speaker'])
        ->setCellValue("G$cell", $row[$i]['play_time'])
        ->setCellValue("H$cell", $row[$i]['ct_type'])
        ->setCellValue("I$cell", $row[$i]['rating'])
        ->setCellValue("J$cell", $row[$i]['ct_hit'])
        ->setCellValue("K$cell", $row[$i]['ct_dt_update'])
        ->setCellValue("L$cell", $row[$i]['ct_open_type'])
        ->setCellValue("M$cell", $row[$i]['ct_open']);

    $cell++;
}

$now = date("ymd");

// Rename sheet
$sheet->setTitle("contentList");

// 문서 열어볼시 미리 선택되어지는 셀 설정
$sheet->setSelectedCellByColumnAndRow(0, 1);

// 엑셀 파일 오픈시 활성화될 시트
$spreadsheet->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition:attachment;filename=' . $now . '_loginReport.xlsx');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');

exit;
?>