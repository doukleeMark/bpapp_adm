<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// Paging
	$sLimit = "";
	if (isset($_POST['start']) && $_POST['length'] != '-1') {
		$sLimit = "LIMIT " . $DB->RealEscapeString($_POST['start']) . ", " .
		$DB->RealEscapeString($_POST['length']);
	}

	// Ordering
	$orderColumns = array('l.idx', 'l.idx', 'ur_id', 'ur_name', 'unit_name', 'ur_team', 'ur_level', 'log_model', 'log_os', 'log_appver', 'log_date');

	if (isset($_POST['order'])) {
		$sOrder = "ORDER BY ";
		$sOrder .= $orderColumns[intval($_POST['order'][0]['column'])] . " " . $DB->RealEscapeString($_POST['order'][0]['dir']) . " ";
	}

	$sWhere = "";
	if ($_POST['search']['value'] != "") {
		$sWhere = "AND (";

		if (strpos($_POST['search']['value'], " ")) {
			$words = explode(" ", $_POST['search']['value']);

			for ($i = 0; $i < count($words); $i++) {
				$sWhere .= " (";
				for ($j = 0; $j < count($orderColumns); $j++) {
					$sWhere .= $orderColumns[$j] . " LIKE '%" . $DB->RealEscapeString($words[$i]) . "%' OR ";
				}
				$sWhere = substr_replace($sWhere, "", -3);
				$sWhere .= ") AND";
			}
		} else {

			for ($i = 0; $i < count($orderColumns); $i++) {
				$sWhere .= $orderColumns[$i] . " LIKE '%" . $DB->RealEscapeString($_POST['search']['value']) . "%' OR ";
			}
		}

		$sWhere = substr_replace($sWhere, "", -3);
		$sWhere .= ') ';
	}

	$add_sql = '';
	if ($_SESSION['USER_LEVEL'] != "10") {
		$add_sql = "AND u.ur_level!=10 AND u.ur_hidden=0 ";
	}

	$sql = "select l.idx, l.log_date, l.log_os, l.log_model, l.log_appver, u.ur_name, u.ur_id, ud.unit_name, u.ur_team, u.ur_level from system_log l, user_data u, unit_data ud where l.log_type = 'login' AND l.log_user = u.idx AND ud.idx = u.ur_unit ";
	$sql .= $add_sql . $sWhere . $sOrder . $sLimit;
	$res = $DB->Execute($sql);

	// 총 갯수
	$sql = "select count(l.idx) as cnt from system_log l, user_data u, unit_data ud where l.log_type = 'login' AND l.log_user = u.idx ";
	$sql .= $add_sql . $sWhere;
	$countRes = $DB->GetOne($sql);
	$cnt = $countRes['cnt'];

	$aColumns = array('idx', 'idx', 'ur_id', 'ur_name', 'unit_name', 'ur_team', 'ur_level', 'log_model', 'log_os', 'log_appver', 'log_date');

	$output = array(
		"draw" => $draw,
		"recordsTotal" => $cnt,
		"recordsFiltered" => $cnt,
		"data" => array(),
	);

	$count = 0;
	while ($aRow = mysqli_fetch_array($res)) {
		$count++;
		$row = array();
		for ($i = 0; $i < count($aColumns); $i++) {
			if ($aColumns[$i] == "no") {
				$row[] = $count;
			} else if ($aColumns[$i] != ' ') {
				$row[] = $aRow[$aColumns[$i]];
			}
		}
		$output['data'][] = $row;
	}

	echo json_encode($output);
?>