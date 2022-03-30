<?php
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");	
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>App</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta content="" name="description" />
<meta content="" name="author" />
	
<link href="/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/common.css?v=1" rel="stylesheet" type="text/css"/>
<link href="/app/css/calendar.css" rel="stylesheet" type="text/css"/>

</head>

<body>
<input type="hidden" id="actionType" name="actionType" value="insert">
<input type="hidden" id="idx" name="idx" value="">
<input type="hidden" id="unit" name="unit" value="<?=$_GET['unit']?>">
<input type="hidden" id="selectDate" name="selectDate" value="">

<div id="datepicker"></div>

<script src="/assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/breakpoints.js" type="text/javascript"></script> 
<script src="/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="/app/js/jquery-ui.min.js"></script>
<script src="/app/js/calendar.js" type="text/javascript"></script>
</body>
</html>