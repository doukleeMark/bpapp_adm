<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    // Paging
    $sLimit = "";
    if (isset($_POST['start']) && $_POST['length'] != '-1') {
        $sLimit = "LIMIT " . $DB->RealEscapeString($_POST['start']) . ", " .
        $DB->RealEscapeString($_POST['length']);
    }

    // Ordering
    $orderColumns = array('ur.idx', 'u.unit_name', 'ur.ur_team', 'ur.ur_name', 'ur.ur_position', 'ur.ur_id');

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
        $add_sql = "AND ur.ur_level!=10 AND ur.ur_hidden=0 AND ur.ur_state = 1 ";
    }

    $sql = "select ur.idx, ifnull(u.unit_name, '-') as unit, ur.ur_team, ur.ur_name, ur.ur_id, ur.ur_position, ur.ur_tag from user_data ur ";
    $sql .= "left join unit_data u on ur.ur_unit = u.idx, course_user cu ";
    $sql .= "where ur.idx = cu.cu_ur_id and cu.cu_co_id = " . $_POST['course_idx'] . " ";
    $sql .= $add_sql . $sWhere . $sOrder . $sLimit;
    $res = $DB->Execute($sql);

    // 총 갯수
    $sql = "select count(ur.idx) as cnt from user_data ur ";
    $sql .= "left join unit_data u on ur.ur_unit = u.idx, course_user cu ";
    $sql .= "where ur.idx = cu.cu_ur_id and cu.cu_co_id = " . $_POST['course_idx'] . " ";
    $sql .= $add_sql . $sWhere;
    $countRes = $DB->GetOne($sql);
    $cnt = $countRes['cnt'];

    $aColumns = array('idx', 'unit', 'ur_team', 'ur_name', 'ur_id', 'ur_position');

    $output = array(
        "draw" => $draw,
        "recordsTotal" => $cnt,
        "recordsFiltered" => $cnt,
        "data" => array(),
    );

    $pos = array(
        "RSM",
        "MR",
        "CP",
        "PM",
        "영업팀장",
        "MKT팀장",
        "그룹장",
        "본부장",
        "부문장"
    );

    $count = 0;
    while ($aRow = mysqli_fetch_array($res)) {
        $count++;
        $row = array();

        for ($i = 0; $i < count($aColumns); $i++) {

            if ($aColumns[$i] == "no") {
                $row['no'] = $count;
            } else if ($aColumns[$i] == 'ur_position') {
                $row[$aColumns[$i]] = $pos[$aRow['ur_position']];

            } else if ($aColumns[$i] != ' ') {
                $row[$aColumns[$i]] = $aRow[$aColumns[$i]];
            }
        }
        $output['data'][] = $row;
    }
    
    echo json_encode($output);
?>