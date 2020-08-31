document.addEventListener('DOMContentLoaded', function () {

	function removeHash() {
		/*
		history.pushState("", document.title, window.location.pathname
			+ window.location.search);
		*/

		history.replaceState({}, document.title,
			location.href.substr(0, location.href.length - location.hash.length));
	}

	if (window.location.hash) {
		let hash = window.location.hash.substr(1);

		removeHash();
		//console.log(hash);
		let element = $('[id=' + hash + '],[name=' + hash + ']').first();
		//console.log(element);
		//alert ('before');
		$(window).scrollTo(element);
	}
});

$("[href^='#']").not("[data-toggle=collapse]").click(function (e) {
	e.preventDefault();

	var hash = $(this).attr('href').substr(1);

	if (!hash)
		var element = $('body').first();
	else
		var element = $('[id=' + hash + '],[name=' + hash + ']').first();

	$(window).scrollTo(element);
});