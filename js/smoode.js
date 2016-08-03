
	$(document).ready(function(){
		$("#right").fadeIn(400);
	});
	$('a:not([href="#"])').click(function() {
			$("#right").fadeOut(400);
			var source = $(this).attr("href");
			setTimeout(function() {
			  window.location.href = source;
			}, 500);
			event.preventDefault();
			return false;
	});