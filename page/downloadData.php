<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
	if(isset($_GET['idx'])){		
		$sql = "select * from upload_data where idx={$_GET['idx']}";
		$fileRow = $DB->GetOne($sql);
	}else{
		return;
	}

	$file = $ROOT_PATH.$fileRow['tmp_name'];
	$real = $fileRow['real_name'];
	
	download_file($file, $real);

	function download_file( $fullPath, $realName){

		if( headers_sent() )
			die('Headers Sent');

		if(ini_get('zlib.output_compression'))
			ini_set('zlib.output_compression', 'Off');

		if( file_exists($fullPath) ){

			$fsize = filesize($fullPath);
			$path_parts = pathinfo($fullPath);
			$ext = strtolower($path_parts['extension']);

			switch ($ext) {
				case "pdf": $ctype="application/pdf"; break;
				case "exe": $ctype="application/octet-stream"; break;
				case "zip": $ctype="application/zip"; break;
				case "doc": $ctype="application/msword"; break;
				case "xls": $ctype="application/vnd.ms-excel"; break;
				case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
				case "gif": $ctype="image/gif"; break;
				case "png": $ctype="image/png"; break;
				case "jpeg":
				case "jpg": $ctype="image/jpg"; break;
				default: $ctype="application/force-download";
			}

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=\"".$realName."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$fsize);
			ob_clean();
			flush();
			readfile( $fullPath );

		} else
			die('파일이 올바르지 않습니다. 삭제 후 재업로드가 필요합니다.');
	}
?>