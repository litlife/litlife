import item from "./components/post/item";
import bell_toggle_button from "./components/bell_toglle_button";

var list = $(".list");

add_event();

on_list_ready();

function add_event() {
	/*
		let id = Math.random();

		console.log(id);
		window.history.pushState({id: id, html: list.html()}, "", document.location);
	*/
	list.on('click', '.pagination a', function (e) {
		e.preventDefault();

		list.addClass("loading-cap");

		let url = $(this).attr('href');

		window.history.pushState({}, "", url);

		$.ajax({
			url: url, data: {'ajax': true}
		}).done(function (data) {
			list.removeClass("loading-cap");
			list.html(data);

			on_list_ready();

			$('html, body').animate({
				scrollTop: list.offset().top - 80
			}, 100);

			//window.history.pushState({id: id, html: data}, "", url);

		}).fail(function () {
			if (jqXHR.status == 401) location.reload();
		});
	});

	/*
		window.onpopstate = function (event) {
			console.log("location: " + document.location + " ");
			console.log(event.state);

			if (id !== undefined && id !== null && event.state !== undefined
				&& event.state !== null && event.state.id == id) {
				event.preventDefault();
				list.html(event.state.html);

				on_list_ready();
			} else {
				window.location.href = document.location;
			}
		};
		*/
}

function on_list_ready() {

	//console.log('on_list_ready');

	window.paginationScrollToActive();

	list.find(".item:first").siblings('.item').addBack().each(function () {
		item($(this));
	});
}

$('.fixed_post').find(".item:first").siblings('.item').addBack().each(function () {
	item($(this));
});

$('.move').click(function () {

	var ids = [];

	$('.item').each(function () {

		var item = $(this);

		var id = item.data('id');

		if (item.find('.select:checked').length) {
			ids.push(id);
		}
	});

	if (ids.length > 0)
		window.location = '/posts/move?ids=' + ids.join();
});

let object = new bell_toggle_button;
object.init($('.btn-bell-toggle').first());

/*
let $btn_subscribe = $('.subscribe');
let $btn_unsubscribe = $('.unsubscribe');

$btn_subscribe.on('click', subscribe_toggle);
$btn_unsubscribe.on('click', subscribe_toggle);

function subscribe_toggle(event)
{
    event.preventDefault();

    $btn_subscribe.addClass('disabled');
    $btn_unsubscribe.addClass('disabled');

    let href = '';

    if ($btn_subscribe.is(":visible"))
        href = $btn_subscribe.attr('href');
    else if ($btn_unsubscribe.is(":visible"))
        href = $btn_unsubscribe.attr('href');

    $.ajax({
        method: "GET",
        url: href,
        dataType: 'json'
    }).done(function (data) {

        if (data.result == 'subscribed') {

            $btn_unsubscribe.show();
            $btn_subscribe.hide();

            $btn_subscribe.removeClass('disabled');
            $btn_unsubscribe.removeClass('disabled');

        } else if (data.result == 'unsubscribed') {

            $btn_unsubscribe.hide();
            $btn_subscribe.show();

            $btn_subscribe.removeClass('disabled');
            $btn_unsubscribe.removeClass('disabled');
        }

    }).fail(function () {
        $btn_subscribe.removeClass('disabled');
        $btn_unsubscribe.removeClass('disabled');
    });
}
*/