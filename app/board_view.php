<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	// GET : ur_idx, bod_idx

	if(isset($_GET['ur_idx']) && isset($_GET['bod_idx'])){

		$sql = "select * from board_data ";
		$sql .= "where idx={$_GET['bod_idx']} ";
		$boardRes = $DB->GetOne($sql);

		// hit 증가
		$hit = $boardRes['bod_hit'] + 1;
		$sql = "update board_data set bod_hit = {$hit} where idx={$_GET['bod_idx']}";
		$DB->Execute($sql);

		if($boardRes['bod_file'] > 0){
			$sql = "select * from upload_data ";
			$sql .= "where idx={$boardRes['bod_file']} ";
			$fileRes = $DB->GetOne($sql);
		}
	}
	function url_auto_link($str = '', $popup = false)
	{
		if (empty($str)) {
			return false;
		}
		$target = $popup ? 'target="_blank"' : '';
		$str = str_replace(
			array("&lt;", "&gt;", "&amp;", "&quot;", "&nbsp;", "&#039;"),
			array("\t_lt_\t", "\t_gt_\t", "&", "\"", "\t_nbsp_\t", "'"),
			$str
		);
		$str = preg_replace(
			"/([^(href=\"?'?)|(src=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,\(\)]+)/i",
			"\\1<a href=\"javascript:open_web('\\2');\" {$target}>\\2</A>",
			$str
		);
		return $str;
	}
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
</head>

<body style="margin:0;padding:10px 20px;word-break:break-all;line-height:25px; background-color:rgba(0,0,0,0);">
<?php
	if($boardRes['bod_file'] > 0 && (strtolower($fileRes['file_ext']) == 'png' || strtolower($fileRes['file_ext']) == 'jpg')){
?>
<img src="<?=$fileRes['tmp_name']?>" style="width:100%;max-width:100%;">
<?php
	}
?>
<?=str_replace("\n", "<br>", url_auto_link($boardRes['bod_content'], true))?>
<script src="/app/js/isMobile.js" type="text/javascript"></script>
<script type="text/javascript">
	function open_web(_url){
		if(isMobile.iOS()){
			document.location = "jscall://link|" + _url;
		}else if(isMobile.Android()){
			window.android.callAndroid("link|" + _url);
		}else {
			window.open(_url,'_blank');
		}
	}
</script>
</body>
</html>