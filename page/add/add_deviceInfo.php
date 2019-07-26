<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

	// POST : actionType, deviceIdx, userIdx, dIdx

	if($_POST['actionType'] == "deviceInfo"){

		if(!isset($_POST['deviceIdx']))return;

		$sql = "select * from device_data where idx in({$_POST['deviceIdx']})";
		$dInfo = $DB->GetAll($sql);

		for($i=0;$i<count($dInfo);$i++){
			if($dInfo[$i]['dvi_uuid'] != NULL){
?>
				<div class="col-xs-12 addDeviceInfo">
					<input type="hidden" class="dIdx" value="<?=$dInfo[$i]['idx']?>">
					<div class="grid solid">
						<div class="grid-body">
							<div class="col-sm-4">
								<span>Model : <span class="semi-bold"><?=$dInfo[$i]['dvi_model']?></span></span>
							</div>
							<div class="col-sm-4">
								<?php
									if($dInfo[$i]['dvi_appver'] == '' || $dInfo[$i]['dvi_appver'] == null)$appver = '';
									else $appver = 'v'.$dInfo[$i]['dvi_appver'];
								?>
								<span>App : <span class="semi-bold"><?=$appver?></span></span>
							</div>
							<div class="col-sm-4">
								<span>OS : <span class="semi-bold"><?=$dInfo[$i]['dvi_os']?></span></span>
							</div>
							<div class="col-sm-6 ">
								<span>UUID : <span class="semi-bold"><?=$dInfo[$i]['dvi_uuid']?></span></span>
							</div>
							<div class="col-sm-6 ">
								<?php
									if(strlen($dInfo[$i]['dvi_token']) > 0)
										$token = substr($dInfo[$i]['dvi_token'], 0, 30)."...";
									else $token = '';
								?>
								<span>TokenID : <span class="semi-bold"><?=$token?></span></span>
							</div>
							<div class="pull-left">
								<button type="button" class="btn btn-mini btn-warning resetBtn"><i class="fa fa-undo"> 등록된 디바이스 정보 비우기</i></button>
							</div>
							<div class="pull-right">
								<span class="muted">Date Insert : <span><?=$dInfo[$i]['dvi_dt_add']?></span> / </span>
								<span class="muted">Date Last : <span><?=$dInfo[$i]['dvi_dt_last']?></span></span>
							</div>
							<div class="clearfix"> </div>
						</div>
					</div>
				</div>
<?php
			}else{
				emptyDeviceInfoHTML($dInfo[$i]['idx']);
			}
		}
	}else if($_POST['actionType'] == "newEmptyDeviceInfo"){
		$sql = "insert into device_data (dvi_user) values ({$_POST['userIdx']})";
		$DB->Execute($sql);
		$newDeviceIdx = $DB->Insert_ID();

		emptyDeviceInfoHTML($newDeviceIdx);
	}else if($_POST['actionType'] == "updateEmptyDeviceInfo"){
		$sql = "update device_data set ";
		$sql .= "dvi_uuid=NULL,dvi_os=NULL,dvi_model=NULL,dvi_token=NULL,dvi_version=NULL,dvi_dt_add=NULL,dvi_dt_last=NULL ";
		$sql .= "where idx={$_POST['dIdx']}";
		$output = $DB->Execute($sql);

		emptyDeviceInfoHTML($_POST['dIdx']);
	}

	function emptyDeviceInfoHTML($_dIdx){
?>
		<div class="col-xs-12 addDeviceInfo">
			<input type="hidden" class="dIdx" value="<?=$_dIdx?>">
			<div class="grid solid">
				<div class="grid-body">
					<div class="col-sm-12">
						<span class="semi-bold">Empty Device Space</span>
					</div>
						
					<div class="pull-right">
						<button type="button" class="btn btn-mini btn-danger spaceDelBtn"><i class="fa fa-minus"> 등록공간 삭제</i></button>
					</div>
					<div class="clearfix"> </div>
				</div>
			</div>
		</div>
<?php
	}
?>