export default function item(i) {

	var id = parseInt(i.data('id'));

	i.onUpdate = function () {
	};

	chose();

	function chose() {
		var chose_button = i.find('.chose').first();

		var selected_books = localStorage.getObj("selected_books");

		if (selected_books == null)
			selected_books = [];

		if (selected_books.indexOf(id) > -1) {
			chose_button.addClass('active')
				.attr('aria-pressed', 'true');
		}

		chose_button.unbind("click").on('click', function () {

			event.preventDefault();

			var selected_books = localStorage.getObj("selected_books");

			if (chose_button.hasClass('active')) {
				chose_button.removeClass('active')
					.attr('aria-pressed', 'false');

				var index = selected_books.indexOf(id);

				if (index != -1) {
					selected_books.splice(index, 1);
				}

				localStorage.setObj("selected_books", $.unique(selected_books));

				i.onUpdate(selected_books);
			} else {
				chose_button.addClass('active')
					.attr('aria-pressed', 'true');

				selected_books.push(id);

				localStorage.setObj("selected_books", $.unique(selected_books));

				i.onUpdate(selected_books);
			}
		})
	}

	return i;
}