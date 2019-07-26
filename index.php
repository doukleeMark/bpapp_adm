<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	if ($_GET['page'] == "login" || !$_SESSION['USER_ID']) {
		require_once(PAGE_PATH."/login.php");
		return;
	}

	if ($_GET['page'] == "logout") {
		session_destroy(); 
		echo "<script>location.href='/?page=login';</script>";
		return;
	}

	if ($_GET['page'] == null){
		echo "<script>location.href='/?page=user_list';</script>";
		return;
	}
	
	if(is_file(PAGE_PATH."/".$_GET['page'].".php")){
		require_once(PAGE_PATH."/".$_GET['page'].".php");
	}else{
		require_once(PAGE_PATH."/404.php");
	}

?>