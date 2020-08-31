export default function bell_toggle_button() {

	let self = this;

	this.init = function (item, $url) {

		self.item = item;
		self.type = self.item.data("type");
		self.id = self.item.data("id");
		self.url = $url;

		self.wait_content = self.item.find("[data-status=wait]");
		self.filled_content = self.item.find("[data-status=filled]");
		self.empty_content = self.item.find("[data-status=empty]");
		self.count = self.item.find('.count');

		if (self.url === undefined)
			self.url = self.item.data("url");

		self.item.unbind('click')
			.on('click', self.onClick);
	};

	this.onClick = function () {

		console.log('onClick');

		self.item.removeClass("active").attr("disabled", "disabled");

		self.wait_content.show();
		self.filled_content.hide();
		self.empty_content.hide();

		$.ajax({
			method: "GET",
			url: self.url,
			dataType: 'json'
		})
			.done(self.onDone)
			.fail(self.onFail);
	};

	this.onDone = function (data) {

		console.log('onDone');

		self.wait_content.hide();
		self.item.removeAttr("disabled");

		if (data.status === 'subscribed') {
			self.filled_content.show();
			self.empty_content.hide();
		}

		if (data.status === 'unsubscribed') {
			self.filled_content.hide();
			self.empty_content.show();
		}
	};

	this.onFail = function (event) {

		console.log('onFail');

		self.wait_content.hide();
		self.item.removeAttr("disabled");
		self.empty_content.show();
	}
}