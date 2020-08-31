export default function AddScrollOffset() {

	let self = this;

	this.init = function () {

		self.header = $('header.navbar');

		let offset = 0;

		if (self.isHeaderSticky())
			offset = -self.getHeaderHeight();

		console.log('offset: ' + offset);

		offset = offset - 15;

		// устанавливаем глобальный offset из-за навигационной панели
		$.extend($.scrollTo.defaults, {
			offset: {top: offset},
			duration: 100,
			axis: 'y'
		});
	};

	this.isHeaderSticky = function () {
		if (self.header.css('position') === 'sticky')
			return true;

		if (self.header.css('position') === 'fixed')
			return true;

		return false;
	};

	this.getHeaderHeight = function () {
		return self.header.height();
	};
}