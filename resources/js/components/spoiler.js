$("body").on('click', '.bb_spoiler_title', function (e) {

	let title = $(this);
	let spoiler = title.parent('.bb_spoiler');
	let text = spoiler.find('.bb_spoiler_text');

	if (text.is(":visible"))
		text.hide();
	else
		text.show();
});