$(document).ready(function() {
	$('select').select2({ minimumResultsForSearch: -1 });

	$('#cq_tag').tagsinput({ trimValue: true });

	$('#btn_submit').on('click', function(e) {
		if (!$('textarea[name=cq_question]').val().length) {
			$('textarea[name=cq_question]').focus();
			alert('문제를 입력해주세요.');
			return;
		}
		if (!$('input[name=cq_item_1]').val().length) {
			$('input[name=cq_item_1]').focus();
			alert('1번 항목을 입력해주세요.');
			return;
		}
		if (!$('input[name=cq_item_2]').val().length) {
			$('input[name=cq_item_2]').focus();
			alert('2번 항목을 입력해주세요.');
			return;
		}
		if (!$('input[name=cq_item_3]').val().length) {
			$('input[name=cq_item_3]').focus();
			alert('3번 항목을 입력해주세요.');
			return;
		}
		if (!$('input[name=cq_item_4]').val().length) {
			$('input[name=cq_item_4]').focus();
			alert('4번 항목을 입력해주세요.');
			return;
		}

		$('#form').submit();
	});
});
