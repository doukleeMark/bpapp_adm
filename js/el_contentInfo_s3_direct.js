$(document).ready(function() {
	$("select").select2({ minimumResultsForSearch: -1 });

	$("#ct_tag").tagsinput({ trimValue: true });

	$("#content-upload").fileupload({
		url: $(this).attr("action"),
		type: $(this).attr("method"),
		datatype: "xml",
		dropZone: $("#content-upload .dropinput"),
		add: function(event, data) {
			window.onbeforeunload = function() {
				return "You have unsaved changes.";
			};

			var file = data.files[0];
			var maxFileSize = 300;

			var fileExt = file.name.split(".").pop();

			if (!isContent(fileExt)) {
				window.onbeforeunload = null;
				alert("등록가능한 파일이 아닙니다.");
				return false;
			}

			var folders = ["contents", fileExt];

			// 사이즈체크
			if (!fileSizeCheck(file, maxFileSize)) {
				window.onbeforeunload = null;
				alert(
					"첨부파일 사이즈는 " + maxFileSize + "MB 이내로 등록 가능합니다."
				);
				return false;
			}

			var filename = createFileName() + "." + fileExt;

			$(this)
				.find('input[name="Content-Type"]')
				.val(file.type);
			$(this)
				.find('input[name="Content-Length"]')
				.val(file.size);
			$(this)
				.find('input[name="key"]')
				.val((folders.length ? folders.join("/") + "/" : "") + filename);

			data.submit();

			var bar = $(
				'<div class="progress" data-mod="' +
					file.size +
					'"><div class="bar"></div></div>'
			);
			$(this)
				.find(".progress-bar-area")
				.html(bar);
			bar.slideDown("fast");
		},
		progress: function(e, data) {
			var percent = Math.round((data.loaded / data.total) * 100);
			$(this)
				.find('.progress[data-mod="' + data.files[0].size + '"] .bar')
				.css("width", percent + "%")
				.html(percent + "%");
		},
		fail: function(e, data) {
			window.onbeforeunload = null;
			$(this)
				.find('.progress[data-mod="' + data.files[0].size + '"] .bar')
				.css("width", "100%")
				.addClass("red")
				.html("");
		},
		done: function(event, data) {
			window.onbeforeunload = null;

			$(this)
				.find(".progress-bar-area .progress")
				.slideUp("fast");
			$(this)
				.find(".progress-bar-area")
				.html("");

			$(this)
				.find(".fileInput")
				.addClass("hidden");
			$(this)
				.find(".fileDel")
				.removeClass("hidden");

			var original = data.files[0];
			var s3Result = data.result.documentElement.children;

			//s3 파일정보 DB Insert
			var fileGroup = $(this).closest(".file-group");
			$.post(
				"/page/ajax/a_s3FileInfo.php",
				{
					actionType: "insertFileInfo",
					url: s3Result[0].innerHTML,
					fileName: s3Result[2].innerHTML,
					realName: original.name,
					size: original.size
				},
				function(s3_idx) {
					fileGroup
						.find(".link-filename")
						.attr("href", s3Result[0].innerHTML)
						.text(original.name);
					fileGroup.find(".file_idx").val(s3_idx);
				},
				"json"
			);
		}
	});

	$("#thumb-upload").fileupload({
		url: $(this).attr("action"),
		type: $(this).attr("method"),
		datatype: "xml",
		dropZone: $("#thumb-upload .dropinput"),
		add: function(event, data) {
			window.onbeforeunload = function() {
				return "You have unsaved changes.";
			};

			var file = data.files[0];
			var maxFileSize = 1;
			var fileExt = file.name.split(".").pop();

			if (!isImage(fileExt)) {
				window.onbeforeunload = null;
				alert("등록가능한 파일이 아닙니다.");
				return false;
			}

			var folders = ["contents", "thumb"];

			// 사이즈체크
			if (!fileSizeCheck(file, maxFileSize)) {
				window.onbeforeunload = null;
				alert(
					"첨부파일 사이즈는 " + maxFileSize + "MB 이내로 등록 가능합니다."
				);
				return false;
			}

			var filename = createFileName() + "." + fileExt;

			$(this)
				.find('input[name="Content-Type"]')
				.val(file.type);
			$(this)
				.find('input[name="Content-Length"]')
				.val(file.size);
			$(this)
				.find('input[name="key"]')
				.val((folders.length ? folders.join("/") + "/" : "") + filename);

			data.submit();

			var bar = $(
				'<div class="progress" data-mod="' +
					file.size +
					'"><div class="bar"></div></div>'
			);
			$(this)
				.find(".progress-bar-area")
				.html(bar);
			bar.slideDown("fast");
		},
		progress: function(e, data) {
			var percent = Math.round((data.loaded / data.total) * 100);
			$(this)
				.find('.progress[data-mod="' + data.files[0].size + '"] .bar')
				.css("width", percent + "%")
				.html(percent + "%");
		},
		fail: function(e, data) {
			window.onbeforeunload = null;
			$(this)
				.find('.progress[data-mod="' + data.files[0].size + '"] .bar')
				.css("width", "100%")
				.addClass("red")
				.html("");
		},
		done: function(event, data) {
			window.onbeforeunload = null;

			$(this)
				.find(".progress-bar-area .progress")
				.slideUp("fast");
			$(this)
				.find(".progress-bar-area")
				.html("");

			$(this)
				.find(".fileInput")
				.addClass("hidden");
			$(this)
				.find(".fileDel")
				.removeClass("hidden");

			var original = data.files[0];
			var s3Result = data.result.documentElement.children;

			//s3 파일정보 DB Insert
			var fileGroup = $(this).closest(".file-group");
			$.post(
				"/page/ajax/a_s3FileInfo.php",
				{
					actionType: "insertFileInfo",
					url: s3Result[0].innerHTML,
					fileName: s3Result[2].innerHTML,
					realName: original.name,
					size: original.size
				},
				function(s3_idx) {
					fileGroup
						.find(".link-filename")
						.attr("href", s3Result[0].innerHTML)
						.text(original.name);
					fileGroup.find(".file_idx").val(s3_idx);
				},
				"json"
			);
		}
	});

	$(".btn-delfile").on("click", function() {
		var fileGroup = $(this).closest(".file-group");
		$.post(
			"/page/ajax/a_s3FileInfo.php",
			{
				actionType: "deleteFile",
				file_idx: fileGroup.find(".file_idx").val()
			},
			function(res) {
				fileGroup.find(".fileDel").addClass("hidden");
				fileGroup.find(".fileInput").removeClass("hidden");
				fileGroup.find(".file_idx").val("0");
			},
			"json"
		);
	});

	$("#btn_submit").on("click", function() {
		// validation
		var formData = new FormData();

		formData.append("actionType", $("input[name='actionType']").val());
		formData.append("idx", $("input[name='idx']").val());
		formData.append("ct_title", $("input[name='ct_title']").val());
		formData.append("ct_speaker", $("input[name='ct_speaker']").val());
		formData.append("ct_desc", $("textarea[name='ct_desc']").val());
		formData.append("ct_tag", $("input[name='ct_tag']").val());
		formData.append("ct_type", $("input[name='ct_type']:checked").val());
		formData.append("ct_s3_file", $("input[name='ct_s3_file']").val());
		formData.append("ct_s3_thumb", $("input[name='ct_s3_thumb']").val());

		$("#btn_submit").prop("disabled", true);
		$.ajax({
			url: "/page/ajax/a_contentInfo.php",
			data: formData,
			processData: false,
			contentType: false,
			type: "POST",
			success: function(res) {
				$("#btn_submit").prop("disabled", false);
				if ($("input[name='actionType']").val() == "insert") {
					location.href = "/?page=el_contentInfo&idx=" + res;
				} else {
					location.href = "/?page=el_contentList";
				}
			}
		});
	});
});

var generateRandom = function(min, max) {
	var ranNum = Math.floor(Math.random() * (max - min + 1)) + min;
	return ranNum;
};

var createFileName = function() {
	var d = new Date();

	var res = d.getFullYear();
	res += ("0" + (d.getMonth() + 1)).slice(-2);
	res += ("0" + d.getDate()).slice(-2);
	res += ("0" + d.getHours()).slice(-2);
	res += ("0" + d.getMinutes()).slice(-2);
	res += ("0" + d.getSeconds()).slice(-2);
	res += generateRandom(100, 999);
	return res;
};

var fileSizeCheck = function(file, max) {
	var maxSize = max * 1024 * 1024;
	var fileSize = 0;

	// 브라우저 확인
	var browser = navigator.appName;

	// 익스플로러일 경우
	if (browser == "Microsoft Internet Explorer") {
		var oas = new ActiveXObject("Scripting.FileSystemObject");
		fileSize = oas.getFile(file.value).size;
	}
	// 익스플로러가 아닐경우
	else {
		fileSize = file.size;
	}

	return fileSize < maxSize;
};

function isImage(ext) {
	switch (ext.toLowerCase()) {
		case "jpg":
		case "jpeg":
		case "png":
			//etc
			return true;
	}
	return false;
}

function isContent(ext) {
	switch (ext.toLowerCase()) {
		case "mp4":
		case "mp3":
			return true;
	}
	return false;
}
