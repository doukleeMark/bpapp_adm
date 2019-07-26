<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");
    
    require_once ROOT_PATH."/vendor/autoload.php";

    use Aws\S3\S3Client;
    use Aws\S3\Exception\S3Exception;

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

        $s3folder = "contents/test/";

        $location = ROOT_PATH . $path . $name;

        if(!move_uploaded_file($file['tmp_name'], $location)){
            exit();
        }

        $credentials = new Aws\Credentials\Credentials(AWS_ACCESS_KEY, AWS_SECRET);

        $s3 = new S3Client([
            'version'     => 'latest',
            'region'  => AWS_REGION,
            'credentials' => $credentials
        ]);

        try {
            // Upload data.
            $result = $s3->putObject([
                'Bucket' => AWS_BUCKET,
                'Key'    => $s3folder.$name,
                'Body'   => fopen($location, 'r'),
                'ACL'    => 'public-read'
            ]);
        } catch (S3Exception $e) {}
        echo json_encode(1);
    }
	
?>