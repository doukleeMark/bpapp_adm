<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
    
    // FILES : file

    include_once(CLASS_PATH . "/s3.class.lib");

    if(isset($_FILES['file'])){

        $file = $_FILES['file'];
        $file_ext = strtolower(substr(strrchr($file['name'], "."), 1));
        $file_name = substr($file['name'], 0, strrpos($file['name'], "."));
        $file_size = $file['size'];

        $rand = rand(100, 999);

        if(!is_dir(ROOT_PATH."/upload/s3_tmp")){
        	@mkdir(ROOT_PATH."/upload/s3_tmp", 0777);
        }

        //tmp filename
        $path = "/upload/s3_tmp/";
        $name = substr(date("YmdHis"), 2, 12) . $rand . "." . $file_ext;

        $location = ROOT_PATH . $path . $name;

        if(!move_uploaded_file($file['tmp_name'], $location)){
            exit();
        }

        // 미디어 파일 길이 추출
        $play_sec = 0;

        if ($file_ext == 'mp4') {
            
            $ffprobe = FFMpeg\FFProbe::create();

            $play_sec = $ffprobe 
                ->streams($location) 
                ->videos()     
                ->first()     
                ->get('duration');

        }else if ($file_ext == 'mp3') {
            
            $ffprobe = FFMpeg\FFProbe::create();

            $play_sec = $ffprobe 
                ->format($location) 
                ->get('duration');
        }

        $obj = array(
            's3_url'=>'',
            's3_real_name' => $file['name'],
            's3_tmp_name' => $path . $name,
            's3_file_size' => (int)$file_size,
            's3_file_ext' => $file_ext,
            's3_play_sec' => (int)$play_sec
        );

        $s3Class = new S3Class();
		$s3_idx = $s3Class->s3Insert($obj);

        $result = array(
            's3Idx' => $s3_idx,
            'tmpUrl' => SHOST . $path . $name,
            'playSec' => (int)$play_sec
        );

        echo json_encode($result);
    }
?>