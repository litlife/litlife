export default function ShowMoreCollapse() {
	let self = this;

	this.init = function () {
		self.collapsed_elements.on('show.bs.collapse', function (event) {

			var button = $('[href="#' + $(this).attr('id') + '"]');

			if (button.hasClass('hide_on_collapse'))
				button.hide();
		});
	}
}