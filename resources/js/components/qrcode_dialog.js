var dialog = $('#QRCodeDialog');

dialog.on('shown.bs.modal', function () {

	let body = dialog.find('.modal-body');
	let url = $(location).attr('href') || window.location.href;

	$.ajax({
		url: dialog.data('url'),
		data: {size: "200", str: url}
	}).done(function (data) {
		body.html(data);
	}).fail(function (response) {
		body.html('Error ' + response.status + ': ' + response.responseJSON.message);
	});
});