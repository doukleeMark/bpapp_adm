<?php
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}
	
	include_once(CLASS_PATH . "/user.class.lib");
	$userClass = new UserClass();
	
	$pointScopeRes = $userClass->getPointSettingInfo(1);
	$pointRoleRes = $userClass->getPointSettingInfo(3);

	$sql = "select * from unit_data";
	$units = $DB->GetAll($sql);

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
<link href="/css/user_pointSetting.css" rel="stylesheet" type="text/css"/>

</head>

<body class="">
<?php include COMMON_PATH."/header.php"; ?>
<div class="page-container row-fluid">
	<?php include COMMON_PATH."/sidebar.php"; ?>
	<div class="page-content">
		<div class="content">  
			<ul class="breadcrumb">
				<li>
					<p>User</p>
				</li>
				<li>
					<a href="#" class="active">Point Setting</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>User - <span class="semi-bold">Point Setting</span></h3>
			</div>
			<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-pills" id="tab-4">
						<li class="active">
							<a href="#tab4scope">Point Scope</a>
						</li>
						<li class="">
							<a href="#tab4role">Point Role</a>
						</li>
					</ul>
					<div class="tab-content">

						<div class="tab-pane active" id="tab4scope">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4>Point <span class="semi-bold">Scope</span></h4>
								</div>
								<div class="grid-body no-border pointSettingBody">
									<form id="form_scope">
										<input type="hidden" name="actionType" value="pointSetting">
											<?php
												for($i=0;$i<9;$i++){
													if($i==0)$scope_group = "Common";
													else {
														for($j=0;$j<count($units);$j++){
															if($i == $units[$j]['idx']){
																$scope_group = $units[$j]['unit_name'];
																break;
															}
														}
													}
											?>
											<div class="row">
											<h5><?=$scope_group?></h5>
											<?php
													for($j=0;$j<count($pointScopeRes);$j++){
														if( ($i == 0 && strpos($pointScopeRes[$j]['ptr_code'], "choice") === false) || 
															($i != 0 && strpos($pointScopeRes[$j]['ptr_code'], "_u".$i) !== false) ){
											?>
											
											<div class="col-sm-6 col-xs-12">
												<div class="form-inline">
													<div class="form-group">
														<label class="form-label"><?=$pointScopeRes[$j]['ptr_title']?></label>
														<input name="<?=$pointScopeRes[$j]['ptr_code']?>" type="text" class="form-control" placeholder="0" value="<?=$pointScopeRes[$j]['ptr_point']?>" >
														<span>pt</span>
													</div>
												</div>
											</div>
											<?php
														}
													}
											?>
											</div>
											<?php
												}
											?>
										<div class="form-actions">									
											<div class="pull-right">
												<button class="btn btn-primary btn-cons btn-submit" id="scopeSettingBtn" type="button">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab4role">
							<div class="grid simple">
								<div class="grid-title no-border">
									<h4>Point <span class="semi-bold">Role</span></h4>
								</div>
								<div class="grid-body no-border pointSettingBody">
									<form id="form_role">
										<input type="hidden" name="actionType" value="pointSetting">
										<div class="row">
											<?php
												for($i=0;$i<count($pointRoleRes);$i++){
											?>
											<div class="col-sm-6 col-xs-12">
												<div class="form-inline">
													<div class="form-group">
														<label class="form-label"><?=$pointRoleRes[$i]['ptr_title']?></label>
														<input name="<?=$pointRoleRes[$i]['ptr_code']?>" type="text" class="form-control" placeholder="0" value="<?=$pointRoleRes[$i]['ptr_point']?>" >
														<span>%</span>
													</div>
												</div>
											</div>	
											<?php
												}
											?>
										</div>
										<div class="form-actions">									
											<div class="pull-right">
												<button class="btn btn-primary btn-cons btn-submit" id="roleSettingBtn" type="button">Submit</button>
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
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/user_pointSetting.js" type="text/javascript"></script> 
</body>
</html>