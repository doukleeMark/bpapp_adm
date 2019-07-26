<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	include_once(CLASS_PATH . "/board.class.lib");
	
	$boardClass = new BoardClass();
	$res = $boardClass->getBoardList($_POST);
	
	$aColumns = array( 'idx', 'no', 'bod_title', 'bod_units', 'ur_name', 'bod_hit', 'bod_date');
	$board_type = array( '', 'Notice');

	$sql = "select * from unit_data";
	$units = $DB->GetAll($sql);

	$output = array(		
		"data" => array()
	);

	$count = 0;
	while ( $aRow = mysqli_fetch_array( $res ) )
	{
		$count++;
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "no" ){				
				$row[] = $count;
			}else if($aColumns[$i] == 'bod_units' ){
				$unitName = "-";
				
				$select_units = array_filter(explode(",", str_replace("X", "", $aRow[$aColumns[$i]])));

				if(count($select_units) == 0)$unitName = "-";
				else{
					for ($j=0; $j < count($units) ; $j++) {
						if($select_units[0] == $units[$j]['idx']){
							$unitName = $units[$j]['unit_name'];
							break;
						}
					}

					if(count($select_units) != 1){
						$unitName .= " 외 " .  (count($select_units) - 1) . "개";
					}
				}

				$row[] = $unitName;
			}else if($aColumns[$i] == 'bod_type' ){
				$row[] = $board_type[$aRow[ $aColumns[$i] ]];
			}else if ( $aColumns[$i] != ' ' )
			{
				$row[] = $aRow[ $aColumns[$i] ];
				
			}
		}
		$output['data'][] = $row;
	}
	
	echo json_encode($output);	
?>