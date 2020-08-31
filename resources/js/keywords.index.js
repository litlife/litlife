$('[data-item=keyword]').each(function () {


	let $i = $(this);
	let $text = $i.find('[data-text]');
	let $btn_delete = $i.find('.delete');
	let $btn_restore = $i.find('.restore');

	$btn_delete.unbind("click").on('click', function (event) {
		event.preventDefault();
		deleteOrRestore($(this));
	});

	$btn_restore.unbind("click").on('click', function (event) {
		event.preventDefault();
		deleteOrRestore($(this));
	});

	function deleteOrRestore(btn) {
		btn.button('loading');

		btn.hide();

		$.ajax({
			method: "DELETE",
			url: btn.attr('href')
		}).done(function (msg) {

			if (msg.deleted_at) {
				$btn_delete.hide();
				$btn_restore.show();
				$text.addClass('transparency');
			} else {
				$btn_delete.show();
				$btn_restore.hide();
				$text.removeClass('transparency');
			}

			$btn_restore.button('reset');
			$btn_delete.button('reset');
		});
	}

});