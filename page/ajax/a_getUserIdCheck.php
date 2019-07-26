<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	if(isset($_POST['userId'])){
		$sql = "select ur_id from user_data where ur_id = '{$_POST['userId']}'";
		$res = $DB->GetOne($sql);

		if(isset($res['ur_id']))
			$rchar = 1;
		else
			$rchar = 0;
	}
	echo $rchar;

?>