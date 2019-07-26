<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	include_once(CLASS_PATH . "/board.class.lib");
	
	$boardClass = new BoardClass();
	$res = $boardClass->getBBSList('4,5');
	
	$aColumns = array( 'idx', 'no', 'bbs_title', 'bbs_unit', 'ur_name', 'bbs_hit', 'bbs_dt_create');
	
	$sql = "select * from unit_data";
	$units = $DB->GetAll($sql);
	
	$mode_txt = array("","칭찬","자유","DC 현황", "요청", "질문");

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
			}else if($aColumns[$i] == 'bbs_title' ){
				$row[] = '['.$mode_txt[$aRow['bbs_mode']].'] '.$aRow['bbs_title'];
			}else if($aColumns[$i] == 'bbs_unit' ){
				$unitName = "-";
				for ( $j = 0 ; $j < count($units) ; $j++ ) { 
					if($aRow[$aColumns[$i]] == $units[$j]['idx']){
						$unitName = $units[$j]['unit_name'];
						break;
					}
				}
				$row[] = $unitName;
			}else if ( $aColumns[$i] != ' ' )
			{
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['data'][] = $row;
	}
	
	echo json_encode($output);	
?>