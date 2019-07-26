<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	include_once(CLASS_PATH . "/data.class.lib");
	
	$dataClass = new DataClass();
	$res = $dataClass->getDataList($_POST);
	
	$aColumns = array( 'idx', 'no', 'fd_name', 'dt_title', 'file_size', 'ur_name', 'dt_date');
	
	$sql = "select idx, fd_name from folder_data";
	$folders = $DB->GetAll($sql);

	$output = array(		
		"data" => array()
	);

	$count = 0;
	while($aRow=mysqli_fetch_array($res)){
		$count++;
		$row = array();
		for($i=0;$i<count($aColumns);$i++){
			if($aColumns[$i] == "no" ){				
				$row[] = $count;
			}else if($aColumns[$i] == 'fd_name' ){
				$folderName = "-";
				
				$select_folders = array_filter(explode(",", str_replace("X", "", $aRow['dt_folders'])));

				if(count($select_folders) == 0)$folderName = "-";
				else{
					for ($j=0; $j < count($folders) ; $j++) {
						if($select_folders[0] == $folders[$j]['idx']){
							$folderName = $folders[$j]['fd_name'];
							break;
						}
					}

					if(count($select_folders) != 1){
						$folderName .= " 외 " .  (count($select_folders) - 1) . "개";
					}
				}

				$row[] = $folderName;
			}else if($aColumns[$i] == 'file_size' ){
				if(empty($aRow[$aColumns[$i]])){
					$row[] = "-";
				}else{
					if($aRow[$aColumns[$i]] < 1024){
						$row[] =  number_format($aRow[$aColumns[$i]] * 1.024).'byte';
					}else if(($aRow[$aColumns[$i]] > 1024) && ($aRow[$aColumns[$i]] < 1024000)){
						$row[] =  number_format($aRow[$aColumns[$i]] * 0.001024).'KB';
					}else if($aRow[$aColumns[$i]] > 1024000){
						$row[] =  number_format($aRow[$aColumns[$i]] * 0.000001024,2).'MB';
					}
				}
			}else if($aColumns[$i] != ' ' ){
				$row[] = $aRow[$aColumns[$i]];
			}
		}
		$output['data'][] = $row;
	}
	
	echo json_encode($output);	
?>