<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	if((int)$_SESSION['USER_LEVEL'] >= 3){
		$sql = "select * from folder_data ORDER BY idx";
	}else{
		$sql = "select * from folder_data where fd_unit = {$_SESSION['USER_UNIT']} ORDER BY idx";
	}
	
	$res = $DB->Execute($sql);	
	
	$output = array();

	while ($aRow = mysqli_fetch_array($res))
	{	
		$row = array(
			"label"=>$aRow['fd_name'],
			"id"=>$aRow['idx'],
			"parentId"=>$aRow['fd_parent'],
			"depth"=>$aRow['fd_depth'],
			"order"=>$aRow['fd_display']
		);
		
		$output[] = $row;
	}

	echo json_encode($output);	
?>
