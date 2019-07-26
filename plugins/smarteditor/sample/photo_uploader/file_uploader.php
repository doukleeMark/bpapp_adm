<?
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// default redirection
	$url = $_REQUEST["callback"].'?callback_func='.$_REQUEST["callback_func"];
	$bSuccessUpload = is_uploaded_file($_FILES['Filedata']['tmp_name']);

	// SUCCESSFUL
	if(bSuccessUpload) {
		$tmp_name = $_FILES['Filedata']['tmp_name'];
		$name = $_FILES['Filedata']['name'];
		
		$filename_ext = strtolower(array_pop(explode('.',$name)));
		$allow_file = array("jpg", "png", "bmp", "gif");
		
		if(!in_array($filename_ext, $allow_file)) {
			$url .= '&errstr='.$name;
		} else {
			$uploadDir = '/upload/board/';
			if(!is_dir(ROOT_PATH.$uploadDir)){
				mkdir(ROOT_PATH.$uploadDir, 0777);
			}
			
			$newPath = $uploadDir.urlencode($_FILES['Filedata']['name']);
			
			@move_uploaded_file($tmp_name, $newPath);
			
			$url .= "&bNewLine=true";
			$url .= "&sFileName=".urlencode(urlencode($name));
			$url .= "&sFileURL=/upload/board/".urlencode(urlencode($name));
			//$url .= "&sFileName=".urlencode($name);
			//$url .= "&sFileURL=/upload/{$_SESSION['DB_NAME']}/".urlencode($name);
		}
	}
	// FAILED
	else {
		$url .= '&errstr=error';
	}
		
	header('Location: '. $url);
?>