<?php	
	header("Pragma;no-cache");
	header("Cache-Control;no-cache,must-revalidate");

	if (!$_SESSION['USER_ID']){
		echo "<script>location.href='/?page=login'</script>";
		return;
	}

	include_once(CLASS_PATH . "/el.class.lib");
	include_once(CLASS_PATH . "/s3.class.lib");
	include_once(CLASS_PATH . "/code.class.lib");

	$elClass = new ELClass();
	$s3Class = new S3Class();
	$codeClass = new codeClass();

	if(!isset($_GET['idx'])) {
		$actionType = "insert";
		$readonly = "";
		$pagetitle = "New";
	}else {
		$actionType = "update";
		$readonly = "readonly";
		$pagetitle = "Edit";
		$disabled = "disabled";

		$rating = 0;
		$playSec = 0;

		$contRes = $elClass->getContentInfo($_GET['idx']);
		$ratingRes = $elClass->getContentRating($_GET['idx']);
		if(isset($ratingRes['rating'])){
			$rating = $ratingRes['rating'];
		}
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
	
<link href="/plugins/bootstrap-slider/10.2.1/bootstrap-slider.min.css" rel="stylesheet" type="text/css">
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
<link rel="stylesheet" type="text/css" href="/plugins/dataTables/DataTables-1.10.18/css/dataTables.bootstrap4.css"/>
<link rel="stylesheet" type="text/css" href="/plugins/dataTables/Buttons-1.5.4/css/buttons.bootstrap4.css"/>
<link rel="stylesheet" type="text/css" href="/plugins/dataTables/RowReorder-1.2.4/css/rowReorder.bootstrap4.css"/>
<link rel="stylesheet" type="text/css" href="/plugins/dataTables/Select-1.2.6/css/select.bootstrap4.css"/>
<link rel="stylesheet" type="text/css" href="/plugins/bootstrapformhelpers/css/bootstrap-formhelpers-number.css"/>
<link href="/css/common.css?v=1" rel="stylesheet" type="text/css"/>
<link href="/css/el_contentInfo.css" rel="stylesheet" type="text/css"/>

</head>

<body class="">
<?php include COMMON_PATH."/header.php"; ?>
<div class="page-container row-fluid">
	<?php include COMMON_PATH."/sidebar.php"; ?>
	<div class="page-content">
		<div class="content">  
			<ul class="breadcrumb">
				<li>
					<p>E-learning</p>
				</li>
				<li>
					<a href="#" class="active">Contents</a>
				</li>
			</ul>
			<div class="page-title"> <i class="icon-custom-left" ></i>
				<h3>Content Info - <span class="semi-bold"><?=$pagetitle?></span></h3>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>Contents <span class="semi-bold">Information</span></h4>
						</div>
						<div class="grid-body no-border">
							<input type="hidden" id="actionType" name="actionType" value=<?=$actionType?>>
							<input type="hidden" id="idx" name="idx" value=<?=$_GET['idx']?>>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="form-label">제품</label>
										<div class="input-with-icon right">  
											<i class=""></i>
											<select name="code_pd" id="code_pd" class="select2 form-control" multiple="multiple" style="width: 100%;">
												<?php

													$codePD = $codeClass->getByGroupList("PD");

													$_sel = str_replace("X", "", $contRes['ct_code_pd']);
													$sel_list = explode(",", $_sel);
													
													for($i=0; $i<count($codePD); $i++) {
														for($j=0; $j<count($sel_list);$j++){
															$selectedChar = "";
															if($sel_list[$j]==$codePD[$i]['idx']){
																$selectedChar = "selected";
																break;
															}
														}
														echo "<option value=\"".$codePD[$i]['idx']."\" " . $selectedChar . " >".$codePD[$i]['code_name']."</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="form-label">질환</label>
										<div class="input-with-icon right">  
											<i class=""></i>
											<select name="code_di" id="code_di" class="select2 form-control" multiple="multiple" style="width: 100%;">
												<?php

													$codeDI = $codeClass->getByGroupList("DI");

													$_sel = str_replace("X", "", $contRes['ct_code_di']);
													$sel_list = explode(",", $_sel);
													
													for($i=0; $i<count($codeDI); $i++) {
														for($j=0; $j<count($sel_list);$j++){
															$selectedChar = "";
															if($sel_list[$j]==$codeDI[$i]['idx']){
																$selectedChar = "selected";
																break;
															}
														}
														echo "<option value=\"".$codeDI[$i]['idx']."\" " . $selectedChar . " >".$codeDI[$i]['code_name']."</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="form-label">Title</label>
										<div class="input-with-icon right">
											<i class=""></i>
											<input name="ct_title" type="text"  class="form-control" placeholder="" value="<?=$contRes['ct_title']?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="form-label">Speaker</label>
										<div class="input-with-icon right">
											<i class=""></i>
											<input name="ct_speaker" type="text"  class="form-control" placeholder="" value="<?=$contRes['ct_speaker']?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="form-label">Description</label>
										<div class="input-with-icon right">
											<i class=""></i>
											<textarea name="ct_desc" id="ct_desc" rows="3" style="width:100%;resize: none;"><?=$contRes['ct_desc']?></textarea>
										</div>
									</div>
									<?php
										if($actionType == 'update'){
									?>
									<div class="form-group">
										<label class="form-label">Rating</label>
										<p><?=$rating?></p>
									</div>
									<?php
										}
									?>
								</div>
								
								<div class="col-md-6">

									<div class="form-group">
										<label class="form-label">신입/경력</label>
										<div class="input-with-icon right">  
											<i class=""></i>
											<select name="code_gd" id="code_gd" class="select2 form-control" multiple="multiple" style="width: 100%;">
												<?php

													$codeGD = $codeClass->getByGroupList("GD");

													$_sel = str_replace("X", "", $contRes['ct_code_gd']);
													$sel_list = explode(",", $_sel);
													
													for($i=0; $i<count($codeGD); $i++) {
														for($j=0; $j<count($sel_list);$j++){
															$selectedChar = "";
															if($sel_list[$j]==$codeGD[$i]['idx'] || ($actionType == 'insert' && $codeGD[$i]['idx'] == 6 )){
																$selectedChar = "selected";
																break;
															}
														}
														echo "<option value=\"".$codeGD[$i]['idx']."\" " . $selectedChar . " >".$codeGD[$i]['code_name']."</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="form-label">직책</label>
										<div class="input-with-icon right">  
											<i class=""></i>
											<select name="code_lv" id="code_lv" class="select2 form-control" multiple="multiple" style="width: 100%;">
												<?php

													$codeLV = $codeClass->getByGroupList("LV");

													$_sel = str_replace("X", "", $contRes['ct_code_lv']);
													$sel_list = explode(",", $_sel);
													
													for($i=0; $i<count($codeLV); $i++) {
														for($j=0; $j<count($sel_list);$j++){
															$selectedChar = "";
															if($sel_list[$j]==$codeLV[$i]['idx'] || $actionType == 'insert'){
																$selectedChar = "selected";
																break;
															}
														}
														echo "<option value=\"".$codeLV[$i]['idx']."\" " . $selectedChar . " >".$codeLV[$i]['code_name']."</option>";
													}

												?>
											</select>
										</div>
									</div>
                                    <div class="form-group">
                                        <label class="form-label">Sales Skill</label>
                                        <div class="input-with-icon right">
                                            <i class=""></i>
                                            <select name="code_ss" id="code_ss" class="select2 form-control" multiple="multiple" style="width: 100%;">
                                                <?php

                                                $codeSS = $codeClass->getByGroupList("SS");

                                                $_sel = str_replace("X", "", $contRes['ct_code_ss']);
                                                $sel_list = explode(",", $_sel);

                                                for($i=0; $i<count($codeSS); $i++) {
                                                    for($j=0; $j<count($sel_list);$j++){
                                                        $selectedChar = "";
                                                        if($sel_list[$j]==$codeSS[$i]['idx']){
                                                            $selectedChar = "selected";
                                                            break;
                                                        }
                                                    }
                                                    echo "<option value=\"".$codeSS[$i]['idx']."\" " . $selectedChar . " >".$codeSS[$i]['code_name']."</option>";
                                                }

                                                ?>
                                            </select>
                                        </div>
                                    </div>
									<div class="form-group">
										<label class="type-label">소속</label>
										<div class="radio radio-primary">
											<input id="radio_in" type="radio" name="open_type" value="0"  <?=$contRes['ct_open_type']==0?'checked':''?> <?=$_SESSION['USER_LEVEL']<9?"disabled":"";?> <?=$actionType=='insert'?'checked':''?>>
											<label for="radio_in">내부공개</label>
											<input id="radio_out" type="radio" name="open_type" value="1" <?=$contRes['ct_open_type']==1?'checked':''?> <?=$_SESSION['USER_LEVEL']<9?"disabled":"";?> >
											<label for="radio_out">전체공개</label>
										</div>
									</div>
                                    <div class="form-group">
                                        <label class="type-label">Type</label>
                                        <div class="radio radio-primary">
                                            <input id="radio_v" type="radio" name="ct_type" value="V" <?=($contRes['ct_type']=="V"|| !isset($contRes))?"checked":"";?> <?=$disabled?>>
                                            <label for="radio_v">Video</label>
                                            <input id="radio_a" type="radio" name="ct_type" value="A" <?=($contRes['ct_type']=="A")?"checked":"";?> <?=$disabled?>>
                                            <label for="radio_a">Audio</label>
                                        </div>
                                    </div>
									<?php
										if($contRes['ct_s3_file']>0){
											$fileInfo = $s3Class->getS3Info($contRes['ct_s3_file']);
											$playSec = $fileInfo['s3_play_sec'];
										}
									?>
									<div class="form-group file-group">
										<form action="/page/ajax/a_fileUpload.php" method="POST" enctype="multipart/form-data" id="content-upload" class="file-upload">
											<input type="hidden" class="file_idx" name="ct_s3_file" value=<?=$contRes['ct_s3_file']?>>
											<input type="hidden" class="file_size" value='500'>
											<input type="hidden" id="playTime" name="playTime" value=<?=$playSec?>>
											<label class="form-label">Content</label>
											<div class="input-group fileInput <?=($contRes['ct_s3_file'] > 0)?'hidden':'';?>" >
												<input type="text" class="form-control dropinput" placeholder="마우스로 파일을 끌어오세요." readonly>
												<span class="input-group-btn">
													<span class="btn btn-default btn-file">
														<input type="file" name="file" accept="video/mp4,audio/mp3" >찾아보기
													</span>
												</span>
											</div>
											<div class="input-group fileDel <?=($contRes['ct_s3_file'] > 0)?'':'hidden';?>">
												<a href="<?=$fileInfo['s3_url']?>" class="link-filename" target="_blank"><?=$fileInfo['s3_real_name']?></a>
												<span class="input-group-btn">
													<span class="btn btn-warning btn-delfile">삭제</span>
												</span>
											</div>
											<div class="progress-bar-area"></div>
											<span class="help">* 업로드 최대 용량은 500MB 입니다. 용량이 클경우, 관리자에게 문의하세요.</span>
										</form>
									</div>
									<?php
										if($contRes['ct_s3_thumb']>0){
											$fileInfo = $s3Class->getS3Info($contRes['ct_s3_thumb']);
										}
									?>
									<div class="form-group file-group">
										<form action="/page/ajax/a_fileUpload.php" method="POST" enctype="multipart/form-data" id="thumb-upload" class="file-upload">
											<input type="hidden" class="file_idx" name="ct_s3_thumb" value=<?=$contRes['ct_s3_thumb']?>>
											<input type="hidden" class="file_size" value='1'>
											<label class="form-label">Thumbnail</label>
											<div class="input-group fileInput <?=($contRes['ct_s3_thumb'] > 0)?'hidden':'';?>" >
												<input type="text" class="form-control dropinput" placeholder="마우스로 파일을 끌어오세요." readonly>
												<span class="input-group-btn">
													<span class="btn btn-default btn-file">
														<input type="file" name="file" accept="image/x-png,image/jpeg" >찾아보기
													</span>
												</span>
											</div>
											<div class="fileDel <?=($contRes['ct_s3_thumb'] > 0)?'':'hidden';?>" >
												<img src="<?=$fileInfo['s3_url']?>" alt="thumbnail" style="width:80px;" />
												<div class="input-group  ">
													<a href="<?=$fileInfo['s3_url']?>" class="link-filename" target="_blank"><?=$fileInfo['s3_real_name']?></a>
													<span class="input-group-btn">
														<span class="btn btn-warning btn-delfile">삭제</span>
													</span>
												</div>
											</div>
											<div class="progress-bar-area"></div>
											<span class="help">* 업로드 최대 용량은 1M 입니다. </span>
										</form>
									</div>
								</div>
							</div>
							<div class="form-actions">
								<div class="pull-right">
									<button class="btn btn-primary btn-cons btnSubmit" type="button">Submit</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
				if($actionType == 'update'){
					// 등록된 퀴즈가 있는지 확인
					$quizRes = $elClass->getSupriseQuizInfo($_GET['idx']);	
			?>
			<div class="row">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>Suprise Quiz <span class="semi-bold">Information</span></h4>
							<div class="pull-right">
								<button type="button" class="btn btn-mini btn-success" id="btnAddQuiz"><i class="fa fa-plus"> Add Suprise Quiz</i></button>
							</div>
							<div class="clearfix"> </div>
						</div>
						<div class="grid-body no-border">
							<form id="supriseQuizForm">

								<ul class="nav nav-pills" id="tab-menu">
									<?php
										for($i=0;$i<count($quizRes);$i++){
									?>
									<li class="<?=($i==0)?'active':''?>" no="<?=$i+1?>"><a href="#q<?=$i+1?>">Q<?=$i+1?></a></li>
									<?php
										}
									?>
								</ul>
								<div class="tab-content" id="question_group">
									<?php
										for($i=0;$i<count($quizRes);$i++){
											
											$quiz = $quizRes[$i];
											
											include PAGE_PATH."/add/add_supriseQuiz.php";
										}
									?>
								</div>
								<p class="supriseQuiz-blank <?=(count($quizRes) > 0)?"hidden":""?>">깜작퀴즈를 추가해주세요.</p>
							</form>
							<div class="form-actions">
								<div class="pull-right">
									<button class="btn btn-primary btn-cons btnSubmit" type="button">Submit</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row select-group">
				<div class="col-xs-12">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>TEST <span class="semi-bold">Information</span></h4>
						</div>
						<div class="grid-body no-border">
							<div class="row">
								<div class="col-md-12">
									<div class="grid simple">
										<div class="grid-title no-border">
											<h4><span class="semi-bold">Excel Upload</span></h4>
											<p class="text">업로드 기본 엑셀파일 : <a href="/excel/content_test_template_excel.php">[다운로드]</a></p>
										</div>
										<div class="grid-body no-border">							
											<form class="test_upload" id="test_upload">	
												<div class="row">
													<div class="col-xs-6">									
														<div class="input-group">
															<input type="text" class="form-control" readonly>
															<span class="input-group-btn">
																<span class="btn btn-default btn-file">
																	<input type="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" if="testListFile" name="testListFile">찾아보기
																</span>
															</span>
														</div>
													</div>
													<button class="btn btn-cons" type="button" id="uploadButton">Upload</button>
												</div>
											</form>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<label class="form-label">Random Test (Max 100) : <span class="semi-bold testCount">0</span></label><br/>
									</div>
									<table class="table table-bordered" id="testTable" style="width:100%">
										<thead>
											<tr>
												<th></th>
												<th style="width:100%">Question</th>
											</tr>
										</thead>
									</table>
								</div>		
							</div>

							<div class="add-quiz" id="quiz_group">
								<?php
									include PAGE_PATH."/add/add_quiz.php";
								?>
							</div>

							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label class="form-label">Show Test</label>
										<input type="text" id="ct_test_count" name="ct_test_count" class="form-control bfh-number" value="<?=$contRes['ct_test_count']?>">
									</div>
								</div>
							</div>

							<div class="form-actions">
								<div class="pull-right">
									<button class="btn btn-primary btn-cons btnSubmit" type="button">Submit</button>
								</div>
							</div>
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
<script src="/plugins/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="assets/plugins/breakpoints.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script> 
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="/plugins/blueimp-file-upload/9.5.7/jquery.fileupload.js"></script>
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
<script src="assets/plugins/bootstrap-select2/select2.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-tag/bootstrap-tagsinput.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-autonumeric/autoNumeric.js" type="text/javascript"></script>
<script>$.fn.slider = null</script>
<script src="/plugins/bootstrap-slider/10.2.1/bootstrap-slider.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/plugins/dataTables/DataTables-1.10.18/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="/plugins/dataTables/DataTables-1.10.18/js/dataTables.bootstrap4.js"></script>
<script type="text/javascript" src="/plugins/dataTables/Buttons-1.5.4/js/dataTables.buttons.js"></script>
<script type="text/javascript" src="/plugins/dataTables/Buttons-1.5.4/js/buttons.bootstrap4.js"></script>
<script type="text/javascript" src="/plugins/dataTables/RowReorder-1.2.4/js/dataTables.rowReorder.js"></script>
<script type="text/javascript" src="/plugins/dataTables/Select-1.2.6/js/dataTables.select.js"></script>
<script type="text/javascript" src="/plugins/bootstrapformhelpers/js/bootstrap-formhelpers-number.js"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript"></script>
<script src="/js/el_contentInfo.js?v=2111260001" type="text/javascript"></script>
</body>
</html>