<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// Paging
	$sLimit = "";
	if(isset( $_POST['start'] ) && $_POST['length'] != '-1'){
		$sLimit = "LIMIT ".$DB->RealEscapeString( $_POST['start'] ).", ".
		$DB->RealEscapeString( $_POST['length'] );
	}

	// Ordering
	$orderColumns = array( 'tr.idx', 'ur_name', 'tr_unit', 'tr_team', 'dt_title', 'tr_date');

	if(isset($_POST['order'])){
		$sOrder = "ORDER BY ";
		$sOrder .= $orderColumns[intval($_POST['order'][0]['column'])]." ".$DB->RealEscapeString($_POST['order'][0]['dir']) ." ";
	}

	$sWhere = "";
	if ( $_POST['search']['value'] != "" )
	{
		$sWhere = "AND (";

		if(strpos($_POST['search']['value'], " ")){
			$words = explode(" ", $_POST['search']['value']);
		
			for ( $i=0 ; $i<count($words) ; $i++ ){
				$sWhere .= " (";
				for ( $j=0 ; $j<count($orderColumns) ; $j++ )
				{
					$sWhere .= $orderColumns[$j]." LIKE '%".$DB->RealEscapeString( $words[$i] )."%' OR ";
				}
				$sWhere = substr_replace( $sWhere, "", -3 );
				$sWhere .= ") AND";
			}
		}else{

			for ( $i=0 ; $i<count($orderColumns) ; $i++ )
			{
				$sWhere .= $orderColumns[$i]." LIKE '%".$DB->RealEscapeString( $_POST['search']['value'] )."%' OR ";
			}
		}


		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ') ';
	}

	$add_sql = '';
	if($_SESSION['USER_LEVEL'] != "10")
		$add_sql = "AND u.ur_level!=10 AND u.ur_hidden=0 ";

	$sql = "select tr.idx, u.ur_name, tr.tr_unit, tr.tr_team, tr.tr_date, dt.dt_title from data_info dt, data_tracking tr, user_data u ";
	$sql .= "where tr.tr_user = u.idx ";
	$sql .= "AND dt.idx = tr.tr_data ";
	$sql .= $add_sql.$sWhere.$sOrder.$sLimit;
	$res = $DB->Execute($sql);

	// 총 갯수
	$sql = "select count(tr.idx) as cnt from data_info dt, data_tracking tr, user_data u ";
	$sql .= "where tr.tr_user = u.idx ";
	$sql .= "AND dt.idx = tr.tr_data ";
	$sql .= $add_sql.$sWhere;
	$countRes = $DB->GetOne($sql);
	$cnt = $countRes['cnt'];

	$sql = "select * from unit_data ";
	$unitRes = $DB->GetAll($sql);

	$aColumns = array('idx', 'ur_name', 'tr_unit', 'tr_team', 'dt_title', 'tr_date');
	
	$output = array(
		"draw" => $draw,
		"recordsTotal" => $cnt,
		"recordsFiltered" => $cnt,
		"data" => array()
	);

	$count = 0;
	while ( $aRow = mysqli_fetch_array( $res ) ){
		$count++;
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			if ($aColumns[$i] == "no"){				
				$row[] = $count;
			}else if ($aColumns[$i] == "tr_unit"){
				if((int)$aRow['tr_unit'] == 0)$row[] = "-";
				else {
					for($j=0;$j<count($unitRes);$j++) { 
						if($aRow['tr_unit'] == $unitRes[$j]['idx']){
							$row[] = $unitRes[$j]['unit_name'];
							break;
						}
					}
					
				}
			}else if ($aColumns[$i] != ' ' ){
				$row[] = $aRow[$aColumns[$i]];
			}
		}
		$output['data'][] = $row;
	}
	
	echo json_encode($output);	
?>