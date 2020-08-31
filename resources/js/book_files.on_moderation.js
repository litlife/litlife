$('.file').each(function () {

	let i = $(this);
	let id = i.data('id');
	let book_id = i.data('book-id');
	let title = i.find('.title');

	let $btn_delete = i.find('.delete');

	$btn_delete.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});

	let $btn_restore = i.find('.restore');

	$btn_restore.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});

	function deleteOrRestore(btn) {
		btn.button('loading');

		btn.hide();

		$.ajax({
			method: "DELETE",
			url: "/books/" + book_id + "/files/" + id
		}).done(function (msg) {
			//console.log(msg);
			if (msg.deleted_at) {
				$btn_delete.hide();
				$btn_restore.show();
				title.addClass('transparency');
			} else {
				$btn_delete.show();
				$btn_restore.hide();
				title.removeClass('transparency');
			}

			$btn_restore.button('reset');
			$btn_delete.button('reset');
		});
	}
});