<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    // Paging
    $sLimit = "";
    if (isset($_POST['start']) && $_POST['length'] != '-1') {
        $sLimit = "LIMIT " . $DB->RealEscapeString($_POST['start']) . ", " .
        $DB->RealEscapeString($_POST['length']);
    }

    // Ordering
    $orderColumns = array('cq.idx', 'cq.cq_question', 'cq.cq_tag');

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
    
    $sql = "select cq.idx, cq.cq_question, cq.cq_tag from contents_quiz cq ";
    $sql .= "where cq.cq_ct_id = " . $_POST['content_idx'] . " ";
    $sql .= $add_sql . $sWhere . $sOrder . $sLimit;
    $res = $DB->Execute($sql);
    
    // 총 갯수
    $sql = "select count(cq.idx) as cnt from contents_quiz cq ";
    $sql .= "where cq.cq_ct_id = " . $_POST['content_idx'] . " ";
    $sql .= $add_sql . $sWhere;
    $countRes = $DB->GetOne($sql);
    $cnt = $countRes['cnt'];

    $aColumns = array('idx', 'cq_question', 'cq_tag');

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
            } else if ($aColumns[$i] == "cq_question") {
                
                $row[$aColumns[$i]] = splitCharacter($aRow[$aColumns[$i]], 100);

            } else if ($aColumns[$i] != ' ') {
                $row[$aColumns[$i]] = $aRow[$aColumns[$i]];
            }
        }
        $output['data'][] = $row;
    }
    
    echo json_encode($output);
?>