import idea from './components/idea/item';

new IdeasIndex().init();

function IdeasIndex() {

	let self = this;

	this.init = function () {

		self.ideas = $('.ideas').first();

		self.ideas.find('.idea').each(function () {
			self.idea($(this));
		});

		self.idea_create_form = $('#idea_create_form');

		self.idea_name_input = self.idea_create_form.find('input[name="name"]').first();

		self.idea_create_form.formChange({
			timeout: 500,
			onShow: function () {
				self.nameInputChange();
			}
		});

		self.submit_button = self.idea_create_form.find('[type="submit"]').first();

		self.submit_button.on('click', function () {
			self.submit_button.addClass('loading-cap');
		});
	};

	this.nameInputChange = function () {

		let name = $.trim(self.idea_name_input.val());

		if (name === '')
			return false;

		self.ideas.addClass('loading-cap');

		$.ajax({
			method: "GET",
			url: '/ideas/search',
			data: {'name': name}
		}).done(function (data) {

			self.ideas.html(data);

			self.ideas.find('.idea').each(function () {
				self.idea($(this));
			});

			self.ideas.removeClass('loading-cap');

		}).fail(function () {
			self.ideas.removeClass('loading-cap');
		});
	};

	this.idea = function (item) {
		let cls = new idea;
		cls.init(item);
	}
}