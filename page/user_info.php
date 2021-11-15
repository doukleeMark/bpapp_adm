<?php
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}
	
	include_once(CLASS_PATH . "/user.class.lib");
	include_once(CLASS_PATH . "/select.class.lib");
	$userClass = new UserClass();
	$selectClass = new SelectClass();
	
	if(!isset($_GET['idx'])) {
		$actionType = "insert";
		$readonly = "";
		$pagetitle = "New User";
	}else {
		$actionType = "update";
		$readonly = "readonly";
		$pagetitle = "Edit User";
		$userRes = $userClass->getUserInfoOne($_GET['idx']);
		$deviceRes = $userClass->getDeviceInfoAll($userRes['idx']);
		$deviceCnt = count($deviceRes);
	}
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
<link href="assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<link href="/css/common.css" rel="stylesheet" type="text/css"/>
<link href="/css/user_info.css" rel="stylesheet" type="text/css"/>

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
					<a href="#" class="active"><?=$pagetitle?></a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>User - <span class="semi-bold"><?=$pagetitle?></span></h3>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>User <span class="semi-bold">Information</span></h4>
						</div>
						<div class="grid-body no-border">
							<form action="/page/user_info_proc.php" class="user_info_validation" id="user_info_validation" method="post" autocomplete="off">	
								<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
								<input type="hidden" id="idx" name="idx" value=<?=$_GET['idx']?>>
								<div class="row column-seperation">
									<div class="col-md-6">
										<div class="form-group">
											<label class="form-label">이메일</label>
											<div class="input-with-icon right">
												<i class=""></i>
												<input name="userId" id="userId" type="text"  class="form-control" placeholder="email@address.com" autocapitalize="off" value="<?=$userRes['ur_id']?>" <?=$readonly?>>
											</div>
										</div>
										<div class="form-group">
											<label class="form-label">이름</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<input name="userName" id="userName" type="text"  class="form-control" placeholder="Name" autocomplete="off" value="<?=$userRes['ur_name']?>">
											</div>
										</div>
										<div class="form-group">
											<label class="form-label">비밀번호</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<input name="userPw" id="userPw" type="password"  class="form-control" placeholder="Password" autocomplete="new-password" >
											</div>

											<div class="input-with-icon right">  
												<i class=""></i>
												<input name="userRePw" id="userRePw" type="password"  class="form-control" placeholder="Password Repeat" >
											</div>											
										</div>
										<div class="form-group">
											<label class="form-label">팀</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<input name="userTeam" id="userTeam" type="text"  class="form-control" placeholder="Team" value="<?=$userRes['ur_team']?>">
											</div>
										</div>
										<!--<div class="form-group">
											<label class="form-label">팀 등급</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<select name="userTeamlevel" id="userTeamlevel" class="select2 form-control">
													<?php
														$res = array(
																array("value"=>"1", "text"=>"Member"),
																array("value"=>"2", "text"=>"Leader")
															);
														
														for($i=0; $i<count($res); $i++) {
															if($userRes['ur_teamlevel']==$res[$i]['value']) $selectedChar = "selected";
															else $selectedChar = "";

															echo "<option value=\"".$res[$i]['value']."\" " . $selectedChar . " >".$res[$i]['text']."</option>";
														}
													?>
												</select>
											</div>
										</div>-->
										<div class="form-group">
											<label class="form-label">직책</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<select name="userPosition" id="userPosition" class="select2 form-control">
													<?php
														$res = array(
																array("value"=>"0", "text"=>"RSM"),
																array("value"=>"1", "text"=>"MR"),
																array("value"=>"2", "text"=>"CP"),
																array("value"=>"3", "text"=>"PM"),
																array("value"=>"4", "text"=>"영업팀장"),
																array("value"=>"5", "text"=>"MKT팀장"),
																array("value"=>"6", "text"=>"그룹장"),
																array("value"=>"7", "text"=>"본부장"),
																array("value"=>"8", "text"=>"부문장"),
                                                                array("value"=>"9", "text"=>"아카데미"),
															);
														
														for($i=0; $i<count($res); $i++) {
															if($userRes['ur_position']==$res[$i]['value']) $selectedChar = "selected";
															else $selectedChar = "";

															echo "<option value=\"".$res[$i]['value']."\" " . $selectedChar . " >".$res[$i]['text']."</option>";
														}
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="form-label">사이트관리권한</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<select name="userLevel" id="userLevel" class="select2 form-control">
													<?php
														$res = array(
																array("value"=>"2", "text"=>"USER"),
																array("value"=>"3", "text"=>"MANAGER")
															);
														if((int)$_SESSION['USER_LEVEL'] >= 9)
															$res[] = array("value"=>"9", "text"=>"ADMIN");
														if((int)$_SESSION['USER_LEVEL'] >= 10)
															$res[] = array("value"=>"10", "text"=>"MASTER");
														
														for($i=0; $i<count($res); $i++) {
															if($userRes['ur_level']==$res[$i]['value']) $selectedChar = "selected";
															else $selectedChar = "";

															echo "<option value=\"".$res[$i]['value']."\" " . $selectedChar . " >".$res[$i]['text']."</option>";
														}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="form-label">그룹</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<select name="userUnit" id="userUnit" class="select2 form-control">
													<option value="0">-</option>
													<?php
														$selectClass->printUnitOptions($userRes['ur_unit']);
													?>
												</select>
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="type-label">소속</label>
                                            <div class="radio radio-primary">
                                                <input id="radio_in" type="radio" name="open_type" value="0"  <?=$userRes['ur_open_type']==0?'checked':''?> <?=$actionType=='insert'?'checked':''?>>
                                                <label for="radio_in">내부</label>
                                                <input id="radio_out" type="radio" name="open_type" value="1" <?=$userRes['ur_open_type']==1?'checked':''?> >
                                                <label for="radio_out">외부</label>
                                            </div>
                                        </div>
										<!--<div class="form-group">
											<label class="form-label">하위조직(승인그룹)</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<input name="userGroupLow" id="userGroupLow" type="text"  class="form-control" placeholder="" value="<?=$userRes['ur_group_low']?>">
											</div>
										</div>
										<div class="form-group">
											<label class="form-label">승인권한</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<select name="userGroupLevel" id="userGroupLevel" class="select2 form-control">
													<?php
														$res = array(
																array("value"=>"0", "text"=>"-"),
																array("value"=>"1", "text"=>"하위조직")
															);
														
														for($i=0; $i<count($res); $i++) {
															if($userRes['ur_group_level']==$res[$i]['value']) $selectedChar = "selected";
															else $selectedChar = "";

															echo "<option value=\"".$res[$i]['value']."\" " . $selectedChar . " >".$res[$i]['text']."</option>";
														}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="form-label">최상위조직(초이스그룹)</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<input name="userGroupHigh" id="userGroupHigh" type="text"  class="form-control" placeholder="" value="<?=$userRes['ur_group_high']?>">
											</div>
										</div>
										<div class="form-group">
											<label class="form-label">초이스권한</label>
											<div class="input-with-icon right">  
												<i class=""></i>
												<select name="userChoiceLevel" id="userChoiceLevel" class="select2 form-control">
													<?php
														$res = array(
																array("value"=>"0", "text"=>"-"),
																array("value"=>"1", "text"=>"최상위조직"),
																array("value"=>"2", "text"=>"전체")
															);
														
														for($i=0; $i<count($res); $i++) {
															if($userRes['ur_choice_level']==$res[$i]['value']) $selectedChar = "selected";
															else $selectedChar = "";

															echo "<option value=\"".$res[$i]['value']."\" " . $selectedChar . " >".$res[$i]['text']."</option>";
														}
													?>
												</select>
											</div>
										</div>-->
									</div>
								</div>
								<div class="form-actions">									
									<div class="pull-right">
										<button class="btn btn-primary btn-cons btn-submit" type="submit">Submit</button>										
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php
				if(isset($_GET['idx'])) {
			?>	
			<div class="row">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>Device <span class="semi-bold">Information</span></h4>
							<div class="pull-right">
								<button type="button" class="btn btn-mini btn-success" id="addSpaceBtn"><i class="fa fa-plus"> 등록공간 추가</i></button>
							</div>
							<div class="clearfix"> </div>
						</div>
						<div class="grid-body no-border" id="deviceList"></div>
					</div>
				</div>
			</div>
			<?php
				}
			?>
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
<script src="assets/plugins/bootstrap-tag/bootstrap-tagsinput.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/user_info.js?v=2111150001" type="text/javascript"></script>
</body>
</html>