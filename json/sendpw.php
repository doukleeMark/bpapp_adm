<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// IN : id

	define('CHARSET','UTF-8');
	define('SENDER', 'no-reply@boryung.co.kr');
	define('SUBJECT','BP App 계정 임시 비밀번호');
	
	require_once ROOT_PATH."/vendor/autoload.php";

	use Aws\Ses\SesClient;
	use Aws\Ses\Exception\SesException;

	$credentials = new Aws\Credentials\Credentials(AWS_ACCESS_KEY, AWS_SECRET);

	$client = SesClient::factory(array(
		'version'=> 'latest',     
		'region' => 'us-east-1',
		'credentials' => $credentials
	));

	$result = (object)null;
	$result->result = "0";
	
	if(isset($_REQUEST['id']) && strlen($_REQUEST['id']) != 0){
		$tmpPw = get_random_string(4,"azAZ").get_random_string(4,"09");
	
		$sql = "UPDATE user_data SET 
					ur_pw = PASSWORD('{$tmpPw}') 
				where ur_id = '{$_REQUEST['id']}' ";
		$DB->Execute($sql);
		
		$htmlbody = "<table cellpadding='0' cellspacing='0' style='border:1px solid #f8f=8f8; border-radius:10px; font-family: Arial, Helvetica, sans-serif; background:#F4F4F4; width:720px;'>
						<tr>
							<td>
								<div style='border:1px solid #f6f6f6;border-radius:8px;'>
									<div style='border:1px solid #ececec;border-radius:5px;'>
										<div style='border:1px solid #e0e0e0;'>
											<div style='border:1px solid #bdbdbd;'>
												<div style='margin-left: 18px; margin-right: 18px; font-family:Arial; font-size: 12px;'>
													<div style='line-height: 18px'>&nbsp;</div>
													<div style='color: #000000; font-size: 14px; font-weight:bold; font-family: Arial;'>Account Password</div>
												<div style='line-height: 18px'>&nbsp;</div>
												<div style='font-size: 1px; line-height: 1px; height: 1px; background: none repeat scroll 0% 0% #999999;'>&nbsp;</div>
												<div s=tyle='line-height: 18px'>&nbsp;</div>안녕하세요. BR KNOWLEDGE 입니다.
												<br>
												<br><b>{$_REQUEST['id']}</b> 계정 임시비밀번호는 <b>{$tmpPw}</b> 입니다.
												<br>
												<br>
												<br>감사합니다.
												<br>
												<br>
												<div style='line-height: 18px'>&nbsp;</div>
											</div>
										</div>
									</div>
								</div>
								</div>
							</td>
						</tr>
					</table>
					<table style='padding: 9px 0px; width:720px; font-family: Arial; font-size: 11px; color: #9b9b9b; text-align: right;'>
						<tr>
							<td>BR KNOWLEDGE App</td>
						</tr>
					</table>";

		$textbody = "안녕하세요. BR KNOWLEDGE 입니다.\n\n {$_REQUEST['id']} 계정 임시비밀번호는 {$tmpPw} 입니다.\n\n 감사합니다.";
	
		$sql = "select idx, ur_id from user_data where ur_id='{$_REQUEST['id']}' ";
		$userRes = $DB->GetOne($sql);

		if(isset($userRes['idx'])) {
			
			$email = $userRes['ur_id'];
	
			$check_email = preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email);
	
			if($check_email == false){
				echo json_encode($result);
				exit();
			}
	
			try {
				$res = $client->sendEmail([
					'Destination' => [
						'ToAddresses' => [
							$email
						],
					],
					'Message' => [
						'Body' => [
							'Html' => [
								'Charset' => CHARSET,
								'Data' => $htmlbody,
							],
							'Text' => [
								'Charset' => CHARSET,
								'Data' => $textbody,
							],
						],
						'Subject' => [
							'Charset' => CHARSET,
							'Data' => SUBJECT,
						],
					],
					'Source' => SENDER,
				]);
				$messageId = $res->get('MessageId');
				$result->result = "1";
			} catch (SesException $error) {}
		}
	}

	echo json_encode($result);

	function get_random_string($len = 10, $type = '') {
		$lowercase = 'abcdefghijklmnopqrstuvwxyz';
		$uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$numeric = '0123456789';
		$special = '`~!@#$%^&*()-_=+\\|[{]};:\'",<.>/?';
		$key = '';
		$token = '';

		if ($type == '') {
			$key = $lowercase.$uppercase.$numeric;
		} else {
			if (strpos($type,'09') > -1) $key .= $numeric;
			if (strpos($type,'az') > -1) $key .= $lowercase;
			if (strpos($type,'AZ') > -1) $key .= $uppercase;
			if (strpos($type,'$') > -1) $key .= $special;
		}
		for ($i = 0; $i < $len; $i++) {
			$token .= $key[mt_rand(0, strlen($key) - 1)];
		}
		return $token;
	}

?>