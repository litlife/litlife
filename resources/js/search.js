import collection from "./components/collection/item";

let search = new Search();
search.form = $('#outter_form');
search.init();

function Search() {

	let self = this;

	this.init = function () {

		self.form.submit(function (e) {
			self.submitForm();

			return false;
		});

		self.dialog = $('#search');
		self.inner_form = self.dialog.find('form').first();
		self.inner_form_input = self.inner_form.find('input[type="text"]').first();
		self.title_query = self.dialog.find('.title_query').first();
		self.form_input = self.form.find('input[type="text"]').first();
		self.result = self.dialog.find('.result');

		self.inner_form.formChange({
			timeout: 500,
			onShow: function () {
				self.submitInnerForm();
			}
		});

		self.inner_form.submit(function (e) {
			self.submitInnerForm();
			return false;
		});
	};

	this.submitForm = function () {

		let query = $.trim(self.form_input.val());

		if (query) {
			self.dialog.modal('show');

			self.result.addClass("loading-cap");

			self.setQueryString(query);

			self.form.ajaxSubmit({
				success: self.onSubmitSuccess
			});
		}
	};

	this.submitInnerForm = function () {

		let query = $.trim(self.inner_form_input.val());

		if (query) {

			self.dialog.modal('show');

			self.result.addClass("loading-cap");

			self.setQueryString(query);

			let dom_form = self.inner_form.get(0);

			console.log(dom_form.checkValidity());

			if (dom_form.checkValidity()) {
				self.inner_form.ajaxSubmit({
					success: self.onSubmitSuccess
				});
			} else {
				dom_form.reportValidity();
			}
		}
	};

	this.setQueryString = function (str) {
		self.inner_form_input.val(str);
		self.form_input.val(str);
		self.title_query.text(str);
	};

	this.onSubmitSuccess = function (html, statusText, xhr, $form) {

		self.result.removeClass("loading-cap");

		self.result.html(html);

		$('#collections').each(function () {
			let object = new collection();
			object.init($(this));
		});
	};
}