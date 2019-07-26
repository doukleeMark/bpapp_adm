<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	

    require_once "/home/bpapp/public_html/plugins/Classes/PHPExcel.php";
    
    include_once(CLASS_PATH . "/quiz.class.lib");
    $quizClass = new quizClass();

    $excelArray = excelToArray($_FILES['testListFile']['tmp_name'], false);

    $output = array();
    if(!(isset($_POST['contentIdx']) && $_POST['contentIdx'] > 0)){
        $output['result'] = 'error';
        echo json_encode($output);
		return;
    }

    $success_count = 0;
	for($i=2;$i<=count($excelArray);$i++){
        
        if(strlen(trim($excelArray[$i]['A'])) == 0)continue;
        if(strlen(trim($excelArray[$i]['B'])) == 0)continue;
        if(strlen(trim($excelArray[$i]['C'])) == 0)continue;
        if(strlen(trim($excelArray[$i]['D'])) == 0)continue;
        if(strlen(trim($excelArray[$i]['E'])) == 0)continue;
        if(!((int)$excelArray[$i]['F'] > 0 && (int)$excelArray[$i]['F'] < 5))continue;

        $obj = array(
            'cq_ct_id' => $_POST['contentIdx'],
            'cq_writer' => $_SESSION['USER_NO'],
            'cq_question' => $excelArray[$i]['A'],
            'cq_item_1' => $excelArray[$i]['B'],
            'cq_item_2' => $excelArray[$i]['C'],
            'cq_item_3' => $excelArray[$i]['D'],
            'cq_item_4' => $excelArray[$i]['E'],
            'cq_answer' => $excelArray[$i]['F']
        );
        $quizClass->testInsert($obj);
        $success_count++;
    }
    $output['result'] = 'success';
    $output['total'] = count($excelArray)-1;
    $output['count'] = $success_count;
    echo json_encode($output);
    return;
	
	function excelToArray($filePath, $header=true){
		$inputFileName = $filePath;
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$objWorksheet = $objPHPExcel->getActiveSheet();
		if($header){
			$highestRow = $objWorksheet->getHighestRow();
			$highestColumn = $objWorksheet->getHighestColumn();
			$headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
			$headingsArray = $headingsArray[1];
			$r = -1;
			$namedDataArray = array();
			for ($row = 2; $row <= $highestRow; ++$row) {
				$dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
				if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
					++$r;
					foreach($headingsArray as $columnKey => $columnHeading) {
						$namedDataArray[$r][$columnHeading] = $dataRow[$row][$columnKey];
					}
				}
			}
		}
		else{
			$namedDataArray = $objWorksheet->toArray(null,true,true,true);
		}
		return $namedDataArray;
	}
?>