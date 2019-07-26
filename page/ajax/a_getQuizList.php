<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	include_once(CLASS_PATH . "/addon.class.lib");
	
	$addonClass = new AddonClass();
	$res = $addonClass->getQuizList($_POST);
	
	$aColumns = array( 'idx', 'no', 'qz_title', 'ur_name', 'qz_date');

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
			else if ( $aColumns[$i] != ' ' )
			{
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['data'][] = $row;
	}
	
	echo json_encode($output);	
?>