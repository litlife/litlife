const container = $('#faq').first();

container.find('a[href]')
	.filter(function () {
		return this.href.match(/#(.+)/);
	}).on('click', function (event) {

	const $href = $(this).attr('href');

	const $matches = $href.match(/#(.+)/);

	if ($matches[1] !== undefined) {
		const $anchor = $('#' + $matches[1] + '').first();

		if ($anchor.length > 0) {
			$(window).scrollTo($anchor);
		}
	}
});

const $errors = container.find('.errors').first();

if ($errors.length) {
	$(window).scrollTo($errors);
}