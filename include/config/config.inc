<?php
	session_start();

    	error_reporting(0);
	ini_set("display_errors", 1);

	$sroot			= $_SERVER['DOCUMENT_ROOT'];
	$shost			= "http://".$_SERVER['HTTP_HOST'];

	$ROOT_PATH = substr($sroot, 0, -1);

	$lib_path		= $ROOT_PATH ."/include/lib";
	$common_path	= $ROOT_PATH ."/include/common";
	$class_path		= $ROOT_PATH ."/include/class";
	$page_path		= $ROOT_PATH ."/page";

	// Global Var
	define('SHOST',$shost);
	define('LIB_PATH',$lib_path);
	define('ROOT_PATH',$ROOT_PATH);
	define('COMMON_PATH',$common_path);
	define('CLASS_PATH', $class_path);
	define('PAGE_PATH', $page_path);

	// AWS
	define('AWS_ACCESS_KEY', 'AKIAZQS2DUU5UTMDBLC4');
	define('AWS_SECRET', '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');
	define('AWS_BUCKET', 'boryung-brain-upload');
	define('AWS_REGION', 'ap-northeast-2');

	// Common Include Files
	require_once(LIB_PATH."/db.class.lib");
	require_once(LIB_PATH."/common.lib");

	// PUSH SERVER
	define('PUSH_SERVER', 'http://133.186.212.15:7788/fcm/sendPush');


	$DB = new DBclass();
?>
