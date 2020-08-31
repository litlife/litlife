export default function Favorite() {

	let self = this;

	this.init = function (parent, url) {

		self.parent = parent;
		self.url = url;
		self.type = self.parent.data("type");
		self.id = self.parent.data("id");

		self.loading_content = self.parent.find("[data-status=loading]");
		self.exists_content = self.parent.find("[data-status=exists]");
		self.not_exists_content = self.parent.find("[data-status=not_exists]");
		self.count = self.parent.find('.count');

		self.parent.on('click', self.onClick);
	};

	this.onClick = function (e) {

		self.parent.removeClass("active").attr("disabled", "disabled");

		if (self.isPressed())
			self.decrement();
		else
			self.increment();

		self.loading_content.show();
		self.exists_content.hide();
		self.not_exists_content.hide();

		$.ajax({
			method: "GET",
			url: self.url,
			dataType: 'json'
		}).done(self.onDone)
			.fail(self.onFail);
	};

	this.onDone = function (data) {

		self.loading_content.hide();

		self.parent.removeAttr("disabled");

		if (data.result == 'attached') {
			self.exists_content.show();
			self.parent.attr('aria-pressed', 'true');
		} else {
			self.not_exists_content.show();
			self.parent.attr('aria-pressed', 'false');
		}

		self.setCount(data.added_to_favorites_count);
	};

	this.onFail = function () {

		self.loading_content.hide();
		self.parent.removeAttr("disabled");
		self.not_exists_content.show();
		self.setCount(0);
	};

	this.setCount = function (count) {

		count = parseInt(count);

		if (count > 0) {
			self.count.hide();
			self.count.text(count);
		} else {
			self.count.hide();
			self.count.text('');
		}
	};

	this.getCount = function () {
		return parseInt(self.count.text());
	}

	this.isPressed = function () {
		if (self.parent.attr('aria-pressed') === 'true')
			return true;

		return false;
	};

	this.increment = function () {
		console.log('increment');
		let count = self.getCount();
		self.setCount(count + 1);
	};

	this.decrement = function () {
		console.log('decrement');
		let count = self.getCount();
		self.setCount(count - 1);
	}
}