(function ($, window) {
	var adjustAnchor = function () {

		var $anchor = $(':target'),
			fixedElementHeight = 80;

		var $hash = window.location.hash;

		console.log($hash);

		if ($anchor.length < 1 && $hash != '') {

			var $matches = $hash.match(/#(.+)/);

			if ($matches[1] !== null) {
				$hash_name = $matches[1];
				console.log($hash_name);
				$anchor = $('#' + $hash_name + ', [name="' + $hash_name + '"]').first();
			}
		}

		console.log($anchor);

		if ($anchor.length > 0) {
			$(window).scrollTo($anchor, 100);
		}
	};

	$(window).on('hashchange load', function () {
		adjustAnchor();
	});

})(jQuery, window);