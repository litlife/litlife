import item from "./components/message/item";


var list = $(".messages");

on_list_ready();

list.on('click', '.pagination a', function (e) {
	e.preventDefault();

	list.addClass("loading-cap");

	let url = $(this).attr('href');

	window.history.pushState("", "", url);

	$.ajax({
		url: url, data: {'ajax': true}
	}).done(function (data) {
		list.removeClass("loading-cap");
		list.html(data);

		on_list_ready();

		$('html, body').animate({
			scrollTop: list.offset().top - 80
		}, 100);

	}).fail(function () {
		if (jqXHR.status == 401) location.reload();
	});
});

function on_list_ready() {
	window.paginationScrollToActive();

	list.find(".item").each(function () {
		item($(this));
	});
}