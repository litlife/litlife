(new UsersCreate().init());

function UsersCreate() {

	let self = this;

	this.init = function () {

		self.form = $('form');

		self.nickField = self.form.find('[name="nick"]');
		self.nickTextDanger = self.nickField.parent().find('.text-danger');
		self.nickTextSuccess = self.nickField.parent().find('.text-success');

		self.lastNickValue = self.getNickFieldValue();

		self.form.formChange({
			timeout: 500,
			onChange: function () {
				console.log('onChange');

				if (self.getNickFieldValue() !== self.lastNickValue) {
					self.resetMarks();
				}
			},
			onShow: function () {
				if (self.getNickFieldValue() !== self.lastNickValue) {
					self.lastNickValue = self.getNickFieldValue();
					self.onNickFieldChanged();
				}
			}
		});
	};

	this.getNickFieldValue = function () {
		return $.trim(self.nickField.val());
	};

	this.onNickFieldChanged = function () {

		console.log('onNickFieldChanged');

		if (self.getNickFieldValue().length >= self.nickField.attr('minlength')) {
			$.ajax({
				url: "/users/where/nick",
				data: {nick: self.getNickFieldValue()}
			}).done(function (users) {
				console.log(users);

				if (users.length > 0) {
					self.nickExists();
				} else {
					self.nickNotExists();
				}
			});
		}
	};

	this.nickExists = function () {
		console.log('nickExists');

		self.nickTextDanger.show();
		self.nickTextSuccess.hide();

		self.nickField.addClass('is-invalid');
		self.nickField.removeClass('is-valid');
	};

	this.nickNotExists = function () {
		console.log('nickNotExists');

		self.nickTextDanger.hide();
		self.nickTextSuccess.show();

		self.nickField.removeClass('is-invalid');
		self.nickField.addClass('is-valid');
	};

	this.resetMarks = function () {
		self.nickTextDanger.hide();
		self.nickTextSuccess.hide();

		self.nickField.removeClass('is-invalid');
		self.nickField.removeClass('is-valid');
	};
}