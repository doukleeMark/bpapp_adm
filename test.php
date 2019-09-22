<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    /*
    include_once(CLASS_PATH . "/user.class.lib");

    $userClass = new UserClass();

    $sql = "select * from user_data where ur_state=1";
    $users = $DB->GetAll($sql);

    echo "d";

    for ($i = 0; $i<count($users); $i++) {
        $_idx = $users[$i]['idx'];

        $sql = "insert into device_data(dvi_user) values(";
        $sql .= $_idx . ")";

        echo $sql . "<br>";

        $DB->Execute($sql);
    }
    */

?>
