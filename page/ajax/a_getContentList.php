<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    // Paging
    $sLimit = "";
    if (isset($_POST['start']) && $_POST['length'] != '-1') {
        $sLimit = "LIMIT " . $DB->RealEscapeString($_POST['start']) . ", " .
        $DB->RealEscapeString($_POST['length']);
    }

    // Ordering
    $orderColumns = array('c.idx', 'c.idx', 'c.ct_title', 'c.ct_speaker', 'play_sec', 'c.ct_type', 'rating', 'c.ct_hit', 'c.ct_dt_update', 'c.ct_open');

    if (isset($_POST['order'])) {
        $sOrder = "ORDER BY ";
        $sOrder .= $orderColumns[intval($_POST['order'][0]['column'])] . " " . $DB->RealEscapeString($_POST['order'][0]['dir']) . " ";
    }

    $filterColumns = array('c.ct_title', 'c.ct_speaker');
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

    $add_sql = '';
    
    $sql = "SELECT 
            c.idx, c.ct_title, c.ct_speaker, c.ct_type, c.ct_hit, c.ct_dt_update, c.ct_open, 
            ifnull(s3.s3_play_sec, 0) as play_sec, 
            ifnull(cr.rating, '-') as rating 
        FROM contents c ";
    $sql .= "left join (select cr_ct_id, avg(cr_rating) as rating from contents_rating group by cr_ct_id) cr on c.idx = cr.cr_ct_id ";
    $sql .= "left join s3_data s3 on c.ct_s3_file = s3.idx ";
    $sql .= "where c.ct_delete = 0 ";
    $sql .= $add_sql . $sWhere . $sOrder . $sLimit;
    $res = $DB->Execute($sql);
    
    // 총 갯수
    $sql = "select count(c.idx) as cnt from contents c ";
    $sql .= "where c.ct_delete = 0 ";
    $sql .= $add_sql . $sWhere;
    $countRes = $DB->GetOne($sql);
    $cnt = $countRes['cnt'];

    $aColumns = array('idx', 'no', 'ct_title', 'ct_speaker', 'play_sec', 'ct_type', 'rating', 'ct_hit', 'ct_dt_update', 'ct_open');

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