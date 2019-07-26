<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	include_once(CLASS_PATH . "/user.class.lib");
	$userClass = new UserClass();

	if($_POST['actionType'] == "pointSetting"){
		
		foreach ($_POST as $key=>$value){ 
			
			if($key == 'actionType')continue;

			$sql = "update point_role set ";
			$sql .= "ptr_point={$value} ";
			$sql .= "where ptr_code='{$key}'";
			$DB->Execute($sql);
		}

		echo json_encode('1');
		
	}
	
?>