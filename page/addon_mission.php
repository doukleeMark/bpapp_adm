<?php	
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}
	
	$sql = "select * from mission_data order by md_group";
	$result = $DB->GetAll($sql);

	$sql = "select * from mission_str LIMIT 1";
	$desc = $DB->GetOne($sql);	
	
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
	
<link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<link href="/css/common.css?v=1" rel="stylesheet" type="text/css"/>
<link href="/css/addon_mission.css" rel="stylesheet" type="text/css"/>

</head>
<body class="">
<?php include COMMON_PATH."/header.php"; ?>
<div class="page-container row-fluid">
	<?php include COMMON_PATH."/sidebar.php"; ?>
	<div class="page-content">
		<div class="content">  
			<ul class="breadcrumb">
				<li>
					<p>Addon</p>
				</li>
				<li>
					<a href="#" class="active">Mission</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Addon - <span class="semi-bold">Mission</span></h3>
			</div>
			<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-pills" id="tab_list">
						<li class="active">
							<a href="#tab_comm">COMMON</a>
						</li>
						<li class="">
							<a href="#tab1">CD</a>
						</li>
						<li class="">
							<a href="#tab2">SC</a>
						</li>
						<li class="">
							<a href="#tab3">Onco</a>
						</li>
						<li class="">
							<a href="#tab4">CLINIC</a>
						</li>
						<li class="">
							<a href="#tab5">병원</a>
						</li>
						<li class="">
							<a href="#tab6">RENAL</a>
						</li>
						<li class="">
							<a href="#tab8">CNS</a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_comm">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4><span class="semi-bold">COMMON</span></h4>
								</div>
								<div class="grid-body no-border">
									<form>
										<input type="hidden" name="actionType" value="missionDesc">
										<div class="row">
											<div class="col-xs-12">
												<h5><span class="semi-bold">미션팝업 하단 설명</span></h5>
												<div class="form-group col-xs-12">
													<input name="descTxt" type="text" class="form-control" placeholder="" value="<?=$desc['descTxt']?>" >
												</div>
											</div>
										</div>
										<div>									
											<div class="pull-right">
												<button class="btn btn-primary btn-cons btn-submit" type="button">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab1">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4><span class="semi-bold">CD</span></h4>
								</div>
								<div class="grid-body no-border mSettingBody">
									<form>
										<input type="hidden" name="actionType" value="missionSetting">
										<div class="row">
											<?php
												for($i=0;$i<count($result);$i++){
													if($result[$i]['md_unit'] == 1){
											?>
											<div class="col-xs-12">
												<h5><span class="semi-bold"><?=$result[$i]['md_name']?></span></h5>
												<input type="hidden" name="group[]" value="<?=$result[$i]['md_group']?>">
												<div class="form-inline">
													<div class="form-group col-xs-6">
														<label class="form-label">현재 매출</label>
														<input name="arrival[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_arrival']?>" >
														<span>억</span>
													</div>
													<div class="form-group col-xs-6">
														<label class="form-label">목표 매출</label>
														<input name="target[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_target']?>" >
														<span>억</span>
													</div>
												</div>
											</div>
											<?php
												}}
											?>
										</div>
										<div>									
											<div class="pull-right">
												<button class="btn btn-primary btn-cons btn-submit" type="button">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab2">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4><span class="semi-bold">SC</span></h4>
								</div>
								<div class="grid-body no-border mSettingBody">
									<form>
										<input type="hidden" name="actionType" value="missionSetting">
										<div class="row">
											<?php
												for($i=0;$i<count($result);$i++){
													if($result[$i]['md_unit'] == 2){
											?>
											<div class="col-xs-12">
												<h5><span class="semi-bold"><?=$result[$i]['md_name']?></span></h5>
												<input type="hidden" name="group[]" value="<?=$result[$i]['md_group']?>">
												<div class="form-inline">
													<div class="form-group col-xs-6">
														<label class="form-label">현재 매출</label>
														<input name="arrival[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_arrival']?>" >
														<span>억</span>
													</div>
													<div class="form-group col-xs-6">
														<label class="form-label">목표 매출</label>
														<input name="target[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_target']?>" >
														<span>억</span>
													</div>
												</div>
											</div>
											<?
												}}
											?>
										</div>
										<div>									
											<div class="pull-right">
												<button class="btn btn-primary btn-cons btn-submit" type="button">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab3">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4><span class="semi-bold">Onco</span></h4>
								</div>
								<div class="grid-body no-border mSettingBody">
									<form>
										<input type="hidden" name="actionType" value="missionSetting">
										<div class="row">
											<?
												for($i=0;$i<count($result);$i++){
													if($result[$i]['md_unit'] == 3){
											?>
											<div class="col-xs-12">
												<h5><span class="semi-bold"><?=$result[$i]['md_name']?></span></h5>
												<input type="hidden" name="group[]" value="<?=$result[$i]['md_group']?>">
												<div class="form-inline">
													<div class="form-group col-xs-6">
														<label class="form-label">현재 매출</label>
														<input name="arrival[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_arrival']?>" >
														<span>억</span>
													</div>
													<div class="form-group col-xs-6">
														<label class="form-label">목표 매출</label>
														<input name="target[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_target']?>" >
														<span>억</span>
													</div>
												</div>
											</div>
											<?
												}}
											?>
										</div>
										<div>									
											<div class="pull-right">
												<button class="btn btn-primary btn-cons btn-submit" type="button">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab4">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4><span class="semi-bold">CLINIC</span></h4>
								</div>
								<div class="grid-body no-border mSettingBody">
									<form>
										<input type="hidden" name="actionType" value="missionSetting">
										<div class="row">
											<?
												for($i=0;$i<count($result);$i++){
													if($result[$i]['md_unit'] == 4){
											?>
											<div class="col-xs-12">
												<h5><span class="semi-bold"><?=$result[$i]['md_name']?></span></h5>
												<input type="hidden" name="group[]" value="<?=$result[$i]['md_group']?>">
												<div class="form-inline">
													<div class="form-group col-xs-6">
														<label class="form-label">현재 매출</label>
														<input name="arrival[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_arrival']?>" >
														<span>억</span>
													</div>
													<div class="form-group col-xs-6">
														<label class="form-label">목표 매출</label>
														<input name="target[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_target']?>" >
														<span>억</span>
													</div>
												</div>
											</div>
											<?
												}}
											?>
										</div>
										<div>									
											<div class="pull-right">
												<button class="btn btn-primary btn-cons btn-submit" type="button">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab5">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4><span class="semi-bold">병원</span></h4>
								</div>
								<div class="grid-body no-border mSettingBody">
									<form>
										<input type="hidden" name="actionType" value="missionSetting">
										<div class="row">
											<?
												for($i=0;$i<count($result);$i++){
													if($result[$i]['md_unit'] == 5){
											?>
											<div class="col-xs-12">
												<h5><span class="semi-bold"><?=$result[$i]['md_name']?></span></h5>
												<input type="hidden" name="group[]" value="<?=$result[$i]['md_group']?>">
												<div class="form-inline">
													<div class="form-group col-xs-6">
														<label class="form-label">현재 매출</label>
														<input name="arrival[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_arrival']?>" >
														<span>억</span>
													</div>
													<div class="form-group col-xs-6">
														<label class="form-label">목표 매출</label>
														<input name="target[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_target']?>" >
														<span>억</span>
													</div>
												</div>
											</div>
											<?
												}}
											?>
										</div>
										<div>									
											<div class="pull-right">
												<button class="btn btn-primary btn-cons btn-submit" type="button">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab6">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4><span class="semi-bold">RENAL</span></h4>
								</div>
								<div class="grid-body no-border mSettingBody">
									<form>
										<input type="hidden" name="actionType" value="missionSetting">
										<div class="row">
											<?
												for($i=0;$i<count($result);$i++){
													if($result[$i]['md_unit'] == 6){
											?>
											<div class="col-xs-12">
												<h5><span class="semi-bold"><?=$result[$i]['md_name']?></span></h5>
												<input type="hidden" name="group[]" value="<?=$result[$i]['md_group']?>">
												<div class="form-inline">
													<div class="form-group col-xs-6">
														<label class="form-label">현재 매출</label>
														<input name="arrival[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_arrival']?>" >
														<span>억</span>
													</div>
													<div class="form-group col-xs-6">
														<label class="form-label">목표 매출</label>
														<input name="target[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_target']?>" >
														<span>억</span>
													</div>
												</div>
											</div>
											<?
												}}
											?>
										</div>
										<div>									
											<div class="pull-right">
												<button class="btn btn-primary btn-cons btn-submit" type="button">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab8">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4><span class="semi-bold">CNS</span></h4>
								</div>
								<div class="grid-body no-border mSettingBody">
									<form>
										<input type="hidden" name="actionType" value="missionSetting">
										<div class="row">
											<?
												for($i=0;$i<count($result);$i++){
													if($result[$i]['md_unit'] == 8){
											?>
											<div class="col-xs-12">
												<h5><span class="semi-bold"><?=$result[$i]['md_name']?></span></h5>
												<input type="hidden" name="group[]" value="<?=$result[$i]['md_group']?>">
												<div class="form-inline">
													<div class="form-group col-xs-6">
														<label class="form-label">현재 매출</label>
														<input name="arrival[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_arrival']?>" >
														<span>억</span>
													</div>
													<div class="form-group col-xs-6">
														<label class="form-label">목표 매출</label>
														<input name="target[]" type="text" class="form-control" placeholder="0" value="<?=$result[$i]['md_target']?>" >
														<span>억</span>
													</div>
												</div>
											</div>
											<?
												}}
											?>
										</div>
										<div>									
											<div class="pull-right">
												<button class="btn btn-primary btn-cons btn-submit" type="button">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script> 
<script src="assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="assets/plugins/breakpoints.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
<script src="assets/plugins/bootstrap-select2/select2.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
<script src="/plugins/smarteditor/js/HuskyEZCreator.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/addon_mission.js" type="text/javascript"></script>

</body>
</html>