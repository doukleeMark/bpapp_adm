/* common.js */
$(document).ready(function() {
	$(".icon-custom-left").on("click", function() {
		window.history.back();
	});
	$(".btn_logout").click(function() {
		location.href = "/?page=logout";
	});
});

// 숫자 자리수에 맞게 0표시. ex) 01
function pad(n, width) {
	n = n + "";
	return n.length >= width ? n : new Array(width - n.length + 1).join("0") + n;
}

// 초를 시:분:초로 표시.
function secondToTime(seconds) {
	const hour = parseInt(seconds / 3600);
	const min = parseInt((seconds % 3600) / 60);
	const sec = seconds % 60;
	return pad(hour, 2) + ":" + pad(min, 2) + ":" + pad(sec, 2);
}
