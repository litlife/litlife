export default function Sidebar() {

	let self = this;

	this.init = function () {

		if (self.sidebar.length) {
			$('.badge-fire-if-inner-badge-primary-exists').each(function () {
				let collapsed = $(this);
				let counter = collapsed.find('.badge-primary').first();

				let collapse = self.sidebar.find(collapsed.attr('href') + '.collapse');

				let count = 0;

				collapse.find('.list-group-item .badge').each(function (i) {
					let $badge = $(this);

					if ($badge.hasClass('badge-primary') || $badge.hasClass('badge-info')) {
						count = count + parseInt($(this).text(), 10);
					}
				});

				if (count > 0) {
					counter.html(count);
				}
			});
		}

		self.button.unbind('click').bind('click', function () {

			if (self.isVisible()) {

				self.hide();

				$.ajax({
					method: "GET",
					url: '/sidebar/hide'
				}).done(function (msg) {

				});

			} else {

				self.show();

				$.ajax({
					method: "GET",
					url: '/sidebar/show'
				}).done(function (msg) {

				});

			}
		});

		let $login_form = self.sidebar.find('#login_form');
		let $submit_button = $login_form.find('[type="submit"]');

		$submit_button.bind('click', function () {

			let $loading = $submit_button.find('.loading');
			let $enter_text = $submit_button.find('.enter_text');

			$submit_button.attr('disabled', 'disabled');

			$loading.show();
			$enter_text.hide();

			$login_form.submit();
		});
	};

	this.show = function () {
		console.log('show sidebar');

		self.sidebar.addClass('d-sm-block').removeClass('d-none');
		self.main.addClass('pl-260px');
		self.footer.addClass('pl-260px');
	};

	this.hide = function () {
		console.log('hide sidebar');

		self.sidebar.removeClass('d-sm-block').addClass('d-none');
		self.main.removeClass('pl-260px');
		self.footer.removeClass('pl-260px');
	};

	this.isVisible = function () {
		return self.sidebar.is(':visible');
	}
}