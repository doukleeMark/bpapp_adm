<?php
	ini_set('memory_limit','-1');
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	$sql = "select * from unit_data";
	$units = $DB->GetAll($sql);

	// Paging
	$sLimit = "";
	if(isset( $_POST['start'] ) && $_POST['length'] != '-1'){
		$sLimit = "LIMIT ".$DB->RealEscapeString( $_POST['start'] ).", ".
		$DB->RealEscapeString( $_POST['length'] );
	}

	// Ordering
	$orderColumns = array( 'p.idx', 'p.idx', 'ur_id', 'ur_name', 'ur_unit', 'ur_level', 'ptd_event', 'ptd_point', 'ptd_total', 'ptd_date');

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

	$sql = "select p.*, u.ur_id, u.ur_name, u.ur_unit, u.ur_level ";
	$sql .= "from user_data u, point_data p ";
	$sql .= "where u.idx = p.ptd_user AND u.ur_level!=10 AND u.ur_hidden=0 ".$sWhere.$sOrder.$sLimit;
	$res = $DB->Execute($sql);

	// 총 갯수
	$sql = "select count(p.idx) as cnt ";
	$sql .= "from user_data u, point_data p ";
	$sql .= "where u.idx = p.ptd_user AND u.ur_level!=10 AND u.ur_hidden=0 ".$sWhere;
	$countRes = $DB->GetOne($sql);
	$cnt = $countRes['cnt'];

	$aColumns = array( 'idx', 'no', 'ur_id', 'ur_name', 'ur_unit', 'ur_level', 'ptd_event', 'ptd_point', 'ptd_total', 'ptd_date');
	$output = array(
		"draw" => $draw,
		"recordsTotal" => $cnt,
		"recordsFiltered" => $cnt,
		"data" => array()
	);

	$count = 0;
	while ( $aRow = mysqli_fetch_array( $res ) )
	{

		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "no" )
			{				
				$row[] = $aRow['idx'];
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
		$count++;
	}
	
	echo json_encode($output);	
?>