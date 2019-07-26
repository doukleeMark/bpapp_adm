<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	$sql = "select co.idx, u.ur_name, u.ur_unit, u.ur_team, CONCAT(co.col_year, '년', co.col_month, '월') as month, co.col_value1, co.col_value2, co.col_value3, co.col_value4, co.col_value5, co.col_dt_last from collect_data co, user_data u where co.col_user = u.idx ";

	if($_SESSION['USER_LEVEL'] != 10){
		$sql .= "AND u.ur_level < 10 AND u.ur_hidden = 0 ";
	}
	
	$res = $DB->Execute($sql);

	$aColumns = array('no', 'ur_name', 'ur_team', 'month', 'col_value1', 'col_value2', 'col_value3', 'col_value4', 'col_value5', 'col_dt_last', 'idx');
	
	$output = array(		
		"data" => array()
	);

	$count = 0;
	while ( $aRow = mysqli_fetch_array( $res ) ){
		$count++;
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			if ($aColumns[$i] == "no"){				
				$row[] = $count;
			}else if ($aColumns[$i] == "col_unit"){
				if((int)$aRow['col_unit'] == 0)$row[] = $aRow['ur_unit'];
				else $row[] = $aRow['col_unit'];
			}else if ($aColumns[$i] != ' ' ){
				$row[] = $aRow[$aColumns[$i]];
			}
		}
		$output['data'][] = $row;
	}
	
	echo json_encode($output);	
?>