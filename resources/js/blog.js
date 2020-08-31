import item from "./components/blog/item";


let list = $(".blogs");

console.log(list);

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

function on_list_ready() {
	list.find(".item:first").siblings('.item').addBack().each(function () {
		console.log($(this));
		item($(this));
	});
}




