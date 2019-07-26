<?php
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}
	
	include_once(CLASS_PATH . "/survey.class.lib");
	$surveyClass = new SurveyClass();
	
	if(!isset($_GET['idx'])) {
		$actionType = "insert";
		$readonly = "";
		$pagetitle = "New";
	}else {
		$actionType = "update";
		$readonly = "readonly";
		$pagetitle = "Edit";

		$surveyRes = $surveyClass->getSurveyInfo($_GET['idx']);
		$surveySub = $surveyClass->getSurveySubInfo($_GET['idx']);
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
<link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<link href="/css/common.css" rel="stylesheet" type="text/css"/>
<link href="/css/survey_info.css" rel="stylesheet" type="text/css"/>

</head>

<body class="">
<?php include COMMON_PATH."/header.php"; ?>
<div class="page-container row-fluid">
	<?php include COMMON_PATH."/sidebar.php"; ?>
	<div class="page-content">
		<div class="content">  
			<ul class="breadcrumb">
				<li>
					<p>Quick Poll</p>
				</li>
				<li>
					<a href="#" class="active">Quick Poll Info</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Quick Poll - <span class="semi-bold"><?=$pagetitle?></span></h3>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border"></div>
						<div class="grid-body no-border">
							<form>	
								<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
								<input type="hidden" id="idx" name="idx" value=<?=$_GET['idx']?>>
								<input type="hidden" id="subCnt" name="subCnt" value=''>
								<div class="row">
									<div class="col-xs-12">
										<div class="form-group">
											<label class="form-label">Quick Poll Title</label>
											<div class="controls">
												<input name="svf_title" id="svf_title" type="text"  class="form-control" placeholder="" value="<?=$surveyRes['svf_title']?>" >
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="row-fluid">
											<label class="share-label">Visible</label>
											<div class="radio radio-primary">
												<input id="radio_on" type="radio" name="svf_visible" value="1" <?=($surveyRes['svf_visible']=="1"|| !isset($surveyRes))?"checked":"";?> <?=$disabled?>>
												<label for="radio_on">ON</label>
												<input id="radio_off" type="radio" name="svf_visible" value="0" <?=($surveyRes['svf_visible']=="0")?"checked":"";?> <?=$disabled?>>
												<label for="radio_off">OFF</label>
											</div>
										</div>
									</div>
								</div>
								<?php
									if($actionType == "insert"){
								?>
								<div class="actions">
									<div class="pull-right">
										<button class="btn btn-primary btn-cons" id="insertBtn" type="button">Submit</button>
									</div>
								</div>
								<?php
									}
								?>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php
				if($actionType == "update"){
			?>
			<div class="add_btn_group">
				<button type="button" class="btn btn-cons btn-primary" id="addSubBtn"><i class="fa fa-plus"></i></button>
				<button type="button" class="btn btn-cons btn-primary" id="delSubBtn"><i class="fa fa-minus"></i></button>
			</div>
			<div class="row" id="question_add_zone">
				<div class="col-xs-12">
					<ul class="nav nav-pills" id="tab-menu">
						<?php
							for($i=0;$i<count($surveySub) || $i==0;$i++){
						?>
						<li class="<?=($i==0)?'active':''?>"><a href="#q<?=$i+1?>">Q<?=$i+1?></a></li>
						<?php
							}
						?>
					</ul>
					<div class="tab-content" id="question_group">
						<?php
							for($i=0;$i<count($surveySub) || $i==0;$i++){
						?>
						<div class="tab-pane <?=($i==0)?'active':''?>" id="q<?=$i+1?>">
							<div class="row">
								<div class="col-xs-12">
									<div class="form-group">
										<label class="form-label">Question</label>
										<div class="controls">
											<input name="svs_question" type="text"  class="form-control" placeholder="" value="<?=$surveySub[$i]['svs_question']?>" >
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="controls">
										<div class="answer-group">
											<label class="">Item 1</label>
											<input name="svs_item_1" type="text"  class="form-control" placeholder="" value="<?=$surveySub[$i]['svs_item_1']?>" >
										</div>
										<div class="answer-group">
											<label class="">Item 2</label>
											<input name="svs_item_2" type="text"  class="form-control" placeholder="" value="<?=$surveySub[$i]['svs_item_2']?>" >
										</div>
										<div class="answer-group">
											<label class="">Item 3</label>
											<input name="svs_item_3" type="text"  class="form-control" placeholder="" value="<?=$surveySub[$i]['svs_item_3']?>" >
										</div>
										<div class="answer-group">
											<label class="">Item 4</label>
											<input name="svs_item_4" type="text"  class="form-control" placeholder="" value="<?=$surveySub[$i]['svs_item_4']?>" >
										</div>
										<div class="answer-group">
											<label class="">Item 5</label>
											<input name="svs_item_5" type="text"  class="form-control" placeholder="" value="<?=$surveySub[$i]['svs_item_5']?>" >
										</div>
									</div>
									<span class="help">* 최대 20자까지 입력이 가능합니다.</span>
								</div>
							</div>
						</div>
						<?php
							}
						?>
					</div>
					<div class="actions">
						<div class="pull-right">
							<button class="btn btn-primary btn-cons" id="updateBtn" type="button">Submit</button>
						</div>
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
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/survey_info.js?v=1901021139" type="text/javascript"></script> 
</body>
</html>