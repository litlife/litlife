export default function favorite_toggle_button(item, $url) {

	let type = item.data("type");
	let id = item.data("id");

	let wait_content = item.find("[data-status=wait]");
	let filled_content = item.find("[data-status=filled]");
	let empty_content = item.find("[data-status=empty]");
	let $count = item.find('.count');

	if ($url === undefined)
		$url = item.data("url");

	item.unbind('click').on('click', function (e) {

		item.removeClass("active").attr("disabled", "disabled");

		wait_content.show();
		filled_content.hide();
		empty_content.hide();

		$.ajax({
			method: "GET",
			url: $url,
			dataType: 'json'
		}).done(function (data) {

			console.log(data);

			wait_content.hide();

			item.removeAttr("disabled");

			if (data.result == 'attached') {

				empty_content.hide();
				filled_content.show();
			} else {
				empty_content.show();
				filled_content.hide();

			}

			$count.text(data.count);

		}).fail(function () {
			wait_content.hide();
			item.removeAttr("disabled");
			//parent.removeClass("active");
			empty_content.show();
		});
	});
}