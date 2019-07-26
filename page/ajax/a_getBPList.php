<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	$sql = "select bu.*, l.bl_cnt from ";
	$sql .= "(select b.*, u.ur_name, u.ur_team from bp_data b, user_data u where b.bp_hidden = 0 AND b.bp_state != 0 AND b.bp_user = u.idx ) bu left outer join ";
	$sql .= "(select count(bl_bp) as bl_cnt, bl_bp from bp_like group by bl_bp) l on l.bl_bp = bu.idx ";
	$sql .= "order by bu.idx desc";
	$res = $DB->Execute($sql);
	
	$aColumns = array( 'idx', 'no', 'bp_title', 'ur_name', 'bp_brand', 'bp_unit', 'ur_team', 'choice', 'bl_cnt', 'bp_hit', 'share', 'bp_state', 'bp_date', 'bp_new_fu');

	$sql = "select * from unit_data";
	$units = $DB->GetAll($sql);

	$sql = "select * from brand_data";
	$brands = $DB->GetAll($sql);
	
	$output = array(		
		"data" => array()
	);

	while ( $aRow = mysqli_fetch_array( $res ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "no" ){				
				$row[] = $aRow['idx']; 
			}else if($aColumns[$i] == 'choice' ){

				$choice = "";
				if((int)$aRow['bp_choice_ceo'] == 1)$choice .= "C,"; 
				if((int)$aRow['bp_choice_bon'] == 1)$choice .= "G,"; 
				if((int)$aRow['bp_choice_unit'] == 1)$choice .= "U,"; 
				if((int)$aRow['bp_choice_mkt'] == 1)$choice .= "M,"; 
				if((int)$aRow['bp_choice_mrt'] == 1)$choice .= "T,"; 
				if((int)$aRow['bp_choice_pm'] == 1)$choice .= "P,"; 
				if((int)$aRow['bp_choice_month'] == 1)$choice .= "MB,";

				if(strlen($choice) > 0)$choice = substr($choice, 0, -1);

				$row[] = $choice;
			}else if($aColumns[$i] == 'bp_brand' ){
				$brandName = "-";
				for ( $j = 0 ; $j < count($brands) ; $j++ ) { 
					if($aRow[$aColumns[$i]] == $brands[$j]['idx']){
						$brandName = $brands[$j]['brand_name'];
						break;
					}
				}
				$row[] = $brandName;
			}else if($aColumns[$i] == 'bp_unit' ){
				$unitName = "-";
				for ( $j = 0 ; $j < count($units) ; $j++ ) { 
					if($aRow[$aColumns[$i]] == $units[$j]['idx']){
						$unitName = $units[$j]['unit_name'];
						break;
					}
				}
				$row[] = $unitName;
			}else if($aColumns[$i] == 'bl_cnt' ){
				if($aRow[$aColumns[$i]] == null)$row[] = 0;
				else $row[] = $aRow[ $aColumns[$i] ];
			}else if($aColumns[$i] == 'share' ){
				if((int)$aRow['bp_state'] == 5)$row[] = "전체공개";
				else $row[] = "팀내공개";
			}else if($aColumns[$i] == 'bp_state' ){
				$str = "";
				switch ($aRow['bp_state']) {
					case '1':
						$str = "팀내공개";
						break;
					case '2':
						$str = "승인대기";
						break;
					case '3':
						$str = "CP승인반려";
						break;
					case '4':
						$str = "CP승인대기";
						break;
					case '5':
						$str = "전체공개";
						break;
				}
				$row[] = $str;
			}else if ( $aColumns[$i] != ' ' )
			{
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['data'][] = $row;
	}
	
	echo json_encode($output);	
?>