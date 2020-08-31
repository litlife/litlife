export default function item(i) {

	let id = i.data("id");

	// delete restore

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
			url: "/bookmark_folders/" + id
		}).done(function (msg) {

			console.log(msg);

			if (msg.deleted_at) {
				$btn_delete.hide();
				$btn_restore.show();
				i.find('.title').addClass('transparency');
			} else {
				$btn_delete.show();
				$btn_restore.hide();
				i.find('.title').removeClass('transparency');
			}

			$btn_restore.button('reset');
			$btn_delete.button('reset');
		});
	}

}