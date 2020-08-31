import queryString from "query-string";

export default function pagination_set_current_page() {
	$('.pagination').each(function () {
		let $pagination = $(this);
		let $btn_set_per_page = $pagination.find('button.set-per-page');
		let $input_per_page = $pagination.find('input.per-page');

		$btn_set_per_page.unbind('click').click(function () {

			var parsed = queryString.parse(location.search.substring(1));

			//console.log(parsed);
			//console.log(location.search.substring(1))

			parsed['per_page'] = $input_per_page.val();

			location.search = $.param(parsed); // Causes page to reload
		});
	});
}

