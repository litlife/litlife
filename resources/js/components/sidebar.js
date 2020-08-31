let sidebar = $('#sidebar');

if (sidebar.length) {
	$('.badge-fire-if-inner-badge-primary-exists').each(function () {
		let collapsed = $(this);
		let counter = collapsed.find('.badge-primary').first();

		let collapse = sidebar.find(collapsed.attr('href') + '.collapse');

		let count = 0;

		collapse.find('.list-group-item .badge-primary').each(function (i) {
			count = count + parseInt($(this).text(), 10);
		});

		if (count > 0) {
			counter.html(count);
		}
	});
}