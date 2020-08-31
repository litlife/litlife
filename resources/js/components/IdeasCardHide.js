export default function IdeasCardHide() {

	let self = this;

	this.init = function (card) {

		let $btn_close = card.find('.card-close');

		$btn_close.unbind('click').on('click', function (e) {

			card.hide();

			$.ajax({
				method: "GET",
				url: '/ideas/card/hide',
				dataType: 'json'
			}).done(function (data) {


			}).fail(function () {

			});
		});
	};
}