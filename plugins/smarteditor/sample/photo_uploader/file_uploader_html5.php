<?
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
	
 	$sFileInfo = '';
	$headers = array();
	 
	foreach($_SERVER as $k => $v) {
		if(substr($k, 0, 9) == "HTTP_FILE") {
			$k = substr(strtolower($k), 5);
			$headers[$k] = $v;
		} 
	}
	
	$file = new stdClass;
	$file->name = str_replace("\0", "", rawurldecode($headers['file_name']));
	$file->size = $headers['file_size'];
	$file->content = file_get_contents("php://input");
	
	$filename_ext = strtolower(array_pop(explode('.',$file->name)));
	$allow_file = array("jpg", "png", "bmp", "gif"); 
	
	if(!in_array($filename_ext, $allow_file)) {
		echo "NOTALLOW_".$file->name;
	} else {
		$uploadDir = '/upload/board/';
		if(!is_dir(ROOT_PATH.$uploadDir)){
			mkdir(ROOT_PATH.$uploadDir, 0777);
		}
		/*
		$uploadDir = '../../../upload/'.$_SESSION['DB_NAME'].'/';
		if(!is_dir($uploadDir)){			
			mkdir($uploadDir, 0777);
		}
		
		
		$newPath = $uploadDir.iconv("utf-8", "cp949", $file->name);
		*/
		/////////////////////////////////////////


		$rand = rand(0, 999);
		if(strlen($rand)==1) {
			$rand = "00".$rand;
		}else if(strlen($rand)==2) {
			$rand = "0".$rand;
		}
		$file->name = substr(date("YmdHi"), 2, 12).$rand.".".$filename_ext;
		$newPath = ROOT_PATH."/upload/board/".$file->name;

		////////////////////////////////////////
		
		if(file_put_contents($newPath, $file->content)) {
			$sFileInfo .= "&bNewLine=true";
			$sFileInfo .= "&sFileName=".$file->name;
			$sFileInfo .= "&sFileURL=".SHOST."/upload/board/".$file->name;
			//$sFileInfo .= "&sFileURL=../../../upload/{$_SESSION['DB_NAME']}/".$file->name;
		}
		
		echo $sFileInfo;
	}
?>