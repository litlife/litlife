var Url = require('url-parse');

let $input = $('.url-maker');
let defaultValue = $input.val();

$input.unbind('change').on('change paste keyup', function () {

	setTimeout(function () {

		var value = $.trim($input.val());

		if (value === '') {
			$input.val(defaultValue);
		} else {
			var url = new Url(value, true);

			console.log(url);

			url.protocol = 'https';
			url.host = $input.data('host');

			let $ref_name = $input.data('ref-name');

			url.query[$ref_name] = $input.data('ref-id');

			$input.val(url);
		}

	}, 100);
});