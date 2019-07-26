<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// POST : actionType, code_group

	include_once(CLASS_PATH . "/code.class.lib");
	include_once(CLASS_PATH . "/el.class.lib");
	$codeClass = new CodeClass();
	
	$output = array();
	if($_POST['actionType'] == "insert"){

		$c = $codeClass->getByNameAndGroup($_POST);

		if(isset($c['idx'])){
			// 중복
			$output['result'] = 'duplicate';
		}else{
			$codeClass->codeInsert($_POST);
			$output['result'] = 'success';
		}
		
		echo json_encode($output);
		return;

	}else if($_POST['actionType'] == "update"){

		$c = $codeClass->getByNameAndGroup($_POST);

		if(isset($c['idx'])){
			// 중복
			$output['result'] = 'duplicate';
		}else{
			$codeClass->codeUpdate($_POST);
			$output['result'] = 'success';
		}

		echo json_encode($output);
		return;

	}else if($_POST['actionType']=="delete") {
		
		$elClass = new ELClass();

		if($elClass->usedContentByCodeIdx($_POST['idx'])){
			// 사용중
			$output['result'] = 'used';
		}else{
			$codeClass->codeDelete($_POST['idx']);
			$output['result'] = 'success';
		}

		echo json_encode($output);
		return;
	}else if($_POST['actionType']=="get") {
		
		$aColumns = array( 'idx', 'code_name', 'code_order');
	
		$sql = "SELECT * FROM code_type WHERE code_group = '{$_POST['code_group']}' ";
		$res = $DB->Execute($sql);
		
		$output = array(		
			"data" => array()
		);

		$count = 0;
		while ( $aRow = mysqli_fetch_array( $res ) )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] != ' ' )
				{
					$row[$aColumns[$i]] = $aRow[$aColumns[$i]];
				}
			}
			$output['data'][] = $row;
		}
		
		echo json_encode($output);	


	}else if($_POST['actionType']=="getList") {
		
		$sql = "SELECT * FROM code_type WHERE code_group = '{$_POST['code_group']}' ORDER BY code_name ASC ";
		$res = $DB->getAll($sql);
		
		echo json_encode($res);	
	}
?>