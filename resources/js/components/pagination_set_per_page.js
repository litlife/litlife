var Url = require('url-parse');

export default function pagination_set_per_page() {
	$('.pagination').each(function () {
		let $pagination = $(this);
		let $btn_set_current_page = $pagination.find('button.set-current-page');
		let $input_current_page = $pagination.find('input.current-page');

		$btn_set_current_page.unbind('click').click(function () {

			let url = new Url($input_current_page.data('url'), true);

			url.query['page'] = $input_current_page.val();

			console.log(url);

			document.location = url;
		});
	});
}