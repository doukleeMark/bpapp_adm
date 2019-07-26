<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// IN : ur_idx, os, model, uuid, token

	/* UUID INSERT */
	$sql = "select * from device_data where dvi_user={$_REQUEST['ur_idx']}";
	$res = $DB->GetAll($sql);
	$state = "0";
	
	for($i=0;$i<count($res);$i++) {
		if($res[$i]['dvi_uuid']==null) { //기기등록 할 공간이 있으면
			if(!empty($_REQUEST['uuid'])) {
				if (empty($_REQUEST['token'])) $_REQUEST['token'] = null;
				$sql = "update device_data set dvi_uuid='{$_REQUEST['uuid']}', dvi_os='{$_REQUEST['os']}', dvi_model='{$_REQUEST['model']}', dvi_token='{$_REQUEST['token']}', dvi_dt_add=sysdate() ";
				$sql .= "where idx=" . $res[$i]['idx'];
				$DB->Execute($sql);

				$state = "1";
				$newUUID = $_REQUEST['uuid']; //리턴해줄 변수 newUUID에 serial 저장
				
				$tmp = 1; //기기등록 했을 경우
				break;
			}
		}
	}
	
	$result = (object)null;
	$result->result = $state;
		
	echo json_encode($result);
?>
