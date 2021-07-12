$("body")
	.find('.bb_spoiler')
	.each(function () {

		const spoiler = $(this);

		spoiler
			.unbind('click')
			.bind('click', function () {
				const title = $('.bb_spoiler_title').first();
				const text = spoiler.find('.bb_spoiler_text').first();

				if (text.is(":visible"))
					text.hide();
				else
					text.show();
			})
	});
