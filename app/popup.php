<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	$sql = "select * from mission_data where idx=1";
	$resMission = $DB->GetOne($sql);
	if($resMission['target'])
		$percent = (string)round(($resMission['arrival']/$resMission['target'])*100);
	else 
		$percent = 0;

	$mobileChromeVer = substr($_SERVER['HTTP_USER_AGENT'], strpos($_SERVER['HTTP_USER_AGENT'], "Chrome")+7,2);
	$cssFileName = '';
	if($mobileChromeVer <= 30)$cssFileName = '_oldversion';

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta id="viewport" name="viewport" content="minimum-scale=1, maximum-scale=1, user-scalable=yes, initial-scale=1, width=device-width">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta name="format-detection" content="telephone=no" />
<link href="/app/css/popup<?=$cssFileName?>.css" rel="stylesheet" type="text/css"/>
</head>

<body>
	<div class="content">
		<div class="topTitle">
			<img src="/app/img/logo.png" alt="logo">
			<span><?=$resMission['title']?></span>
		</div>
		<span class="target"><?=$resMission['target']?>억</span>
		<div class="graph">
			<div id="circle"></div>
			<div class="bg"></div>
		</div>
		<span class="subText">현재 달성액은 <span><?=$resMission['arrival']?>억</span> 입니다.</span>
	</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="js/jquery.circliful.js"></script>
<script>
    $( document ).ready(function() {
        $("#circle").circliful({
            animation: 1,
            animationStep: 8,
            foregroundColor: "#7BBF4B",
			backgroundColor: 'none',
            foregroundBorderWidth: 8,
            backgroundBorderWidth: 8,
            fontColor: '#fff',
            percent: <?=$percent?>,
            textSize: 28,
            textStyle: 'font-size: 12px;',
            textColor: '#fff',
            percentages: [10, 20, 30]
        });
    });

</script>
</body>
</html>