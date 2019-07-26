<?php
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>BP App Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta content="" name="description" />
<meta content="" name="author" />
<link href="/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
<link href="/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="/assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
<link href="/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="/assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="/assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<link href="/css/common.css" rel="stylesheet" type="text/css"/>
<link href="/css/login.css" rel="stylesheet" type="text/css"/>
</head>
<body class="error-body no-top">
<div class="container">	
	<div class="content">
		<div class="wrapper"> 
			<img src="/images/logo/logo.png" alt="LOGO" width="320">
		</div>
		<form id="form_login" action="/page/login_proc.php" method="post">			
			<input type="text" placeholder="User ID" name="ur_id" id="ur_id" onkeydown="javascript:if(event.keyCode==13){form_submit()};">
			<input type="password" placeholder="Password" name="ur_pw" id="ur_pw" onkeydown="javascript:if(event.keyCode==13){form_submit()};">
			<div class="remember">
				<input type="checkbox" id="autoSave">
				<label for="autoSave">Remember me?</label>
			</div>
			<span class="btn btn-success" id="btnLogin">LOGIN</span>
		</form>
	</div>
</div>

<script src="/assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<script src="/assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="/js/plugins/jquery.cookie.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/login.js" type="text/javascript"></script>
</body>
</html>