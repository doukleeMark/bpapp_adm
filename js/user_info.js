$(document).ready(function() {
	$("select").select2({ minimumResultsForSearch: -1 });

	if ($("#actionType").attr("value") == "insert") {
		$("#user_info_validation").validate({
			focusInvalid: false,
			ignore: "",
			rules: {
				userId: {
					userId_check: true,
					required: true
				},
				userName: {
					required: true
				},
				userRePw: {
					equalTo: "#userPw"
				}
			},
			messages: {
				userId: {
					required: "필수입력!"
				},
				userName: {
					required: "필수입력!"
				},
				userRePw: {
					equalTo: "불일치!"
				}
			},
			invalidHandler: function(event, validator) {
				//display error alert on form submit
			},
			errorPlacement: function(error, element) {
				// render error placement for each input type
				var icon = $(element)
					.parent(".input-with-icon")
					.children("i");
				var parent = $(element).parent(".input-with-icon");
				icon.removeClass("fa fa-check").addClass("fa fa-exclamation");
				parent.removeClass("success-control").addClass("error-control");

				$('<span class="error"></span>')
					.insertAfter(element)
					.append(error);
			},
			highlight: function(element) {
				// hightlight error inputs
				var parent = $(element).parent();
				parent.removeClass("success-control").addClass("error-control");
			},
			unhighlight: function(element) {
				// revert the change done by hightlight
			},
			success: function(label, element) {
				var icon = $(element)
					.parent(".input-with-icon")
					.children("i");
				var parent = $(element).parent(".input-with-icon");
				icon.removeClass("fa fa-exclamation").addClass("fa fa-check");
				parent.removeClass("error-control").addClass("success-control");
			},
			submitHandler: function(form) {
				form.submit();
			}
		});

		$.validator.addMethod(
			"userId_check",
			function(value, element) {
				var is_valid = false;
				$.ajax({
					url: "/page/ajax/a_getUserIdCheck.php",
					type: "POST",
					dataType: "html",
					data: { userId: jQuery.trim($("#userId").val()) },
					async: false,
					success: function(data) {
						is_valid = data == false;
					}
				});
				return is_valid;
			},
			"이미 가입된 이메일입니다."
		);
	} else {
		$("#user_info_validation").validate({
			focusInvalid: false,
			ignore: "",
			rules: {
				userName: {
					required: true
				},
				userPw: {
					minlength: 4
				},
				userRePw: {
					equalTo: "#userPw"
				}
			},
			messages: {
				userName: {
					required: "필수입력!"
				},
				userPw: {
					minlength: "4자 이상입력!"
				},
				userRePw: {
					equalTo: "불일치!"
				}
			},
			invalidHandler: function(event, validator) {
				//display error alert on form submit
			},
			errorPlacement: function(error, element) {
				// render error placement for each input type
				var icon = $(element)
					.parent(".input-with-icon")
					.children("i");
				var parent = $(element).parent(".input-with-icon");
				icon.removeClass("fa fa-check").addClass("fa fa-exclamation");
				parent.removeClass("success-control").addClass("error-control");

				$('<span class="error"></span>')
					.insertAfter(element)
					.append(error);
			},
			highlight: function(element) {
				// hightlight error inputs
				var parent = $(element).parent();
				parent.removeClass("success-control").addClass("error-control");
			},
			unhighlight: function(element) {
				// revert the change done by hightlight
			},
			success: function(label, element) {
				var icon = $(element)
					.parent(".input-with-icon")
					.children("i");
				var parent = $(element).parent(".input-with-icon");
				icon.removeClass("fa fa-exclamation");
				parent.removeClass("error-control");
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	}

	// 등록공간 정보 가져오기
	if ($("#actionType").val() == "update") {
		$.post(
			"/page/ajax/a_userInfo.php",
			{
				actionType: "getDeviceIdx",
				idx: $("#idx").val()
			},
			function(device_idx) {
				if (device_idx.length < 1) return;

				var idxChar = "";
				for (var i = 0; i < device_idx.length; i++) {
					idxChar += device_idx[i]["idx"] + ",";
				}
				idxChar = idxChar.substring(0, idxChar.length - 1);

				$.post(
					"/page/add/add_deviceInfo.php",
					{
						actionType: "deviceInfo",
						deviceIdx: idxChar
					},
					function(addData) {
						$("#deviceList").append(addData);
					}
				);
			},
			"json"
		);
	}

	// 등록공간 추가 버튼
	$("#addSpaceBtn").on("click", function() {
		if (confirm("등록공간을 추가하시겠습니까?")) {
			// 등록공간 추가 가능여부 확인
			$.post(
				"/page/ajax/a_userInfo.php",
				{
					actionType: "checkCntDevice",
					idx: $("#idx").val()
				},
				function(cnt) {
					if (cnt < 3) {
						$.post(
							"/page/add/add_deviceInfo.php",
							{
								actionType: "newEmptyDeviceInfo",
								userIdx: $("#idx").val()
							},
							function(addData) {
								$("#deviceList").append(addData);
							}
						);
					} else alert("3개 이상 등록 공간을 추가할 수 없습니다.");
				},
				"json"
			);
		}
	});

	// 등록된 디바이스 정보 비우기 버튼
	$("#deviceList").on("click", ".resetBtn", function() {
		var position = $(this).closest(".addDeviceInfo");
		var dIdx = $(this)
			.closest(".addDeviceInfo")
			.find(".dIdx")
			.val();

		if (confirm("등록된 디바이스 정보를 비우시겠습니까?")) {
			$.post(
				"/page/add/add_deviceInfo.php",
				{
					actionType: "updateEmptyDeviceInfo",
					dIdx: dIdx
				},
				function(addData) {
					position.before(addData);
					position.remove();
				}
			);
		}
	});

	// 등록공간 삭제 버튼
	$("#deviceList").on("click", ".spaceDelBtn", function() {
		var position = $(this).closest(".addDeviceInfo");
		var dIdx = $(this)
			.closest(".addDeviceInfo")
			.find(".dIdx")
			.val();

		if (confirm("등록공간을 삭제하시겠습니까?")) {
			$.post(
				"/page/ajax/a_userInfo.php",
				{
					actionType: "checkCntDevice",
					idx: $("#idx").val()
				},
				function(cnt) {
					if (cnt > 1) {
						$.post(
							"/page/ajax/a_userInfo.php",
							{
								actionType: "deleteDeviceInfo",
								idx: $("#idx").val(),
								dIdx: dIdx
							},
							function(result) {
								if (result) {
									position.remove();
								} else alert("등록공간 삭제를 실패했습니다.");
							},
							"json"
						);
					} else alert("최소 1개의 등록공간은 있어야합니다.");
				},
				"json"
			);
		}
	});

	$("#userTag").tagsinput({
		trimValue: true
	});
});
