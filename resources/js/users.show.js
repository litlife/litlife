import item from "./components/blog/item";

$('.avatar').click(function () {

	let url = $(this).data('fullsize-photo-url');

	let modal = $($(this).data('target'));

	modal.find('img').attr('src', url);
	modal.modal('show');
});


$('.top-blogs').find(".item:first").siblings('.item').addBack().each(function () {
	item($(this));
});

let list = $(".blogs");

if (list.length) {

	on_list_ready();

	add_event();
}

function add_event() {

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
}

function get_url() {
	return list.find(".pagination [rel=next]").first().attr("href");
}

function on_list_ready() {
	window.paginationScrollToActive();

	list.find(".item:first").siblings('.item').addBack().each(function () {
		item($(this));
	});
}