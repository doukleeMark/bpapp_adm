<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	include_once(CLASS_PATH . "/user.class.lib");
	
	$userClass = new UserClass();
	$res = $userClass->getUserList($_POST);
	
	$aColumns = array( 'idx', 'no', 'ur_id', 'ur_name', 'ur_unit', 'ur_level', 'ur_point_bp', 'ur_dt_last');

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
			if ( $aColumns[$i] == "no" )
			{				
				$row[] = $count;
			}
			else if ( $aColumns[$i] == 'ur_unit' )
			{
				$unitName = "-";
				for ( $j = 0 ; $j < count($units) ; $j++ ) { 
					if($aRow[$aColumns[$i]] == $units[$j]['idx']){
						$unitName = $units[$j]['unit_name'];
						break;
					}
				}
				$row[] = $unitName;
			}
			else if ( $aColumns[$i] != ' ' )
			{
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['data'][] = $row;
	}
	
	echo json_encode($output);	
?>