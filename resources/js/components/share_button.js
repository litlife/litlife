export default function shareButton() {

	let self = this;

	this.init = function (button) {

		self.button = button;
		self.title = button.data('title');
		self.description = button.data('description');
		self.url = button.data('url');
		self.image = button.data('image');

		self.dialog = bootbox.dialog({
			message: '<div class="spinner" style="text-align: center; font-size:48px;"><i class="fas fa-spinner fa-spin"></i></div>',
			closeButton: true,
			backdrop: true,
			show: false,
			size: "small"
		});

		self.button.unbind('click').on('click', function () {
			console.log('click');
			self.dialog.modal('show');
		});

		self.dialog.on('shown.bs.modal', function (event) {
			self.onDialogOpen(event);
		});
	};

	this.onDialogOpen = function (event) {

		console.log('onDialogOpen');

		var html = '<div data-direction="vertical" data-size="m" class="ya-share2" data-services="' +
			'collections,vkontakte,facebook,odnoklassniki,';

		if (typeof self.image !== 'undefined')
			html += 'pinterest,';

		html += 'twitter,telegram,whatsapp,viber,moimir,blogger,delicious,digg,reddit,evernote,linkedin,' +
			'lj,pocket,qzone,renren,sinaWeibo,surfingbird,tencentWeibo,tumblr,skype' +
			'" data-counter="true" ' +
			'data-title="' + self.title + '" ' +
			'data-description="' + self.description + '" ';

		if (typeof self.image !== 'undefined')
			html += 'data-image="' + self.image + '" ';

		html += 'data-url="' + self.url + '">';

		var button = $(event.relatedTarget);
		var modal = self.dialog;

		console.log(html);

		modal.find('.bootbox-body').html(html);

		$.getScript("//cdn.jsdelivr.net/npm/yandex-share2/share.js", function (data, textStatus, jqxhr) {
			console.log(data); // Data returned
			console.log(textStatus); // Success
			console.log(jqxhr.status); // 200
			console.log("Load was performed.");
		});
	};
}

