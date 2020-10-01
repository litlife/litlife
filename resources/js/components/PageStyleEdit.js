import Sidebar from "./sidebar";

require("bootstrap-colorpicker/dist/js/bootstrap-colorpicker");

export default function PageStyleEdit() {

	var self = this;

	self.options = {
		format: 'hex',
		inline: false,
		container: true,
		customClass: 'colorpicker-2x',
		sliders: {
			saturation: {
				maxLeft: 200,
				maxTop: 200
			},
			hue: {
				maxTop: 200
			},
			alpha: {
				maxTop: 200
			}
		}
	};

	this.init = function () {

		self.sidebar = new Sidebar();
		self.sidebar.sidebar = $('#sidebar');
		self.sidebar.main = $('#main');
		self.sidebar.footer = $('#footer');
		self.sidebar.button = $("[data-target='#sidebar']");

		self.book_text = $('.book_text');
		self.card = $('.card');
		self.body = $('body');

		self.background_color_colorpicker = self.form.find('#cp1').colorpicker(self.options);
		self.font_color_colorpicker = self.form.find('#cp2').colorpicker(self.options);
		self.card_color_colorpicker = self.form.find('#cp3').colorpicker(self.options);

		self.form = $('form.read_style').first();

		self.cssToValues();

		self.resetButton.unbind('click').on('click', self.onResetClick);

		self.form.submit(self.onFormSubmit);

		self.form.find('[name=font]').change(function () {
			self.valuesToCss();
		});

		self.form.find('[name=align]').change(function () {
			self.valuesToCss();
		});

		self.form.find('[name=size]').change(function () {
			self.valuesToCss();
		});

		self.form.find('[name=background_color]').change(function () {
			self.valuesToCss();
		});

		self.form.find('[name=card_color]').change(function () {
			self.valuesToCss();
		});

		self.form.find('[name=font_color]').change(function () {
			self.valuesToCss();
		});

		self.form.find('[name=show_sidebar]').change(function () {

			if ($(this).prop('checked'))
				$(this).prop('checked', true);
			else
				$(this).prop('checked', false);

			self.valuesToCss();
		});
	};

	this.onFormSubmit = function (event) {
		if (self.book_text.length > 0) {

			event.preventDefault();

			$.ajax({
				method: "POST",
				url: self.form.attr('action'),
				data: self.form.serializeArray()
			}).done(self.onLoadDone);

		}
	};

	this.onLoadDone = function (html) {
		self.form.find('.output').html(html);
	};

	this.onResetClick = function () {

		console.log('onResetClick');

		self.form.find('[name=font]').val($('#font').data('default'));
		self.form.find('[name=align]').val($('#align').data('default'));
		self.form.find('[name=size]').val($('#size').data('default'));
		self.form.find('[name=background_color]').val($('#background_color').data('default'));
		self.form.find('[name=card_color]').val($('#card_color').data('default'));
		self.form.find('[name=font_color]').val($('#font_color').data('default'));

		const $show_sidebar_checkbox = self.form.find('[name=show_sidebar][type="checkbox"]').first();

		if ($show_sidebar_checkbox.data('default')) {
			$show_sidebar_checkbox.prop('checked', true);
		} else
			$show_sidebar_checkbox.prop('checked', false);

		self.valuesToCss();

		self.background_color_colorpicker.colorpicker('setValue', $('#background_color').data('default'));
		self.card_color_colorpicker.colorpicker('setValue', $('#card_color').data('default'));
		self.font_color_colorpicker.colorpicker('setValue', $('#font_color').data('default'));

		self.sidebarToggle();
	};

	this.valuesToCss = function () {
		if (self.book_text.length > 0) {

			let font = self.form.find('[name=font]').val();

			if (font === 'Default') {
				self.book_text.css('font-family', 'inherit');
			} else {
				self.book_text.css('font-family', font);
			}

			self.book_text.css('text-align', self.form.find('[name=align]').val());
			self.book_text.css('font-size', self.form.find('[name=size]').val() + 'px');
			self.body.css('background-color', self.form.find('[name=background_color]').val());
			self.card.css('background-color', self.form.find('[name=card_color]').val());
			self.book_text.css('color', self.form.find('[name=font_color]').val());

			self.sidebarToggle();
		}
	};

	this.cssToValues = function () {
		if (self.book_text.length > 0) {
			self.form.find('[name=font]').val($.trim(self.book_text.css('font-family')).replace(/([\"\']*)/g, ''));
			self.form.find('[name=align]').val(self.book_text.css('text-align'));
			self.form.find('[name=size]').val(parseInt(self.book_text.css('font-size')));

			self.background_color_colorpicker.colorpicker('setValue', self.body.css('background-color'));
			self.card_color_colorpicker.colorpicker('setValue', self.card.css('background-color'));
			self.font_color_colorpicker.colorpicker('setValue', self.book_text.css('color'));

			self.sidebarToggle();
		}
	};

	this.sidebarToggle = function () {

		console.log('sidebarToggle');

		if (self.form.find('[name=show_sidebar][type="checkbox"]').prop('checked'))
			self.sidebar.show();
		else
			self.sidebar.hide();
	}
}










