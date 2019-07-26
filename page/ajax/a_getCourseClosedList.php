<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    // Paging
    $sLimit = "";
    if (isset($_POST['start']) && $_POST['length'] != '-1') {
        $sLimit = "LIMIT " . $DB->RealEscapeString($_POST['start']) . ", " .
        $DB->RealEscapeString($_POST['length']);
    }

    // Ordering
    $orderColumns = array('co.idx', 'co.idx', 'co.co_title', 'co.co_dt_closed');

    if (isset($_POST['order'])) {
        $sOrder = "ORDER BY ";
        $sOrder .= $orderColumns[intval($_POST['order'][0]['column'])] . " " . $DB->RealEscapeString($_POST['order'][0]['dir']) . " ";
    }

    $filterColumns = array('co.co_title');
    $sWhere = "";
    if ($_POST['search']['value'] != "") {
        $sWhere = "AND (";

        if (strpos($_POST['search']['value'], " ")) {
            $words = explode(" ", $_POST['search']['value']);

            for ($i = 0; $i < count($words); $i++) {
                $sWhere .= " (";
                for ($j = 0; $j < count($filterColumns); $j++) {
                    $sWhere .= $filterColumns[$j] . " LIKE '%" . $DB->RealEscapeString($words[$i]) . "%' OR ";
                }
                $sWhere = substr_replace($sWhere, "", -3);
                $sWhere .= ") AND";
            }
        } else {

            for ($i = 0; $i < count($filterColumns); $i++) {
                $sWhere .= $filterColumns[$i] . " LIKE '%" . $DB->RealEscapeString($_POST['search']['value']) . "%' OR ";
            }
        }

        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ') ';
    }

    $sql = "SELECT
                co.idx, co.co_title, co.co_dt_closed 
            FROM course co 
    		WHERE co.co_closed = 1 ";
    $sql .= $sWhere . $sOrder . $sLimit;
    $res = $DB->Execute($sql);
    
    // 총 갯수
    $sql = "SELECT count(co.idx) as cnt 
			FROM course co 
			WHERE co.co_closed = 1 ";
    $sql .= $sWhere;
    $countRes = $DB->GetOne($sql);
    $cnt = $countRes['cnt'];

    $aColumns = array('idx', 'no', 'co_title', 'co_dt_closed');

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
				$row['no'] = $count;
            } else if ($aColumns[$i] != ' ') {
                $row[$aColumns[$i]] = $aRow[$aColumns[$i]];
            }
        }
        $output['data'][] = $row;
    }
    
    echo json_encode($output);
?>